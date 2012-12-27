<?php

/**
 * Prof
 * 
 * @author Stanislas Michalak <stanislas.michalak@gmail.com>
 */
class ProfModel extends Model {
   
   protected $_datasourceName = 'Prof';
   
   //Relations
   protected $_relations = array(
       '_idUtil' => 'Utilisateur._idUtil'
   );
   
   //Champs
   protected $_idProf;
   protected $_numBureau;
   protected $_telBureau;
   protected $_idUtil;
}