<?php

/**
 * Typexam
 * 
 * @author Stanislas Michalak <stanislas.michalak@gmail.com>
 */
class TypeExamModel extends Model {
   protected $_datasourceName = 'Typexam';
   
   //Champs
   protected $_idType;
   protected $_libelle;
   protected $_coef;
   
   //Erreurs
   const BAD_ID_TYPE_ERROR = 1;
   const BAD_LIBELLE_ERROR = 2;
   const BAD_COEF_ERROR = 3;
   
   public function setIdType($idType) {
      if (is_numeric($idType) && (int) $idType > 0) {
         $this->_idType = (int) $idType;
      } else if (!empty($idType)) {
         $this->_errors[] = self::BAD_ID_TYPE_ERROR;
      }
   }
   
   public function setLibelle($libelle) {
      if (!empty($libelle) && is_string($libelle) && !is_numeric($libelle)) {
         $this->_libelle = $libelle;
      } else {
         $this->_errors[] = self::BAD_LIBELLE_ERROR;
      }
   }
   
   public function setCoef($coef) {
      $coef = is_string($coef) ? str_replace(',', '.', $coef) : $coef;
      if (is_numeric($coef) && (float) $coef > 0) {
         $this->_coef = (float) $coef;
      } else {
         $this->_errors[] = self::BAD_COEF_ERROR;
      }
   }
}