<?php

/**
 * Primary model class. Provides basic but useful tools
 * to manage database data.
 * 
 * @author Stanislas Michalak <stanislas.michalak@gmail.com>
 * @package Panda.model
 * 
 */
abstract class Model implements ArrayAccess {

   protected $_primaryKey;
   protected $_tableName;
   protected $_errors = array();
   protected $_query;
   protected $_daoName;

   public function __construct() {
      PandaApplication::load('Panda.model.db.Query');
      $this->_daoName = $this->_daoName ? $this->_daoName : Config::read('datasources.default');
      $this->_query = new Query($this->_daoName);
   }
   
   public function hydrate(array $modelData) {
      if ($modelData !== array()) {
         foreach ($modelData as $attribute => $value) {
            $method = 'set' . ucfirst($attribute);

            if (is_callable(array($this, $method))) {
               $this->$method($value);
            }
         }
      }
   }
   
   protected function _tableName() {
      return (!empty($this->_tableName)) ? $this->_tableName : pluralize(strtolower(str_replace('Model', '', get_class($this))));
   }
   
   protected function _tableFields() {
      $fields = get_object_vars($this);
      foreach($fields as $key => $value) {
         if(in_array($key, array('_primaryKey', '_tableName', '_errors', '_query', '_daoName'))) {
            unset($fields[$key]);
         } else {
            $fields[ltrim($key, '_')] = $fields[$key];
            unset($fields[$key]);
         }
      }
      return array_keys($fields);
   }

   public function isValid() {
      return empty($this->_errors);
   }

   public function errors() {
      return $this->_errors;
   }

   public function first(array $conditions = array(), $field = '') {
      if($field !== '') {
         return $this->_query->select($field)->from($this->_tableName())->where($conditions)->limit(1)->getResult();
      } else {
         return $this->_query->select($this->_tableFields())->from($this->_tableName())->where($conditions)->limit(1)->getResult();
      }
   }
   
   public function count(array $conditions = array()) {
      return $this->_query->count()->from($this->_tableName())->where($conditions)->getResult();
   }
   
   public function exists(array $conditions = array()) {
      return $this->count($conditions) > 0 ? true : false; 
   }
   
   public function field($field, array $conditions = array()) {
      $modelAttributes = get_object_vars($this);
      if (!is_string($field) || empty($field) || !in_array('_' . $field, array_keys($modelAttributes))) {
         throw new InvalidArgumentException(__('Invalid or unknown field "%s".', $field));
      }
      return $this->_query->select($field)->from($this->_tableName())->where($conditions)->getResult();
   }
   
   public function find(array $conditions) {
      return $this->_query->select($this->_tableFields())->from($this->_tableName())->where($conditions)->getResult();
   }
   
   public function findAll() {
      return $this->_query->select($this->_tableFields())->from($this->_tableName())->getResult();
   }

   public function save() {
      if ($this->isValid()) {
         if ($this->_primaryKey !== false) {
            $primaryKey = explode(',', $this->_primaryKey);
            return true;
         } else {
            throw new ErrorException(__('Unable to save the model: please complete the _primaryKey attribute.'));
         }
      } else {
         return false;
      }
   }

   public function delete(array $conditions = array()) {
      return $this->_query->delete($this->_tableName())->where($conditions)->getResult();
   }

   protected function _customQuery($sql, $tokens) {
      return $this->_query->customQuery($sql, $tokens);
   }
   
   public function offsetExists($key) {
      return array_key_exists('_' . $key, get_object_vars($this)) && !in_array('_' . $key, array('_tableName', '_errors', '_query', '_daoName'));
   }
   
   public function offsetGet($key) {
      return $this->offsetExists($key) ? $this->{'_' . $key} : null;
   }
   
   public function offsetSet($key, $value) {
      if ($this->offsetExists($key)) {
         return $this->{'_' . $key} = $value;
      }
   }
   
   public function offsetUnset($key) {
      throw new RuntimeException(__('Unable to unset "%s" attribute of "%s".', $key, get_class($this)));
   }

}