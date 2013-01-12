<?php

/**
 * Module
 * 
 * @author Stanislas Michalak <stanislas.michalak@gmail.com>
 */
class ModuleModel extends Model {

   protected $_datasourceName = 'Module';
   
   //Champs
   protected $_idMod;
   protected $_libelle;
   protected $_idPromo;

   //Erreurs
   const BAD_ID_MOD_ERROR = 1;
   const BAD_LIBELLE_ERROR = 2;
   const BAD_ID_PROMO_ERROR = 3;

   public function setIdMod($idMod) {
      if (is_numeric($idMod) && (int) $idMod > 0) {
         $this->_idMod = (int) $idMod;
      } else if (!empty($idMod)) {
         $this->_errors[] = self::BAD_ID_MOD_ERROR;
      }
   }

   public function setLibelle($libelle) {
      if (!empty($libelle) && is_string($libelle) && !is_numeric($libelle)) {
         $this->_libelle = $libelle;
      } else {
         $this->_errors[] = self::BAD_LIBELLE_ERROR;
      }
   }

   public function setIdPromo($idPromo) {
      if (is_numeric($idPromo) && (int) $idPromo > 0) {
         $this->_idPromo = (int) $idPromo;
      } else {
         $this->_errors[] = self::BAD_ID_PROMO_ERROR;
      }
   }

}