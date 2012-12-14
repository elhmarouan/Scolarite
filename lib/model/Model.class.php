<?php

/**
 * Primary model class. Provide basic but useful tools
 * to create, validate, save and delete a model.
 * 
 * @author Stanislas Michalak <stanislas.michalak@gmail.com>
 * @package Panda.model
 * 
 */
abstract class Model {

   protected $_tableName;
   protected $_errors = array();
   protected $_query;
   protected $_daoName;

   public function __construct() {
      PandaApplication::load('Panda.model.db.Query');
      $this->_daoName = $this->_daoName ? $this->_daoName : Config::read('datasources.default');
      $this->_query = new Query($this->_daoName);
   }
   
   protected function _tableName() {
      return (!empty($this->_tableName)) ? $this->_tablename : pluralize(strtolower(str_replace('Model', '', get_class($this))));
   }
   
   protected function _tableFields() {
      $fields = get_object_vars($this);
      foreach($fields as $key => $value) {
         if(in_array($key, array('_tableName', '_errors', '_query', '_daoName'))) {
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

   public function create() {
      if ($this->isValid()) {
         
      } else {
         return $this->errors();
      }
   }

   public function save() {
      if ($this->isValid()) {
         
      } else {
         return $this->errors();
      }
   }

   public function delete() {
      
   }

   protected function _customQuery($sql, $tokens) {
      return $this->_query->customQuery($sql, $tokens);
   }

}
