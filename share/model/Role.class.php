<?php

/**
 * Role
 * 
 * @author Stanislas Michalak <stanislas.michalak@gmail.com>
 */
class RoleModel extends Model {
   protected $_datasourceName = 'Role';
   
   //Champs
   protected $_idRole;
   protected $_libelle;
   
   //Erreurs
   const BAD_ID_ROLE_ERROR = 1;
   const BAD_LIBELLE_ERROR = 2;
   
   public function setIdRole($idRole) {
      if (is_numeric($idRole) && (int) $idRole > 0) {
         $this->_idRole = (int) $idRole;
      } else if (!empty($idRole)) {
         $this->_errors[] = self::BAD_ID_ROLE_ERROR;
      }
   }
   
   public function setLibelle($libelle) {
      if (!empty($libelle) && is_string($libelle) && !is_numeric($libelle)) {
         $this->_libelle = $libelle;
      } else {
         $this->_errors[] = self::BAD_LIBELLE_ERROR;
      }
   }
}