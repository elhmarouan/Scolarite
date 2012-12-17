<?php

/**
 * Matiere
 * 
 * @author Stanislas Michalak <stanislas.michalak@gmail.com>
 */
class MatiereModel extends Model {
   protected $_tableName = 'Matière';
   
   //Champs
   protected $_idMat;
   protected $_libelle;
   protected $_coefMat;
   protected $_idMod;
   protected $_idProf;
}