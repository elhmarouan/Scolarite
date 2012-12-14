<?php

/**
 * Prof controller
 * 
 * @author Soheil Dahmani <dahmanisou@eisti.eu>
 * 
 */
class ProfController extends PandaController {
   public function index() {
      $this->setWindowTitle('Accueil professeurs');
   }
   
   public function promo() {
     $this->setWindowTitle('Promotions');
   }
   
   public function modules() {
      if (PandaRequest::getExists('promo') && PandaRequest::getExists('module')) {
         $this->setWindowTitle('Gestion des modules de ' . PandaRequest::get('promo'));
         $this->page()->addVar('module', PandaRequest::get('module'));
      }
   }

   public function matiere() {
      if(PandaRequest::getExists('promo') && PandaRequest::getExists('module') && PandaRequest::getExists('matiere')) {
         $this->setWindowTitle('Gestion de ' . PandaRequest::get('matiere'));
         $this->page()->addVar('matiere', PandaRequest::get('matiere'));
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
