<?php

/**
 * Eleve
 * 
 * @author Stanislas Michalak <stanislas.michalak@gmail.com>
 */
class EleveModel extends Model {
   protected $_datasourceName = 'Eleve';
   
   //Champs
   protected $_numEtudiant;
   protected $_anneeRedouble;
   protected $_idPromo;
   protected $_idUtil;
   
   //Erreurs
   const BAD_NUM_ETUDIANT_ERROR = 1;
   const BAD_ANNEE_REDOUBLE_ERROR = 2;
   const BAD_ID_PROMO_ERROR = 3;
   const BAD_ID_UTIL_ERROR = 4;
   
   public function setNumEtudiant($numEtudiant) {
      if (is_numeric($numEtudiant) && (int) $numEtudiant > 0) {
         $this->_numEtudiant = (int) $numEtudiant;
      } else {
         $this->_errors[] = self::BAD_NUM_ETUDIANT_ERROR;
      }
   }
   
   public function setAnneeRedouble($anneeRedouble) {
      if (checkdate(1, 1, $anneeRedouble)) {
         $this->_anneeRedouble = $anneeRedouble;
         } else if (!empty($anneeRedouble)) {
         $this->_errors[] = self::BAD_ANNEE_REDOUBLE_ERROR;
      }
   }
   
   public function setIdPromo($idPromo) {
      if (is_numeric($idPromo) && (int) $idPromo > 0) {
         $this->_idPromo = (int) $idPromo;
      } else if (!empty($idPromo)) {
         $this->_errors[] = self::BAD_ID_PROMO_ERROR;
      }
   }
   
   public function setIdUtil($idUtil) {
      if (is_numeric($idUtil) && (int) $idUtil > 0) {
         $this->_idUtil = (int) $idUtil;
      } else {
         $this->_errors[] = self::BAD_ID_UTIL_ERROR;
      }
   }
   
}