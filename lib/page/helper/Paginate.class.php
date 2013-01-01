<?php

/**
 * Paginate
 * 
 * @author Stanislas Michalak <stanislas.michalak@gmail.com>
 * @package Panda.page.helper
 * 
 */
class Paginate {

   protected $_getVar;
   protected $_model;
   protected $_conditions;
   protected $_nbItemsPage;

   public function __construct(Model $model, $conditions = array(), $getVar = 'page', $nbItemsPage = 20) {
      $this->_setModel($model);
      $this->_setConditions($conditions);
      $this->_setGetVar($getVar);
      $this->_setNbItemsPage($nbItemsPage);
   }

   protected function _setModel(Model $model) {
      $this->_model = $model;
   }

   protected function _setConditions(array $conditions) {
      $this->_conditions = $conditions;
   }

   protected function _setGetVar($getVar) {
      if (!empty($getVar) && is_string($getVar) && HTTPRequest::getExists($getVar)) {
         $this->_getVar = $getVar;
      }
   }

   protected function _setNbItemsPage($nbItemsPage) {
      if (!empty($nbItemsPage) && is_int($nbItemsPage)) {
         $this->_nbItemsPage = $nbItemsPage;
      }
   }

   public function currentPage() {
      return (int) HTTPRequest::get($this->_getVar);
   }

   public function countPages() {
      return ceil($this->_model->count($this->_conditions) / $this->_nbItemsPage);
   }

   public function paginate() {
      $pagination = '<p>' . __('Page:');
      for ($i = 1; $i <= $this->countPages(); $i++) {
         if ($i === $this->currentPage()) {
            $pagination .= '<span class="currentPage">' . $i . '</span>';
         } else {
            $pagination .= '<a href="">' . $i . '</a>';
         }
      }
      return $pagination . '</p>';
   }

}