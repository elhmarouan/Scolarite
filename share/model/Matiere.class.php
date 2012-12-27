<?php

/**
 * Matiere
 * 
 * @author Stanislas Michalak <stanislas.michalak@gmail.com>
 */
class MatiereModel extends Model {

   protected $_datasourceName = 'Matiere';
   
   //Relations
   protected $_relations = array(
       '_idMod' => 'Module._idMod',
       '_idProf' => 'Prof._idProf'  
   );
   
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
      if (is_numeric($idMat) && (int) $idMat > 0) {
         $this->_idMat = (int) $idMat;
      } else {
         $this->_errors[] = self::BAD_ID_MAT_ERROR;
      }
   }

   public function setLibelle($libelle) {
      if (!empty($libelle) && is_string($libelle) && !is_numeric($libelle)) {
         $this->_libelle = $libelle;
      } else {
         $this->_errors[] = self::BAD_LIBELLE_ERROR;
      }
   }

   public function setCoefMat($coefMat) {
      if (is_numeric($coefMat) && (float) $coefMat > 0) {
         $this->_coefMat = (float) $coefMat;
      } else {
         $this->_errors[] = self::BAD_COEF_MAT_ERROR;
      }
   }

   public function setIdMod($idMod) {
      if (is_numeric($idMod) && (int) $idMod > 0) {
         $this->_idMod = (int) $idMod;
      } else {
         $this->_errors[] = self::BAD_ID_MOD_ERROR;
      }
   }

   public function setIdProf($idProf) {
      if (is_numeric($idProf) && ((int) $idProf > 0)) {
         $this->_idProf = (int) $idProf;
      } else {
         $this->_errors[] = self::BAD_ID_PROF_ERROR;
      }
   }

}