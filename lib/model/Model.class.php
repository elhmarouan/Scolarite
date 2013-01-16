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

   protected $_datasourceName;
   protected $_errors = array();
   protected $_query;
   protected $_daoName;

   public function __construct() {
      Application::load('Panda.model.db.Query');
      $this->_daoName = $this->_daoName ? $this->_daoName : Config::read('datasources.default');
      if (Config::read('cache.enable')) {
         if (!file_exists(SHARE_DIR . 'cache/datasource')) {
            mkdir(SHARE_DIR . 'cache/datasource');
         }
         if (!file_exists(SHARE_DIR . 'cache/datasource/' . $this->_daoName)) {
            mkdir(SHARE_DIR . 'cache/datasource/' . $this->_daoName);
         }
      }
      $this->_query = new Query($this->_daoName);
   }

   /**
    * Calls the set methods matching with $modelData keys. Allows
    * all the current model attributes to be filled at one time.
    * @param array $modelData
    */
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

   /**
    * Gives the datasource name for the current model :
    * - if it's already setted, the name is the setted value
    * - else, it's the plural form of the part before "Model" in the class name
    * @return string
    */
   protected function _datasourceName() {
      return (!empty($this->_datasourceName)) ? $this->_datasourceName : pluralize(strtolower(str_replace('Model', '', get_class($this))));
   }

   /**
    * Gives the datasource fields names, based on the attributes names.
    * @return array
    */
   protected function _datasourceFields() {
      $fields = get_object_vars($this);
      foreach ($fields as $key => $value) {
         if (in_array($key, array('_datasourceName', '_relations', '_errors', '_query', '_daoName'))) {
            unset($fields[$key]);
         } else {
            $fields[ltrim($key, '_')] = $fields[$key];
            unset($fields[$key]);
         }
      }
      return array_keys($fields);
   }

   /**
    * Gives the datasource keys and values, based on the attributes names.
    * @return array
    */
   protected function _datasourceValues() {
      $fields = get_object_vars($this);
      foreach ($fields as $key => $value) {
         if (in_array($key, array('_datasourceName', '_relations', '_errors', '_query', '_daoName')) || $fields[$key] === null) {
            unset($fields[$key]);
         } else {
            $fields[ltrim($key, '_')] = $fields[$key];
            unset($fields[$key]);
         }
      }
      return $fields;
   }
   
   public function lastInsertId() {
      return $this->_query->getLastInsertId();
   }

   /**
    * Checks if the current model is ready to be saved.
    * @return boolean
    */
   public function isValid() {
      foreach (get_object_vars($this) as $attribute => $value) {
         if ($value === null && !in_array($attribute, array('_datasourceName', '_relations', '_errors', '_query', '_daoName'))) {
            $this->{'set' . ucfirst(ltrim($attribute, '_'))}(false);
         }
      }
      return empty($this->_errors);
   }

   /**
    * Gets the errors list
    * @return array
    */
   public function errors() {
      return array_unique($this->_errors);
   }

   /**
    * If $field is an empty string, gets the first model matching with the conditions.
    * Else, gets only the field $field of the first model matching with the conditions.
    * @param array $conditions
    * @param string $field
    * @return mixed
    */
   public function first(array $conditions = array(), $field = '') {
      if ($field !== '') {
         return $this->_query->select($field)->from($this->_datasourceName())->where($conditions)->limit(1)->getResult();
      } else {
         return $this->_query->select($this->_datasourceFields())->from($this->_datasourceName())->where($conditions)->limit(1)->getResult();
      }
   }

   /**
    * Gets the number of models matching with the conditions, if there is any.
    * @param array $conditions
    * @return int
    */
   public function count(array $conditions = array()) {
      return $this->_query->count()->from($this->_datasourceName())->where($conditions)->getResult();
   }

   /**
    * Checks if there are one or more models matching with the conditions. 
    * @param array $conditions
    * @return boolean
    */
   public function exists(array $conditions = array()) {
      return $this->count($conditions) > 0 ? true : false;
   }

   /**
    * Gets only the field of each model matching with the conditions. 
    * @param string $field
    * @param array $conditions
    * @return array
    * @throws InvalidArgumentException
    */
   public function field($field, array $conditions = array()) {
      $modelAttributes = get_object_vars($this);
      if (!is_string($field) || empty($field) || !in_array('_' . $field, array_keys($modelAttributes))) {
         throw new InvalidArgumentException(__('Invalid or unknown field "%s".', $field));
      }
      return $this->_query->select($field)->from($this->_datasourceName())->where($conditions)->getResult();
   }

   /**
    * Gets the models matching with the conditions.
    * @param array $conditions
    * @return array
    */
   public function find(array $conditions) {
      return $this->_query->select($this->_datasourceFields())->from($this->_datasourceName())->where($conditions)->getResult();
   }

   /**
    * Gets all or part of all models attributes.
    * @param string $conditions,...
    * @return array
    */
   public function findAll() {
      $fields = func_num_args() > 0 ? func_get_args() : array();
      foreach ($fields as $field) {
         if (!is_string($field) || empty($field) || !in_array($field, $this->_datasourceFields())) {
            throw new InvalidArgumentException(__('Invalid or unknown field %s', $field));
         }
      }
      if ($fields !== array()) {
         return $this->_query->select($fields)->from($this->_datasourceName())->getResult();
      } else {
         return $this->_query->select($this->_datasourceFields())->from($this->_datasourceName())->getResult();
      }
   }

   /**
    * Saves the current model in the datasource (finds automaticaly if the model
    * is a new entry or not and acts in consequence).
    * @return boolean
    * @throws ErrorException
    */
   public function save() {
      if ($this->isValid()) {
         $primaryKeys = $this->_query->getPrimaryKeys($this->_datasourceName());
         if (count($primaryKeys) > 1) {
            $where = array();
            foreach ($primaryKeys as $primaryKey) {
               if (empty($this->{'_' . $primaryKey})) {
                  throw new ErrorException(__('Invalid primary key "%s": the primary key can\'t be empty.', $primaryKey));
               }
               $where[$primaryKey] = $this->{'_' . $primaryKey};
            }
            if (!$this->exists($where)) {
               $this->_query->insert($this->_datasourceName())->set($this->_datasourceValues())->getResult();
            } else {
               $this->_query->update($this->_datasourceName())->set($this->_datasourceValues())->where($where)->getResult();
            }
         } else {
            if (empty($this->{'_' . $primaryKeys[0]}) || !$this->exists(array($primaryKeys[0] => $this->{'_' . $primaryKeys[0]}))) {
               $this->_query->insert($this->_datasourceName())->set($this->_datasourceValues())->getResult();
            } else {
               $this->_query->update($this->_datasourceName())->set($this->_datasourceValues())->where(array($primaryKeys[0] => $this->{'_' . $primaryKeys[0]}))->getResult();
            }
         }
         return true;
      } else {
         return false;
      }
   }

   public function delete(array $conditions = array()) {
      return $this->_query->delete($this->_datasourceName())->where($conditions)->getResult();
   }

   /**
    * Allows to execute a custom query, if there isn't any other way to do.
    * Please use this method carefuly, and as less as possible if you want your
    * model to be used with another database system.
    * @param string $sql
    * @param array $tokens
    * @return type
    */
   protected function _customQuery($sql, array $tokens) {
      return $this->_query->customQuery($sql, $tokens);
   }

   /**
    * Allows to check if a model attribute exists, using the array syntax. 
    * @param string $key
    * @return boolean
    */
   public function offsetExists($key) {
      return array_key_exists('_' . $key, get_object_vars($this)) && !in_array('_' . $key, array('_datasourceName', '_relations', '_errors', '_query', '_daoName'));
   }

   /**
    * Allows the value of a model attribute to be getted, using the array syntax. 
    * @param string $key
    * @return mixed
    */
   public function offsetGet($key) {
      return $this->offsetExists($key) ? $this->{'_' . $key} : null;
   }

   /**
    * Allows the value of a model attribute to be setted, using the array syntax. 
    * @param string $key
    * @return boolean
    */
   public function offsetSet($key, $value) {
      if ($this->offsetExists($key)) {
         return $this->{'set' . ucfirst($key)}($value);
      }
   }

   /**
    * Forbids a model attribute to be deleted, using the array syntax. 
    * @param string $key
    * @return boolean
    */
   public function offsetUnset($key) {
      throw new RuntimeException(__('Unable to unset "%s" attribute of "%s".', $key, get_class($this)));
   }

}