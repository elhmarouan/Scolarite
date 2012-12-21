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
      $this->loadModels('Promo');
      if (PandaRequest::getExists('action')) {
         if (PandaRequest::get('action') === 'ajouter') {
            $this->setWindowTitle('Ajouter une promotion');
            $this->setSubAction('addPromo');
         } else {
            $this->app()->user()->addPopup('Désolé, cette action n\'existe pas.', Popup::ERROR);
            PandaResponse::redirect('/admin/promos');
         }
      } else if (PandaRequest::getExists('promo')) {
         if ($this->model('Promo')->exists(array('libelle' => PandaRequest::get('promo')))) {
            $this->setWindowTitle('Gestion de la promotion ' . PandaRequest::get('promo'));
            $this->setSubAction('managePromo');
            $this->page()->addVar('promo', htmlspecialchars(stripslashes(PandaRequest::get('promo'))));
         } else {
            $this->app()->user()->addPopup('Désolé, la promo ' . PandaRequest::get('promo') . ' n\'existe pas.', Popup::ERROR);
            PandaResponse::redirect('/admin/promos');
         }
      } else {
         $this->setWindowTitle('Gestion des promotions');
         $promosList = $this->model('Promo')->field('libelle');
         foreach ($promosList as &$promo) {
            $promo = htmlspecialchars(stripslashes($promo));
         }
         $this->page()->addVar('promosList', $promosList);
      }
   }

   public function enseignement() {
      $this->loadModels('Module', 'Promo', 'Matiere');
      if (PandaRequest::getExists('promo') && $this->model('Promo')->exists(array('libelle' => PandaRequest::get('promo')))) {
         $this->page()->addVar('promo', PandaRequest::get('promo'));
         if (PandaRequest::getExists('module')) {
            $idPromo = $this->model('Promo')->first(array('libelle' => PandaRequest::get('promo')), 'idPromo');
            if ($this->model('Module')->exists(array('libelle' => PandaRequest::get('module'), 'idPromo' => $idPromo))) {
               $this->page()->addVar('module', PandaRequest::get('module'));
               $this->setWindowTitle('Gestion du module ' . PandaRequest::get('module'));
               if (PandaRequest::getExists('matiere')) {
                  $this->page()->addVar('matiere', PandaRequest::get('matiere'));
                  $this->setWindowTitle('Gestion de la matière ' . PandaRequest::get('matiere'));
                  $this->setSubAction('manageMatiere');
               } else if (PandaRequest::getExists('action')) {
                  if (PandaRequest::get('action') === 'modifier') {
                     echo 'test';
                  }
               } else {
                  $this->setSubAction('manageModule');
                  $idModule = $this->model('Module')->first(array('libelle' => PandaRequest::get('module'), 'idPromo' => $idPromo), 'idMod');
                  $matieresList = $this->model('Matiere')->field('libelle', array('idMod' => $idModule));
                  foreach ($matieresList as &$matiere) {
                     $matiere = htmlspecialchars(stripslashes($matiere));
                  }
                  $this->page()->addVar('listeDesMatieres', $matieresList);
               }
            } else {
               //TODO! Ajouter une notification d'erreur
               PandaResponse::redirect('/admin/' . PandaRequest::get('promo') . '/modules');
            }
         } else {
            if (PandaRequest::getExists('action') && PandaRequest::get('action') === 'ajouter') {
               $this->setSubAction('addModule');
               $this->setWindowTitle('Ajouter un module');
               if (PandaRequest::postExists('libelle')) {
                  $module = $this->model('Module');
                  $idPromo = $this->model('Promo')->first(array('libelle' => PandaRequest::get('promo')), 'idPromo');
                  if (!$module->exists(array('idPromo' => $idPromo, 'libelle' => PandaRequest::post('libelle')))) {
                     $module['libelle'] = PandaRequest::post('libelle');
                     $module['idPromo'] = $idPromo;
                     if ($module->save()) {
                        User::addPopup('Le module a bien été ajouté.', Popup::SUCCESS);
                        PandaResponse::redirect('/admin/' . PandaRequest::get('promo') . '/modules');
                     } else {
                        //TODO! Affichage des erreurs
                     }
                  } else {
                     User::addPopup('Un autre module porte déjà ce nom. Veuillez en choisir un autre.', Popup::ERROR);
                  }
               }
            } else {
               if (preg_match('#^[aeiouy]#', PandaRequest::get('promo'))) {
                  $prefixPromo = 'd\'';
               } else {
                  $prefixPromo = 'de ';
               }
               $this->page()->addVar('prefixPromo', $prefixPromo);
               $this->setWindowTitle('Gestion des modules ' . $prefixPromo . PandaRequest::get('promo'));
               //Récupèration de la liste des modules correspondants à la promo
               $modulesList = $this->model('Module')->field('libelle', array('idPromo' => $this->model('Promo')->first(array('libelle' => PandaRequest::get('promo')), 'idPromo')));
               foreach ($modulesList as &$module) {
                  $module = htmlspecialchars(stripslashes($module));
               }
               $this->page()->addVar('listeDesModules', $modulesList);
            }
         }
      } else {
         //TODO! Ajouter une notification d'erreur
         PandaResponse::redirect('/admin/');
      }
   }

   public function etudiant() {
      if (PandaRequest::getExists('promo')) {
         if (preg_match('#^[aeiouy]#', PandaRequest::get('promo'))) {
            $prefixPromo = 'd\'';
         } else {
            $prefixPromo = 'de ';
         }
         $this->setWindowTitle('Gestion des étudiants' . $prefixPromo . PandaRequest::get('promo'));
         $this->page()->addVar('prefixPromo', $prefixPromo);
         $this->page()->addVar('promo', PandaRequest::get('promo'));
      } else {
         //TODO! Ajouter une notification d'erreur
         PandaResponse::redirect('/admin/');
      }
   }

   public function prof() {
      
   }

}
