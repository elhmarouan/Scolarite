<?php

/**
 * Mysql driver
 * 
 * A simple layer, based on PDO, allowing to communicate with MySQL
 * in a more simple way than PDO propose. This driver provide some
 * functions which use a MySQL-specific syntax. The regular ones are
 * provided by the PDOAbstract class, to avoid redundant code.  
 * 
 * @author Stanislas Michalak <stanislas.michalak@gmail.com>
 * @package Panda.model.db.driver
 * 
 */

Application::load('Panda.model.db.driver.PDOAbstract');

class MysqlDriver extends PDOAbstract implements DriverInterface {

   //Date formats
   protected $_dateFormat = 'Y-m-d';
   protected $_timeFormat = 'H:i:s';
   protected $_dateTimeFormat = 'Y-m-d H:i:s';

   protected function _buildDsn(array $credentials) {
      $dsn = 'mysql:';
      if (isset($credentials['host']) && $credentials['host'] != '') {
         $dsn .= 'host=' . $credentials['host'] . ';';
      }
      if (isset($credentials['port'])) {
         $dsn .= 'port=' . $credentials['port'] . ';';
      }
      if (isset($credentials['dbName'])) {
         $dsn .= 'dbname=' . $credentials['dbName'] . ';';
      }
      if (isset($credentials['unixSocket'])) {
         $dsn .= 'unix_socket=' . $credentials['unix_socket'] . ';';
      }
      if (isset($credentials['charset'])) {
         $dsn .= 'charset=' . $credentials['charset'] . ';';
      }
      return $dsn;
   }
   
   public function primaryKeysOf($datasource) {
      if(empty($datasource) || !is_string($datasource)) {
         throw new InvalidArgumentException(__('Unable to get this table primary keys: invalid datasource.'));
      }
      $primaryKeysSchema = $this->fetchAll($this->query('SHOW KEYS FROM ' . $this->_escapeField($datasource) . ' WHERE Key_name = "PRIMARY"'));
      $primaryKeys = array();
      foreach ($primaryKeysSchema as $primaryKey) {
         $primaryKeys[] = $primaryKey['Column_name'];
      }
      return $primaryKeys;
   }
   
   /*public function typesOf($datasource) {
      if(empty($datasource) || !is_string($datasource)) {
         throw new InvalidArgumentException(__('Unable to get this table fields types: invalid datasource.'));
      }
      $fieldsSchema = $this->fetchAll($this->query('SHOW FIELDS FROM ' . $this->_escapeField($datasource)));
      $fieldsTypes = array();
      foreach ($fieldsSchema as $field) {
         $fieldsTypes[$field['Field']] = $field['Type'];
      }
      return $fieldsTypes;
   }*/
   
   public function _escapeField($field) {
      return ($field === '*') ? $field : '`' . $field . '`';
   }

}