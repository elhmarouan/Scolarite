<?php

/**
 * Participe
 * 
 * @author Stanislas Michalak <stanislas.michalak@gmail.com>
 */
class ParticipeModel extends Model {
   protected $_datasourceName = 'Participe';
   
   //Champs
   protected $_numEtudiant;
   protected $_idExam;
   protected $_note;
   
   //Erreurs
   const BAD_NUM_ETUDIANT_ERROR = 1;
   const BAD_ID_EXAM_ERROR = 2;
   const BAD_NOTE_ERROR = 3;
   
   public function setNumEtudiant($numEtudiant) {
      if (is_numeric($numEtudiant) && (int) $numEtudiant > 0) {
         $this->_numEtudiant = (int) $numEtudiant;
      } else {
         $this->_errors[] = self::BAD_NUM_ETUDIANT_ERROR;
      }
   }
   
   public function setIdExam($idExam) {
      if (is_numeric($idExam) && (int) $idExam > 0) {
         $this->_idExam = (int) $idExam;
      } else {
         $this->_errors[] = self::BAD_ID_EXAM_ERROR;
      }
   }
   
   public function setNote($note) {
      $note = is_string($note) ? str_replace(',', '.', $note) : $note;
      if (is_numeric($note) && (float) $note >= 0 && (float) $note <= 20) {
         $this->_note = (float) $note;
      } else {
         $this->_errors[] = self::BAD_NOTE_ERROR;
      }
   }
}