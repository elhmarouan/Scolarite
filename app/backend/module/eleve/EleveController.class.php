<?php

/**
 * Éleve controller
 * 
 * @author Vincent Simon <simonvince@eisti.eu>
 * 
 */
class EleveController extends PandaController {

   public function accessFilter() {
      if (User::isMemberOf('Élève')) {
         return true;
      } else {
         User::addPopup('Vous n\'êtes pas autorisé à accéder à la section élève.', Popup::ERROR);
         PandaResponse::redirect('/');
      }
   }

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

   public function matieres() {
      if (PandaRequest::getExists('promo') && PandaRequest::getExists('module') && PandaRequest::getExists('matiere')) {
         $this->setWindowTitle('Gestion de ' . PandaRequest::get('matiere'));
         $this->page()->addVar('matiere', PandaRequest::get('matiere'));
      }
   }

   public function notes() {
      
   }

   public function moyennes() {
      
   }

}