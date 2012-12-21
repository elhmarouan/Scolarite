<?php

/**
 * Éleve controller
 * 
 * @author Vincent Simon <simonvince@eisti.eu>
 * 
 */
class EleveController extends PandaController {
   public function index() {
      $this->setWindowTitle('Accueil élève');      
   }
   
   public function perso() {      
      $this->setWindowTitle('Informations personnelles');
   }
   
   public function promotion() {
      if (PandaRequest::getExists('promo')) {
         $this->setWindowTitle('Gestion de la promotion ' . PandaRequest::get('promo'));
         $this->setSubAction('manageClass');
         $this->page()->addVar('promo', stripslashes(htmlspecialchars(PandaRequest::get('promo'))));
      } else {
         $this->setWindowTitle('Gestion des promotions');
      }
   }
   
 
}
/*public function notes () {
 * }
 *pubic function moyennes () {
 * }
 */

