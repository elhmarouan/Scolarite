<?php

/**
 * Prof controller
 * 
 * @author Soheil Dahmani <dahmanisou@eisti.eu>
 * 
 */
class ProfController extends Controller {
   
   public function accessFilter() {
      if (User::isMemberOf('Professeur')) {
         return true;
      } else {
         User::addPopup('Vous n\'êtes pas autorisé à accéder à la section professeur.', Popup::ERROR);
         HTTPResponse::redirect('/');
      }
   }
   
   public function index() {
      $this->setWindowTitle('Accueil professeurs');
   }
   
   public function promo() {
      if (HTTPRequest::getExists('promo')) {
         $this->setWindowTitle('Promotions');
         $this->addVar('promo', HTTPRequest::get('promo'));
      }
   }
   
   public function modules() {
      if (HTTPRequest::getExists('promo') && HTTPRequest::getExists('module')) {
         $this->setWindowTitle('Gestion des modules de ' . HTTPRequest::get('promo'));
         $this->addVar('module', HTTPRequest::get('module'));
      }
   }

   public function matiere() {
      if(HTTPRequest::getExists('promo') && HTTPRequest::getExists('module') && HTTPRequest::getExists('matiere')) {
         $this->setWindowTitle('Gestion de ' . HTTPRequest::get('matiere'));
         $this->addVar('matiere', HTTPRequest::get('matiere'));
      }
   }
   
   public function student() {
      $this->setWindowTitle('Étudiants');
   }
   
   /*public function ajouterNotes() {
      $this->setWindowTitle('Saisie des notes: promo');
   }   
   
   public function modules() {
      $this->setWindowTitle('Saisie des notes: modules');
   }
   
   public function maths() {
      $this->setWindowTitle('Modules: Mathématiques')
   }*/
 }
