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
}