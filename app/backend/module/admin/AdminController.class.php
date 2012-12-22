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
            if (PandaRequest::postExists('libelle')) {
                  $promo = $this->model('Promo');
                  if (!$promo->exists(array('libelle' => PandaRequest::post('libelle')))) {
                     $promo['libelle'] = PandaRequest::post('libelle');
                     if ($promo->save()) {
                        User::addPopup('La promotion a bien été ajoutée.', Popup::SUCCESS);
                        PandaResponse::redirect('/admin/promos');
                     } else {
                        //TODO! Affichage des erreurs
                     }
                  } else {
                     User::addPopup('Une autre promo porte déjà ce nom. Veuillez en choisir un autre.', Popup::ERROR);
                  }
               }
         } else  {
            $this->app()->user()->addPopup('Désolé, cette action n\'existe pas.', Popup::ERROR);
            PandaResponse::redirect('/admin/promos');
         }
      } else if (PandaRequest::getExists('promo')) {
         if ($this->model('Promo')->exists(array('libelle' => PandaRequest::get('promo')))) {
            $this->setWindowTitle('Gestion de la promotion ' . PandaRequest::get('promo'));
            $this->setSubAction('managePromo');
            $this->page()->addVar('promo', htmlspecialchars(stripslashes(PandaRequest::get('promo'))));
         } else {
            $this->app()->user()->addPopup('Désolé, la promo « ' . PandaRequest::get('promo') . ' » n\'existe pas.', Popup::ERROR);
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
            //Si le module existe (le libelle existe et correspond à la promo actuelle)
            if ($this->model('Module')->exists(array('libelle' => PandaRequest::get('module'), 'idPromo' => $idPromo))) {
               $idModule = $this->model('Module')->first(array('libelle' => PandaRequest::get('module'), 'idPromo' => $idPromo), 'idMod');
               $this->page()->addVar('module', PandaRequest::get('module'));
               $this->setWindowTitle('Gestion du module ' . PandaRequest::get('module'));
               if (PandaRequest::getExists('matiere')) {
                  $this->page()->addVar('matiere', PandaRequest::get('matiere'));
                  $this->setWindowTitle('Gestion de la matière ' . PandaRequest::get('matiere'));
                  $this->setSubAction('manageMatiere');
               } else if (PandaRequest::getExists('action')) {
                  $module = $this->model('Module');
                  if (PandaRequest::get('action') === 'ajouter') {
                     /**
                     * Ajout d'une matière
                     */
                     $this->setSubAction('addMatiere');
                     $this->setWindowTitle('Ajouter une matière');
                     //Si le formulaire a été bien été envoyé
                     if (PandaRequest::postExists('libelle', 'coef')) {
                        $matiere = $this->model('Matiere');
                        //On vérifie si une autre matière ne porte pas déjà le même nom dans le module concerné
                        if (!$matiere->exists(array('idMod' => $idModule, 'libelle' => PandaRequest::post('libelle')))) {
                           $matiere['idMod'] = $idModule;
                           $matiere['libelle'] = PandaRequest::post('libelle');
                           $matiere['coefMat'] = PandaRequest::post('coef');
                           if ($matiere->save()) {
                              User::addPopup('La matière a bien été ajoutée.', Popup::SUCCESS);
                              PandaResponse::redirect('/admin/' . PandaRequest::get('promo') . '/' . PandaRequest::get('module') . '/matières');
                           } else {
                              //TODO! Affichage des erreurs
                           }
                        } else {
                           User::addPopup('Une autre matière porte déjà ce nom. Veuillez en choisir un autre.', Popup::ERROR);
                        }
                     }
                  } else if (PandaRequest::get('action') === 'modifier') {
                     /**
                     * Modification d'un module
                     */
                     $this->setSubAction('editModule');
                     $this->setWindowTitle('Modifier un module');
                     //Si le formulaire a été bien été envoyé
                     if (PandaRequest::postExists('libelle')) {
                        $module['idMod'] = $idModule;
                        $module['libelle'] = PandaRequest::post('libelle');
                        if ($module->save()) {
                           User::addPopup('Le module a bien été modifié.', Popup::SUCCESS);
                           PandaResponse::redirect('/admin/' . PandaRequest::get('promo') . '/modules');
                        } else {
                           //TODO! Affichage des erreurs
                        }
                     }
                  } else if (PandaRequest::get('action') === 'supprimer') {
                     /**
                     * Suppression d'un module
                     */
                     $module->delete(array('idMod' => $idModule));
                     User::addPopup('Le module a bien été supprimé.', Popup::SUCCESS);
                     PandaResponse::redirect('/admin/' . PandaRequest::get('promo') . '/modules');
                  }
               } else {
                  $this->setSubAction('manageModule');
                  $matieresList = $this->model('Matiere')->field('libelle', array('idMod' => $idModule));
                  foreach ($matieresList as &$matiere) {
                     $matiere = htmlspecialchars(stripslashes($matiere));
                  }
                  $this->page()->addVar('listeDesMatieres', $matieresList);
               }
            } else {
               User::addPopup('Le module « ' . PandaRequest::get('module') . ' » n\'existe pas.', Popup::ERROR);
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
         User::addPopup('Désolé, la promo « ' . PandaRequest::get('promo') . ' » n\'existe pas.', Popup::ERROR);
         PandaResponse::redirect('/admin/promos');
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
