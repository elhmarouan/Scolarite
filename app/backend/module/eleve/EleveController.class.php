<?php

/**
 * Éleve controller
 * 
 * @author Vincent Simon <simonvince@eisti.eu>
 * 
 */
class EleveController extends Controller {

   public function accessFilter() {
      if (User::isMemberOf('Élève')) {
         return true;
      } else {
         User::addPopup('Vous n\'êtes pas autorisé à accéder à la section élève.', Popup::ERROR);
         HTTPResponse::redirect('/');
      }
   }

   public function index() {
      $this->setWindowTitle('Accueil élève');
   }

   public function perso() {
      $this->setWindowTitle('Informations personnelles');
   }

   public function promotion() {
      if (HTTPRequest::getExists('promo')) {
         $this->setWindowTitle('Gestion de la promotion ' . HTTPRequest::get('promo'));
         $this->setSubAction('manageClass');
         $this->addVar('promo', stripslashes(htmlspecialchars(HTTPRequest::get('promo'))));
      } else {
         $this->setWindowTitle('Gestion des promotions');
      }
   }

   public function matieres() {
      if (HTTPRequest::getExists('promo') && HTTPRequest::getExists('module') && HTTPRequest::getExists('matiere')) {
         $this->setWindowTitle('Gestion de ' . HTTPRequest::get('matiere'));
         $this->addVar('matiere', HTTPRequest::get('matiere'));
      }
   }

   public function notes() {
      
   }

   public function moyennes() {
      
   }

}