<?php

/**
 * Promo
 * 
 * @author Stanislas Michalak <stanislas.michalak@gmail.com>
 */
class PromoModel extends Model {
   protected $_datasourceName = 'Promo';
   
   //Champs
   protected $_idPromo;
   protected $_libelle;
   
   //Erreurs
   const BAD_ID_PROMO_ERROR = 1;
   const BAD_LIBELLE_ERROR = 2;
   
   public function setIdPromo($idPromo) {
      $idPromo = (int) $idPromo;
      if ($idPromo > 0) {
         $this->_idPromo = $idPromo;
      } else {
         $this->_errors[] = self::BAD_ID_PROMO_ERROR;
      }
   }
   
   public function setLibelle($libelle) {
      if(!empty($libelle) && is_string($libelle)) {
         $this->_libelle = $libelle;
      } else {
         $this->_errors[] = self::BAD_LIBELLE_ERROR;
      }
   }
}