<?php

/**
 * Popup
 * 
 * @author Stanislas Michalak <stanislas.michalak@gmail.com>
 * @package Panda.user.component
 * 
 */
class Popup {

   const ERROR = 1;
   const INFORMATION = 2;
   const SUCCES = 3;
   const WARNING = 4;
   
   private $_popups = array();

   public function __construct() {
      $this->_getSession();
   }
   
   private function _getSession() {
      if (PandaRequest::sessionExists('user.popups')) {
         if (PandaRequest::session('user.popups') !== null && is_array(PandaRequest::session('user.popups'))) {
            $this->_popups = PandaRequest::session('user.popups');
         }
         PandaResponse::unsetSession('user.popups');
      }
   }
   
   public function send($message, $type = self::INFORMATION, $contentIsHtml = false) {
      if(is_string($message) && is_int($type) && is_bool($contentIsHtml)) {
         $message = $contentIsHtml ? $message : htmlspecialchars($message);
         PandaResponse::setSession('user.popups', array('message' => $message, 'type' => $type), true);
         $this->_getSession();
      }
   }
   
   public function popupsExist() {
      return !empty($this->_popups);
   }
   
   public function popupList() {
      return $this->_popups;
   }

}