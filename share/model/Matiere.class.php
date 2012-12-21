<?php

/**
 * Matiere
 * 
 * @author Stanislas Michalak <stanislas.michalak@gmail.com>
 */
class MatiereModel extends Model {
   protected $_tableName = 'Matiere';
   
   //Champs
   protected $_idMat;
   protected $_libelle;
   protected $_coefMat;
   protected $_idMod;
   protected $_idProf;
   
   //Erreurs
   const BAD_ID_MAT_ERROR = 1;
   const BAD_LIBELLE_ERROR = 2;
   const BAD_COEF_MAT_ERROR = 3;
   const BAD_ID_MOD_ERROR = 4;
   const BAD_ID_PROF_ERROR = 5;
   
   public function setIdMat($idMat) {
      $idMat = (int) $idMat;
      if ($idMat > 0) {
         $this->_idMat = $idMat;
      } else {
         $this->_errors[] = self::BAD_ID_MAT_ERROR;
      }
   }
   
   public function setLibelle($libelle) {
      if(!empty($libelle) && is_string($libelle)) {
         $this->_libelle = $libelle;
      } else {
         $this->_errors[] = self::BAD_LIBELLE_ERROR;
      }
   }
   
   public function setCoefMat($coefMat) {
      $coefMat = (float) $coefMat;
      if ($coefMat > 0) {
         $this->_coefMat = $coefMat;
      } else {
         $this->_errors[] = self::BAD_COEF_MAT_ERROR;
      }
   }
   
   public function setIdMod($idMod) {
      $idMod = (int) $idMod;
      if ($idMod > 0) {
         $this->_idMod = $idMod;
      } else {
         $this->_errors[] = self::BAD_ID_MOD_ERROR;
      }
   }
   
   public function setIdProf($idProf) {
      $idProf = (int) $idProf;
      if ($idProf > 0) {
         $this->_idProf = $idProf;
      } else {
         $this->_errors[] = self::BAD_ID_PROF_ERROR;
      }
   }
}