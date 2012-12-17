<?php

/**
 * Utilisateur
 * 
 * @author Stanislas Michalak <stanislas.michalak@gmail.com>
 */
class UtilisateurModel extends Model {
   protected $_tableName = 'Utilisateur';
   
   //Champs
   protected $_idUtil;
   protected $_login;
   protected $_pass;
   protected $_nom;
   protected $_prenom;
   protected $_idRole;
}