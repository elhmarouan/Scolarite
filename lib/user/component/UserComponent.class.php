<?php

/**
 * User component
 * 
 * @author Stanislas Michalak <stanislas.michalak@gmail.com>
 * @package Panda.user
 * 
 */
class UserComponent {
   protected $_user;
   
   public function __construct(User $user) {
      $this->_user = $user;
   }
   
   public function user() {
      return $this->_user;
   }
}