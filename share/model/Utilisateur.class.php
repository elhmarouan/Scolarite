<?php

/**
 * Utilisateur
 * 
 * @author Stanislas Michalak <stanislas.michalak@gmail.com>
 */
class UtilisateurModel extends Model {
   
   protected $_datasourceName = 'Utilisateur';
   
   //Relations
   protected $_relations = array(
       '_idRole' => 'Role._idRole'
   );
   
   //Champs
   protected $_idUtil;
   protected $_login;
   protected $_pass;
   protected $_nom;
   protected $_prenom;
   protected $_idRole;
   
   //Erreurs
   const BAD_ID_UTIL_ERROR = 1;
   const BAD_LOGIN_ERROR = 2;
   const BAD_PASS_ERROR = 3;
   const BAD_NOM_ERROR = 4;
   const BAD_PRENOM_ERROR = 5;
   const BAD_ID_ROLE_ERROR = 6;
   
   public function setIdUtil($idUtil) {
      if (empty($idUtil) || (is_numeric($idUtil) && (int) $idUtil > 0)) {
         $this->_idUtil = (int) $idUtil;
      } else {
         $this->_errors[] = self::BAD_ID_UTIL_ERROR;
      }
   }

   public function setLogin($login) {
      if (!empty($login) && is_string($login) && !is_numeric($login)) {
         $this->_login = $login;
      } else {
         $this->_errors[] = self::BAD_LOGIN_ERROR;
      }
   }
   
   public function setPass($pass) {
      if (!empty($pass) && is_string($pass) && strlen($pass) >= 7) {
         $this->_pass = $pass;
      } else if (!($pass === false && $this->_idUtil !== null)) {
         $this->_errors[] = self::BAD_PASS_ERROR;
      }
   }
   
   public function setNom($nom) {
      if (!empty($nom) && is_string($nom) && !is_numeric($nom)) {
         $this->_nom = $nom;
      } else {
         $this->_errors[] = self::BAD_NOM_ERROR;
      }
   }
   
   public function setPrenom($prenom) {
      if (!empty($prenom) && is_string($prenom) && !is_numeric($prenom)) {
         $this->_prenom = $prenom;
      } else {
         $this->_errors[] = self::BAD_PRENOM_ERROR;
      }
   }
   
   public function setIdRole($idRole) {
      if (is_numeric($idRole) && (int) $idRole > 0) {
         $this->_idRole = (int) $idRole;
      } else {
         $this->_errors[] = self::BAD_ID_ROLE_ERROR;
      }
   }
   
   //Méthodes propres au composant Panda.user.User
   
   public function getGroupsOf($idUtil) {
      $role = Controller::model('Role')->first(array('idRole' => $this->first(array('idUtil' => $idUtil), 'idRole')), 'libelle');
      return empty($role) ? array() : array(array('key' => $role));
   }
   
   public function getRightsOf() {
      throw new BadMethodCallException('Gestion des droits indisponible');
   }

   public function getRightsData() {
      throw new BadMethodCallException('Gestion des droits indisponible');
   }
   
   public function userExists($idUtil, $login) {
      return $this->exists(array('idUtil' => $idUtil, 'login' => $login));
   }
}