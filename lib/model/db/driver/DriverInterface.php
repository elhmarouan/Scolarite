<?php

/**
 * Driver interface
 * 
 * The common interface for dao drivers
 * 
 * @author Stanislas Michalak <stanislas.michalak@gmail.com>
 * @package Panda.model.db.driver
 * 
 */
interface DriverInterface {
   public function select(Query $query);
   public function count(Query $query);
   public function insert(Query $query);
   public function update(Query $query);
   public function delete(Query $query);
   public function createDatasource(Query $query);
   public function dropDatasource(Query $query);
   public function query($sql, array $tokens = array());
}