<?php

/**
 * User
 * 
 * @author Stanislas Michalak <stanislas.michalak@gmail.com>
 * @package Panda.user
 * 
 */

session_start();

class User {
   protected $_id;
   protected $_email;
   protected $_lang;
   protected $_key;
   protected $_popups;
   
   public function __construct() {
      PandaApplication::load('Panda.user.component.Popup');
      $this->_getSession();
      $this->_popups = new Popup;
   }
   
   public function id() {
      return $this->_id;
   }
   
   public function email() {
      return $this->_email;
   }
   
   public function lang() {
      return $this->_lang;
   }
   
   public function key() {
      return $this->_key;
   }
   
   public function setId($userId) {
      if(is_int($userId) && $userId !== 0) {
         $this->_id = $userId;
      }
   }
   
   public function setEmail($userEmail) {
      if(!empty($userEmail) && filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
         $this->_email = $userEmail;
      }
   }
   
   public function setLang($userLang) {
      //TODO!
   }
   
   public function setKey($userKey) {
      if(is_string($userKey) && !empty($userKey)) {
         $this->_key = $userKey;
      }
   }
   
   protected function _getSession() {
      if (PandaRequest::sessionExists('user')) {
         if (PandaRequest::session('user') === null || !is_array(PandaRequest::session('user'))) {
            PandaResponse::setSession('user', array(
                'id' => null,
                'email' => null,
                'key' => null
            ));
         }
         $userSessionData = PandaRequest::session('user');
         $this->setId($userSessionData['id']);
         $this->setEmail($userSessionData['email']);
         $this->setKey($userSessionData['key']);
      }
   }
   
   private function __getUserData() {
      
   }
   
   public function addPopup($message, $type = Popup::INFORMATION, $contentIsHtml = false) {
      $this->_popups->send($message, $type, $contentIsHtml);
   }
   
   public function getPopups() {
      return $this->_popups->popupList();
   }
}