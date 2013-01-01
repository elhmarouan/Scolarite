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

   protected static $_id;
   protected static $_username;
   protected static $_key;
   protected static $_model;
   protected static $_components = array();

   public function __construct() {
      Application::load('Panda.user.component.UserComponent');
      Application::load('Panda.user.component.Popup');
      Application::load('Panda.user.component.Acl');
      $this->_getSession();
      self::$_components['popup'] = new Popup($this);
      self::$_components['acl'] = new Acl($this);
   }

   /**
    * Gets an instance of the User class model.
    * @return Model
    * @throws RuntimeException
    */
   public function model() {
      if (self::$_model === null) {
         try {
            $modelName = Config::read('user.model');
         } catch (Exception $e) {
            if ($e->getCode() === Config::UNKNOWN_KEY) {
               throw new RuntimeException(__('Unable to use the User component: please set the user.model key in the configuration file.'));
            }
         }
         Application::load('Model.' . $modelName);
         $modelName .= 'Model';
         self::$_model = new $modelName;
      }
      return self::$_model;
   }

   /**
    * Returns the id of the current user.
    * @return int
    */
   public static function id() {
      return self::$_id;
   }

   /**
    * Returns the username of the current user.
    * @return string
    */
   public static function username() {
      return self::$_username;
   }

   public function setId($userId) {
      if ((is_int($userId) && $userId !== 0) || (is_numeric($userId) && $userId !== '0')) {
         self::$_id = (int) $userId;
      }
   }

   public function setUsername($username) {
      if (is_string($username) && !empty($username)) {
         self::$_username = $username;
      }
   }

   protected function _buildKey($userId, $username) {
      if ($userId === null && $username === null) {
         return null;
      } else {
         try {
            return __hash($userId . '_' . $username, Config::read('salt.session.prefix'), Config::read('salt.session.suffix'));
         } catch (Exception $e) {
            if ($e->getCode() === Config::UNKNOWN_KEY) {
               throw new RuntimeException(__('Unable to build the session key: please set the "salt.session" key in the configuration file.'));
            }
         }
      }
   }
   
   protected function _verifKey($key, $id, $username) {
      if ($id === null && $username === null && $key === null) {
         return true;
      } else {
         if ($this->_buildKey($id, $username) === $key) {
            return true;
         } else {
            return false;
         }
      }
   }

   protected function _getSession() {
      if (!HTTPRequest::sessionExists('user') || HTTPRequest::session('user') === null || !is_array(HTTPRequest::session('user')) || !$this->_verifKey(HTTPRequest::session('user.key'), HTTPRequest::session('user.id'), HTTPRequest::session('user.username')) || !$this->model()->userExists(HTTPRequest::session('user.id'), HTTPRequest::session('user.username'))) {
         HTTPResponse::setSession('user', array(
             'id' => null,
             'username' => null,
             'key' => null
         ));
      }
      $userSessionData = HTTPRequest::session('user');
      $this->setId($userSessionData['id']);
      $this->setUsername($userSessionData['username']);
      self::$_key = $this->_buildKey(self::id(), self::username());
   }
   
   public function login($userId, $username) {
      HTTPResponse::setSession('user', array(
          'id' => $userId,
          'username'=> $username,
          'key' => $this->_buildKey($userId, $username)
      ));
      $this->_getSession();
   }
   
   public function logout() {
      HTTPResponse::setSession('user', null);
      $this->_getSession();
   }
   
   public static function isOnline() {
      return HTTPRequest::session('user') === array('id' => null, 'username' => null, 'key' => null) ? false : true; 
   }

   public static function isAllowedTo($rightKey) {
      return self::$_components['acl']->isAllowedTo($rightKey);
   }

   public static function isMemberOf($groupKey) {
      return self::$_components['acl']->isMemberOf($groupKey);
   }

   public static function addPopup($message, $type = Popup::INFORMATION, $contentIsHtml = false) {
      self::$_components['popup']->send($message, $type, $contentIsHtml);
   }

   public static function getPopups() {
      return self::$_components['popup']->popupList();
   }

}