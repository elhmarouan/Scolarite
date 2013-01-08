<?php

/**
 * Prof
 * 
 * @author Stanislas Michalak <stanislas.michalak@gmail.com>
 */
class ProfModel extends Model {

   protected $_datasourceName = 'Prof';
   
   //Champs
   protected $_idProf;
   protected $_numBureau;
   protected $_telBureau;
   protected $_idUtil;

   //Erreurs
   const BAD_ID_PROF_ERROR = 1;
   const BAD_NUM_BUREAU_ERROR = 2;
   const BAD_TEL_BUREAU_ERROR = 3;
   const BAD_ID_UTIL_ERROR = 4;

   public function setIdProf($idProf) {
      if (empty($idProf) || (is_numeric($idProf) && (int) $idProf > 0)) {
         $this->_idProf = (int) $idProf;
      } else {
         $this->_errors[] = self::BAD_ID_PROF_ERROR;
      }
   }
   
   public function setNumBureau($numBureau) {
      if (preg_match('#^[A-Z]{2}[0-9]{3}#i', $numBureau)) {
         $this->_numBureau = $numBureau;
      } else {
         $this->_errors[] = self::BAD_NUM_BUREAU_ERROR;
      }
   }
   
   public function setTelBureau($telBureau) {
      if (preg_match('#^0[1-68][0-9]{8}$#', $telBureau)) {
         $this->_telBureau = $telBureau;
      } else {
         $this->_errors[] = self::BAD_TEL_BUREAU_ERROR;
      }
   }
   
   public function setIdUtil($idUtil) {
      if (empty($idUtil) || (is_numeric($idUtil) && (int) $idUtil > 0)) {
         $this->_idUtil = (int) $idUtil;
      } else {
         $this->_errors[] = self::BAD_ID_UTIL_ERROR;
      }
   }
}