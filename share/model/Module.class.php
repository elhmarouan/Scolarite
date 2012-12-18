<?php

/**
 * Module
 * 
 * @author Stanislas Michalak <stanislas.michalak@gmail.com>
 */
class ModuleModel extends Model {
   protected $_tableName = 'Module';
   
   //Champs
   protected $_idMod;
   protected $_libelle;
   protected $_idPromo;
   
   //Erreurs
   const BAD_ID_MOD_ERROR = 1;
   const BAD_LIBELLE_ERROR = 2;
   const BAD_ID_PROMO_ERROR = 3;
   
   public function setIdMod($idMod) {
      $idMod = (int) $idMod;
      if ($idMod > 0) {
         $this->_idMod = $idMod;
      } else {
         $this->_errors[] = self::BAD_ID_MOD_ERROR;
      }
   }
   
   public function setLibelle($libelle) {
      if(!empty($libelle) && is_string($libelle)) {
         $this->_libelle = $libelle;
      } else {
         $this->_errors[] = self::BAD_LIBELLE_ERROR;
      }
   }
   
   public function setIdPromo($idPromo) {
      $idPromo = (int) $idPromo;
      if ($idPromo > 0) {
         $this->_idPromo = $idPromo;
      } else {
         $this->_errors[] = self::BAD_ID_PROMO_ERROR;
      }
   }
}