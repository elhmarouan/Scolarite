<?php

/**
 * Admin controller
 * 
 * @author Stanislas Michalak <stanislas.michalak@gmail.com>
 * 
 */
class AdminController extends PandaController {
   
   public function index() {
      $this->setWindowTitle('Accueil du panel d\'administration');
   }

   public function promotion() {
      if (PandaRequest::getExists('promo')) {
         $this->setWindowTitle('Gestion de la promotion ' . PandaRequest::get('promo'));
         $this->setSubAction('manageClass');
         $this->page()->addVar('promo', htmlspecialchars(stripslashes(PandaRequest::get('promo'))));
      } else {
         $this->setWindowTitle('Gestion des promotions');
         $this->loadModels('Promo');
         $promosList = $this->model('Promo')->findAll();
         foreach($promosList as &$promo) {
            $promo['libelle'] = htmlspecialchars(stripslashes($promo['libelle']));
         }
         $this->page()->addVar('promosList', $promosList);
      }
   }

   public function enseignement() {
      if (PandaRequest::getExists('promo')) {
         $this->page()->addVar('promo', PandaRequest::get('promo'));
         $this->setWindowTitle('Gestion de la promo ' . PandaRequest::get('promo'));
         if (PandaRequest::getExists('module')) {
            $this->page()->addVar('module', PandaRequest::get('module'));
            $this->setWindowTitle('Gestion du module ' . PandaRequest::get('module'));
            if (PandaRequest::getExists('matiere')) {
               $this->page()->addVar('matiere', PandaRequest::get('matiere'));
               $this->setWindowTitle('Gestion de la matière ' . PandaRequest::get('matiere'));
               $this->setSubAction('manageMatiere');
            } else {
               $this->setSubAction('manageModule');
            }
         } else {
            if(PandaRequest::getExists('action') && PandaRequest::get('action') === 'ajouter') {
               $this->setSubAction('addModule');
               echo PandaRequest::post("NameModule");
            } else {
               $modulesList = array(
                   array('name' => 'Informatique'),
                   array('name' => 'Mathématiques'),
                   array('name' => 'Sciences de l\'ingénieur')
               );
               $this->page()->addVar('listeDesModules', $modulesList);
            }
         }
      } else {
         //TODO! Ajouter une notification d'erreur
         PandaResponse::redirect('admin/');
      }
   }

   public function etudiant() {
      if (PandaRequest::getExists('promo')) {
         $this->page()->addVar('promo', PandaRequest::get('promo'));
      } else {
         //TODO! Ajouter une notification d'erreur
         PandaResponse::redirect('admin/');
      }
   }

   public function prof() {
      
   }

}
