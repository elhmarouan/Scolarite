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

   const SELECT = 1;
   const UPDATE = 2;
   const DELETE = 3;
   
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
         throw new InvalidArgumentException(__('Unknown dao name. Please check the configuration file.'));
      }
      if (!array_key_exists($daoName, self::$_dao)) {
         $driver = ucfirst($daoDriverConfig['driver']) . 'Driver';
         PandaApplication::load('Panda.model.db.driver.' . $driver);
         self::$_dao[$daoName] = new $driver($daoDriverConfig);
      }
      $this->_currentDao = $daoName;
   }
   
   public function reset() {
      $defaultAttributes = get_class_vars($this);
      foreach ($defaultAttributes as $attribute => $defaultValue) {
         if(!in_array($attribute, array('_dao', '_currentDao'))) { 
            $this->$attribute = $defaultValue;
         }
      }
   }

   public function select($fields = null) {
      $this->_type = self::SELECT;
      if (empty($fields)) {
         return $this;
      }
      $this->_sqlParts['select'] = is_array($fields) ? $fields : func_get_args();
      return $this;
   }
   
   public function set($key, $value) {
      if ($this->_type !== self::UPDATE) {
         throw new ErrorException(__('Unable to use the "set" method on this query: update query required.'));
      }
      if (!is_string($key) ||  empty($key)) {
         throw new InvalidArgumentException(__('Invalid key for the set query: not-empty string required.'));
      }
      if (!in_array($key, $this->_sqlParts['set'])) {
         $token = $this->_buildToken($key);
         $this->_sqlParts['set'][] = array($key => $token);
         $this->_values[$token] = $value;
      }
   }

   public function update($datasource = '') {
      $this->_type = self::UPDATE;
      if (!empty($datasource)) {
         $this->from($datasource);
      }
      return $this;
   }

   public function delete($datasource = '') {
      $this->_type = self::DELETE;
      if (!empty($datasource)) {
         $this->from($datasource);
      }
      return $this;
   }

   public function from($datasource) {
      if (!is_string($datasource) || empty($datasource)) {
         throw new InvalidArgumentException(__('Invalid datasource name: expected not-empty string.'));
      }
      if (!in_array($datasource, $this->_sqlParts['from'])) {
         $this->_sqlParts['from'][] = $datasource;
      }
      return $this;
   }

   public function where(array $conditions, $type = 'AND', $groupType = 'AND') {
      if (empty($conditions)) {
         throw new InvalidArgumentException(__('Invalid conditions: not-empty array required.'));
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

   public function datasources() {
      return $this->_sqlParts['from'];
   }

   public function type() {
      return $this->_type;
   }
   
   public function tokensValues() {
      return $this->_values;
   }

   public function customQuery($sql, $tokens) {
      self::$_dao[$this->_currentDao]->query($sql, $tokens);
   }
   
   private function _buildToken($field) {
      $field = explode('.', $field);
      $token = isset($field[1]) ? $field[1] : $field[0];
      $proposedToken = $token . '_0';
      
      for($i = 0 ; array_key_exists($proposedToken, $this->_values) ; ++$i) {
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
   
   public function getFirst($outputFormat = self::OUTPUT_ARRAY) {
      if(!empty($this->_type)) {
         if($this->_type === self::SELECT) {
            $this->limit(1);
            return $this->getResult($outputFormat);
         } else {
            throw new ErrorException(__('Unable to execute the query: please only use select statements with the getFirst method.'));
         }
      } else {
         throw new ErrorException(__('Unable to execute the query: please use "select" method first.'));
      }
   }
   
   public function getResult($outputFormat = self::OUTPUT_ARRAY) {
      if (!empty($this->_type)) {
         if(!empty($this->_sqlParts['from'])) {
            switch ($this->_type) {
               case self::SELECT:
                  return self::$_dao[$this->_currentDao]->select($this, $outputFormat);
                  break;
               case self::UPDATE:
                  return self::$_dao[$this->_currentDao]->update($this);
                  break;
               case self::DELETE:
                  return self::$_dao[$this->_currentDao]->delete($this);
                  break;
            }
         } else {
            throw new ErrorException(__('Unable to execute the query: please specify at least one datasource to work on.'));
         }
      } else {
         throw new ErrorException(__('Unable to execute the query: please use "select", "update" or "delete" methods first.'));
      }
   }
}