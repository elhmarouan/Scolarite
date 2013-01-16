<?php

/**
 * Query
 * 
 * A simple query builder
 * 
 * @author Stanislas Michalak <stanislas.michalak@gmail.com>
 * @package Panda.model.db
 * 
 */
class Query {

   //Query types
   const SELECT_QUERY = 1;
   const COUNT_QUERY = 2;
   const INSERT_QUERY = 3;
   const UPDATE_QUERY = 4;
   const DELETE_QUERY = 5;
   
   //Output types
   const OUTPUT_ARRAY = 1;
   const OUTPUT_OBJECT = 2;

   private static $_dao = array();
   private $_currentDao;
   private $_type;
   private $_sqlParts = array(
       'select' => array(),
       'from' => array(),
       'join' => array(),
       'set' => array(),
       'where' => array(),
       'limit' => array(),
       'groupBy' => array(),
       'having' => null,
       'orderBy' => array()
   );
   private $_values = array();

   public function __construct($daoName) {
      if (!is_string($daoName) || empty($daoName)) {
         throw new InvalidArgumentException(__('Invalid dao name.'));
      }
      try {
         $daoDriverConfig = Config::read('datasources.list.' . $daoName);
      } catch (ErrorException $e) {
         if ($e->getCode() === Config::UNKNOWN_KEY) {
            throw new InvalidArgumentException(__('Unknown dao name. Please check the configuration file.'));
         } else {
            throw $e;
         }
      }
      if (!array_key_exists($daoName, self::$_dao)) {
         $driver = ucfirst($daoDriverConfig['driver']) . 'Driver';
         Application::load('Panda.model.db.driver.' . $driver);
         self::$_dao[$daoName] = new $driver($daoDriverConfig);
      }
      $this->_currentDao = $daoName;
   }

   private function _reset() {
      $defaultAttributes = get_class_vars(get_class($this));
      foreach ($defaultAttributes as $attribute => $defaultValue) {
         if (!in_array($attribute, array('_dao', '_currentDao'))) {
            $this->$attribute = $defaultValue;
         }
      }
   }

   /**
    * Set the current query type to SELECT and the fields to select if there is any.
    * @param array $fields
    * @param string $fields,...
    * @return Query
    */
   public function select($fields = null) {
      $this->_type = self::SELECT_QUERY;
      if (empty($fields)) {
         return $this;
      }
      $this->_sqlParts['select'] = is_array($fields) ? $fields : func_get_args();
      return $this;
   }

   /**
    * Set the current query type to COUNT.
    * @return Query
    */
   public function count() {
      $this->_type = self::COUNT_QUERY;
      return $this;
   }

   public function insert($datasource = '') {
      $this->_type = self::INSERT_QUERY;
      if (!empty($datasource)) {
         $this->from($datasource);
      }
      return $this;
   }

   public function update($datasource = '') {
      $this->_type = self::UPDATE_QUERY;
      if (!empty($datasource)) {
         $this->from($datasource);
      }
      return $this;
   }

   public function delete($datasource = '') {
      $this->_type = self::DELETE_QUERY;
      if (!empty($datasource)) {
         $this->from($datasource);
      }
      return $this;
   }

   public function set() {
      if ($this->_type !== self::UPDATE_QUERY && $this->_type !== self::INSERT_QUERY) {
         throw new ErrorException(__('Unable to use the "set" method on this query: update or insert query required.'));
      }
      if (func_num_args() === 1) {
         $keys = func_get_arg(0);
         if (is_array($keys) && count($keys) > 0) {
            foreach ($keys as $key => $value) {
               $token = $this->_buildToken($key);
               $this->_sqlParts['set'][] = array($key => $token);
               $this->_values[$token] = $value;
            }
         } else {
            throw new InvalidArgumentException(__('Unable to use the "set" method: wrong first parameter, not-empty array expected.'));
         }
      } else if (func_num_args() === 2) {
         $key = func_get_arg(0);
         $value = func_get_arg(1);
         if (is_string($key) && !empty($key)) {
            $token = $this->_buildToken($key);
            $this->_sqlParts['set'][] = array($key => $token);
            $this->_values[$token] = $value;
         } else {
            throw new InvalidArgumentException(__('Unable to use the "set" method: wrong first parameter, not-empty string expected.'));
         }
      } else {
         throw InvalidArgumentException(__('Unable to use the "set" method: too few or too many arguments'));
      }
      return $this;
   }

   public function from($datasource) {
      if (!is_string($datasource) || empty($datasource)) {
         throw new InvalidArgumentException(__('Invalid datasource name: expected not-empty string.'));
      }
      if (!in_array($datasource, $this->_sqlParts['from'])) {
         if ($this->_type !== self::INSERT_QUERY || count($this->_sqlParts['from']) === 0) {
            $this->_sqlParts['from'][] = $datasource;
         } else {
            throw new ErrorException(__('Unable to use more than one datasource with an insert query.'));
         }
      }
      return $this;
   }

   public function join(array $joinData, $type = 'left') {
      $this->_sqlParts['join'][] = array(
          'type' => $type,
          'data' => $joinData
      );
      return $this;
   }

   public function where(array $conditions, $type = 'AND', $groupType = 'AND') {
      if (empty($conditions)) {
         return $this;
      }
      if (($type !== 'AND') &&  ($type !== 'OR')) {
         throw new InvalidArgumentException(__('Invalid type: "OR" or "AND" required.'));
      }
      if (($groupType !== 'AND') &&  ($groupType !== 'OR')) {
         throw new InvalidArgumentException(__('Invalid group type: "OR" or "AND" required.'));
      }
      $where = array('type' => $type, 'groupType' => $groupType, 'conditions' => array());
      foreach ($conditions as $key => $value) {
         $operator = $this->_extractOperator($key);
         $token = $this->_buildToken($key);
         $where['conditions'][] = array('field' => $key, 'token' => $token, 'operator' => $operator);
         $this->_values[$token] = $value;
      }
      $this->_sqlParts['where'][] = $where;
      return $this;
   }

   public function andWhere(array $conditions, $type = 'AND') {
      return $this->where($conditions, $type, 'AND');
   }

   public function orWhere(array $conditions, $type = 'AND') {
      return $this->where($conditions, $type, 'OR');
   }

   public function limit($limit = 20, $offset = null) {
      if (is_int($limit) && $limit > 0) {
         $this->_sqlParts['limit'] = array($limit);
         if ($offset === null || is_int($offset) && $offset > 0) {
            if ($offset !== null) {
               $this->_sqlParts['limit'][] = $offset;
            }
         } else {
            throw new InvalidArgumentException(__('Invalid query offset "%s"', (string) $offset));
         }
      } else {
         throw new InvalidArgumentException(__('Invalid query limit "%s"', (string) $limit));
      }
      return $this;
   }

   public function fields() {
      return empty($this->_sqlParts['select']) ? '*' : $this->_sqlParts['select'];
   }

   public function conditions() {
      return $this->_sqlParts['where'];
   }

   public function limits() {
      return $this->_sqlParts['limit'];
   }

   public function joinList() {
      return $this->_sqlParts['join'];
   }

   public function datasources() {
      return $this->_sqlParts['from'];
   }

   public function type() {
      return $this->_type;
   }

   public function setValues() {
      return $this->_sqlParts['set'];
   }

   public function tokensValues() {
      return $this->_values;
   }

   public function customQuery($sql, array $tokens) {
      self::$_dao[$this->_currentDao]->query($sql, $tokens);
   }

   public function getPrimaryKeys($datasource) {
      return self::$_dao[$this->_currentDao]->primaryKeysOf($datasource);
   }

   private function _buildToken($field) {
      $field = explode('.', $field);
      $token = isset($field[1]) ? $field[1] : $field[0];
      $proposedToken = $token . '_0';

      for ($i = 0; array_key_exists($proposedToken, $this->_values); ++$i) {
         $proposedToken = $token . '_' . $i;
      }

      return $proposedToken;
   }

   private function _extractOperator(&$field) {
      $knownOperators = array(
          '>' => 'gt',
          '<' => 'lt',
          '>=' => 'gte',
          '<=' => 'lte',
          '=' => 'eq',
          '==' => 'eq',
          '!=' => 'neq',
          '<>' => 'neq'
      );
      $fieldComponents = explode(' ', $field);
      if (isset($fieldComponents[1])) {
         if (!array_key_exists($fieldComponents[1], $knownOperators)) {
            throw new InvalidArgumentException(__('Unknown operator "%s".', $fieldComponents[1]));
         }
         $field = $fieldComponents[0];
         return $knownOperators[$fieldComponents[1]];
      } else {
         return 'eq';
      }
   }

   public function getLastInsertId() {
      return self::$_dao[$this->_currentDao]->lastInsertId();
   }

   public function getResult($outputFormat = self::OUTPUT_ARRAY) {
      if (!empty($this->_type)) {
         if (!empty($this->_sqlParts['from'])) {
            if (Config::read('cache.enable')) {
               if ($this->_type === self::COUNT_QUERY) {
                  $cacheKey = sha1(var_export(get_object_vars($this), true));
                  if (file_exists(SHARE_DIR . 'cache/datasource/' . $this->_currentDao . '/' . $cacheKey . '.cache')) {
                     $this->_reset();
                     return (int) file_get_contents(SHARE_DIR . 'cache/datasource/' . $this->_currentDao . '/' . $cacheKey . '.cache');
                  }
               } else if ($this->_type !== self::SELECT_QUERY) {
                  foreach (glob(SHARE_DIR . 'cache/datasource/' . $this->_currentDao . '/*.cache') as $cacheFile) {
                     unlink($cacheFile);
                  }
               }
            }
            switch ($this->_type) {
               case self::SELECT_QUERY:
                  $result = self::$_dao[$this->_currentDao]->select($this, $outputFormat);
                  break;
               case self::COUNT_QUERY:
                  $result = self::$_dao[$this->_currentDao]->count($this);
                  if (Config::read('cache.enable')) {
                     file_put_contents(SHARE_DIR . 'cache/datasource/' . $this->_currentDao . '/' . $cacheKey . '.cache', $result);
                  }
                  break;
               case self::INSERT_QUERY:
                  $result = self::$_dao[$this->_currentDao]->insert($this);
                  break;
               case self::UPDATE_QUERY:
                  $result = self::$_dao[$this->_currentDao]->update($this);
                  break;
               case self::DELETE_QUERY:
                  $result = self::$_dao[$this->_currentDao]->delete($this);
                  break;
               default:
                  throw new ErrorException(__('Unable to get the result of the query: invalid query type.'));
                  break;
            }
            $this->_reset();
            return $result;
         } else {
            throw new ErrorException(__('Unable to execute the query: please specify at least one datasource to work on.'));
         }
      } else {
         throw new ErrorException(__('Unable to execute the query: please use "select", "update" or "delete" methods first.'));
      }
   }

}