<?php

/**
 * Backend application
 * 
 * Application réunissant les modules dont l'accès
 * est réglementé (admin, prof, eleve), ainsi que
 * le module user permettant la connexion/déconnexion.
 * 
 * @author Céline LEPICARD <lepicardce@eisti.eu>
 * 
 */
class BackendApplication extends Application {

   public function __construct() {
      parent::__construct('Backend');
   }

   /**
    * Vérifie si l'utilisateur est connecté. Si ce n'est
    * pas le cas, renvoie le contrôleur de connexion.
    * @return boolean|UserController
    */
   public function accessFilter() {
      if (User::isOnline()) {
         return true;
      } else {
         self::load('App.backend.module.user.UserController');
         return new UserController($this, 'user', 'connexion');
      }
   }

}
