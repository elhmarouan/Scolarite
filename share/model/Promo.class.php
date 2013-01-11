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
      if ((is_numeric($idPromo) && (int) $idPromo > 0)) {
         $this->_idPromo = (int) $idPromo;
      } else if (!empty($idPromo)) {
         $this->_errors[] = self::BAD_ID_PROMO_ERROR;
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