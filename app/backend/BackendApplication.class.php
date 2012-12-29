<?php

/**
 * Backend application
 * 
 * @author Céline LEPICARD <lepicardce@eisti.eu>
 * 
 */
class BackendApplication extends PandaApplication {

   public function __construct() {
      parent::__construct('Backend');
   }

   public function run() {
      if (User::isOnline()) {
         $controller = $this->getController();
      } else {
         self::load('App.backend.module.user.UserController');
         $controller = new UserController($this, 'user', 'connexion');
      }

      $controller->exec();

      PandaResponse::setPage($controller->page());
      PandaResponse::sendRenderedPage();
   }

}
