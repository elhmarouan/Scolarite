<?php

/**
 * Examen
 * 
 * @author Stanislas Michalak <stanislas.michalak@gmail.com>
 */
class ExamenModel extends Model {
   protected $_datasourceName = 'Examen';
   
   //Champs
   protected $_idExam;
   protected $_libelle;
   protected $_idMat;
   protected $_date;
   protected $_idType;
   
   //Erreurs
   const BAD_ID_EXAM_ERROR = 1;
   const BAD_LIBELLE_ERROR = 2;
   const BAD_ID_MAT_ERROR = 3;
   const BAD_DATE_ERROR = 4;
   const BAD_ID_TYPE_ERROR = 5;
   
   public function setIdExam($idExam) {
      if (is_numeric($idExam) && (int) $idExam > 0) {
         $this->_idExam = (int) $idExam;
      } else if (!empty($idExam)) {
         $this->_errors[] = self::BAD_ID_EXAM_ERROR;
      }
   }
   
   public function setLibelle($libelle) {
      if (!empty($libelle) && is_string($libelle) && !is_numeric($libelle)) {
         $this->_libelle = $libelle;
      } else {
         $this->_errors[] = self::BAD_LIBELLE_ERROR;
      }
   }
   
   public function setIdMat($idMat) {
      if (!empty($idMat) && is_numeric($idMat) && (int) $idMat > 0 && Controller::model('Matiere')->exists(array('idMat' => $idMat))) {
         $this->_idMat = (int) $idMat;
      } else {
         $this->_errors[] = self::BAD_ID_MAT_ERROR;
      }
   }
   
   public function setDate($date) {
      if (preg_match('#^[0-9]{2}/[0-9]{2}/[0-9]{4}$#', $date)) {
         $date = explode('/', $date);
         if ($date[0] !== '00' && $date[1] !== '00' && $date[2] !== '0000') {
            $this->_date = new DateTime($date[2] . '-' . $date[1] . '-' . $date[0]);
         } else {
            $this->_errors[] = self::BAD_DATE_ERROR;
         }
      } else {
         $this->_errors[] = self::BAD_DATE_ERROR;
      }
   }
   
   public function setIdType($idType) {
      if (!empty($idType) && is_numeric($idType) && (int) $idType > 0 && Controller::model('TypeExam')->exists(array('idType' => $idType))) {
         $this->_idType = (int) $idType;
      } else {
         $this->_errors[] = self::BAD_ID_TYPE_ERROR;
      }
   }
}