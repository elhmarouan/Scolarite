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
   
   public function getGroupsOf($idUtil) {
      $role = PandaController::model('Role')->first(array('idRole' => $this->first(array('idUtil' => $idUtil), 'idRole')), 'libelle');
      return empty($role) ? array() : array(array('key' => $role));
   }
   
   public function userExists($idUtil, $login) {
      return $this->exists(array('idUtil' => $idUtil, 'login' => $login));
   }
}