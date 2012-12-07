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
   
   private static $_dao = array();
   private $_currentDao;
   private $_type;
   private $_sqlParts = array(
       'select' => array(),
       'from' => array(),
       'join' => array(),
       'set' => array(),
       'where' => null,
       'groupBy' => array(),
       'having' => null,
       'orderBy' => array()
   );
   private $_values;
   
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

   public function where(array $conditions) {
      if(empty($conditions)) {
         throw new InvalidArgumentException(__('Invalid conditions: not-empty array required.'));
      }
      foreach($conditions as $condition) {
         
      }
      return $this;
   }
   
   public function andWhere(array $conditions) {
      if(empty($conditions)) {
         throw new InvalidArgumentException(__('Invalid conditions: not-empty array required.'));
      }
      foreach($conditions as $condition) {
         
      }
      return $this;
   }
   
   public function orWhere(array $conditions) {
      if(empty($conditions)) {
         throw new InvalidArgumentException(__('Invalid conditions: not-empty array required.'));
      }
      foreach($conditions as $condition) {
         
      }
      return $this;
   }

   public function fields() {
      return $this->_fields;
   }

   public function conditions() {
      return $this->_conditions;
   }

   public function datasources() {
      return $this->_datasources;
   }

   public function type() {
      return $this->_type;
   }

   public function customQuery($sql, $tokens) {
      self::$_dao[$this->_currentDao]->query($sql, $tokens);
   }
   
   private function _buildToken($field) {
      $field = explode('.', $field);
      $token = isset($field[1]) ? $field[1] : $field[0];
      $proposedToken = $token;
      
      for($i = 0 ; array_key_exists($proposedToken, $this->_values) ; ++$i) {
         $proposedToken = $token . '_' . $i;
      }
      
      return $proposedToken;
   }
   
   public function getArray() {
      
   }
   
   public function getObject() {
      
   }
   
   public function getResult() {
      
   }
}