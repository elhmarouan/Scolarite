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

   public function utilisateur() {
      if (PandaRequest::getExists('action') && PandaRequest::get('action') === 'ajouter') {
         $this->setSubAction('addUser');
         $this->setWindowTitle('Ajouter un utilisateur');
      } else {
         $this->setWindowTitle('Gestion des utilisateurs');
         $this->page()->addVar('listeDesUtilisateurs', self::model('Utilisateur')->findAll('login', 'nom', 'prenom'));
      }
   }
   
   public function promotion() {
      if (PandaRequest::getExists('action')) {
         if (PandaRequest::get('action') === 'ajouter') {
            $this->setWindowTitle('Ajouter une promotion');
            $this->setSubAction('addPromo');
            if (PandaRequest::postExists('libelle')) {
                  $promo = self::model('Promo');
                  if (!$promo->exists(array('libelle' => PandaRequest::post('libelle')))) {
                     $promo['libelle'] = PandaRequest::post('libelle');
                     if ($promo->save()) {
                        User::addPopup('La promotion a bien été ajoutée.', Popup::SUCCESS);
                        PandaResponse::redirect('/admin/promos');
                     } else {
                        //Récupération et affichage des erreurs
                        $erreurs = $promo->errors();
                        foreach ($erreurs as $erreurId) {
                           switch ($erreurId) {
                              case PromoModel::BAD_LIBELLE_ERROR:
                                 User::addPopup('Le nom de la promotion est invalide.', Popup::ERROR);
                                 break;
                           }
                        }
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
         //Si la promotion existe
         if (self::model('Promo')->exists(array('libelle' => PandaRequest::get('promo')))) {
            $this->setWindowTitle('Gestion de la promotion ' . PandaRequest::get('promo'));
            $this->setSubAction('managePromo');
            $this->page()->addVar('promo', htmlspecialchars(stripslashes(PandaRequest::get('promo'))));
         } else {
            $this->app()->user()->addPopup('Désolé, la promo « ' . PandaRequest::get('promo') . ' » n\'existe pas.', Popup::ERROR);
            PandaResponse::redirect('/admin/promos');
         }
      } else {
         //Par défaut, on affiche la liste des promotions
         $this->setWindowTitle('Gestion des promotions');
         $promosList = self::model('Promo')->field('libelle');
         foreach ($promosList as &$promo) {
            $promo = htmlspecialchars(stripslashes($promo));
         }
         $this->page()->addVar('promosList', $promosList);
      }
   }

   public function enseignement() {
      if (PandaRequest::getExists('promo') && self::model('Promo')->exists(array('libelle' => PandaRequest::get('promo')))) {
         $this->page()->addVar('promo', PandaRequest::get('promo'));
         if (PandaRequest::getExists('module')) {
            $idPromo = self::model('Promo')->first(array('libelle' => PandaRequest::get('promo')), 'idPromo');
            //Si le module existe (le libelle existe et correspond à la promo actuelle)
            if (self::model('Module')->exists(array('libelle' => PandaRequest::get('module'), 'idPromo' => $idPromo))) {
               $idModule = self::model('Module')->first(array('libelle' => PandaRequest::get('module'), 'idPromo' => $idPromo), 'idMod');
               $this->page()->addVar('module', PandaRequest::get('module'));
               $this->setWindowTitle('Gestion du module ' . PandaRequest::get('module'));
               if (PandaRequest::getExists('matiere')) {
                  //Si la matière existe (le libelle existe et correspond au module actuel)
                  if(self::model('Matiere')->exists(array('libelle' => PandaRequest::get('matiere'), 'idMod' => $idModule))) {
                     $this->page()->addVar('matiere', PandaRequest::get('matiere'));
                     $this->page()->addVar('coef', number_format(self::model('Matiere')->first(array('libelle' => PandaRequest::get('matiere'), 'idMod' => $idModule), 'coefMat'), 2, ',', ' '));
                     $this->setWindowTitle('Gestion de la matière ' . PandaRequest::get('matiere'));
                     $this->setSubAction('manageMatiere');
                  } else {
                     User::addPopup('La matière « ' . PandaRequest::get('matiere') . ' » n\'existe pas.', Popup::ERROR);
                     PandaResponse::redirect('/admin/' . PandaRequest::get('promo') . '/' . PandaRequest::get('module') . '/matières');
                  }
               } else if (PandaRequest::getExists('action')) {
                  $module = self::model('Module');
                  if (PandaRequest::get('action') === 'ajouter') {
                     /**
                     * Ajout d'une matière
                     */
                     $this->setSubAction('addMatiere');
                     $this->setWindowTitle('Ajouter une matière');
                     $this->page()->addVar('listeProfsResponsables', self::model('Prof')->findAll());
                     //Si le formulaire a été bien été envoyé
                     if (PandaRequest::postExists('libelle', 'coef')) {
                        $matiere = self::model('Matiere');
                        //On vérifie si une autre matière ne porte pas déjà le même nom dans le module concerné
                        if (!$matiere->exists(array('idMod' => $idModule, 'libelle' => PandaRequest::post('libelle')))) {
                           $matiere['idMod'] = $idModule;
                           $matiere['libelle'] = PandaRequest::post('libelle');
                           $matiere['coefMat'] = PandaRequest::post('coef');
                           if ($matiere->save()) {
                              User::addPopup('La matière a bien été ajoutée.', Popup::SUCCESS);
                              PandaResponse::redirect('/admin/' . PandaRequest::get('promo') . '/' . PandaRequest::get('module') . '/matières');
                           } else {
                              //Récupération et affichage des erreurs
                              $erreurs = $matiere->errors();
                              foreach ($erreurs as $erreurId) {
                                 switch ($erreurId) {
                                    case MatiereModel::BAD_COEF_MAT_ERROR:
                                       User::addPopup('Le coefficient est invalide.', Popup::ERROR);
                                       break;
                                    case MatiereModel::BAD_LIBELLE_ERROR:
                                       User::addPopup('Le nom de la matière est invalide.', Popup::ERROR);
                                       break;
                                    case MatiereModel::BAD_ID_PROF_ERROR:
                                       User::addPopup('Le professeur que vous avez nommé responsable n\'existe pas.', Popup::ERROR);
                                       break;
                                 }
                              }
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
                           //Récupération et affichage des erreurs
                           $erreurs = $module->errors();
                           foreach ($erreurs as $erreurId) {
                              switch($erreurId) {
                                 case ModuleModel::BAD_LIBELLE_ERROR:
                                    User::addPopup('Le nom du module est invalide.', Popup::ERROR);
                                    break;
                              }
                           }
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
                  $matieresList = self::model('Matiere')->field('libelle', array('idMod' => $idModule));
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
                  $module = self::model('Module');
                  $idPromo = self::model('Promo')->first(array('libelle' => PandaRequest::get('promo')), 'idPromo');
                  if (!$module->exists(array('idPromo' => $idPromo, 'libelle' => PandaRequest::post('libelle')))) {
                     $module['libelle'] = PandaRequest::post('libelle');
                     $module['idPromo'] = $idPromo;
                     if ($module->save()) {
                        User::addPopup('Le module a bien été ajouté.', Popup::SUCCESS);
                        PandaResponse::redirect('/admin/' . PandaRequest::get('promo') . '/modules');
                     } else {
                        //Récupération et affichage des erreurs
                        $erreurs = $module->errors();
                        foreach ($erreurs as $erreurId) {
                           switch($erreurId) {
                              case ModuleModel::BAD_LIBELLE_ERROR:
                                 User::addPopup('Le nom du module est invalide.', Popup::ERROR);
                                 break;
                           }
                        }
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
               $modulesList = self::model('Module')->field('libelle', array('idPromo' => self::model('Promo')->first(array('libelle' => PandaRequest::get('promo')), 'idPromo')));
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
         if (self::model('Promo')->exists(array('libelle' => PandaRequest::get('promo')))) {
            $this->page()->addVar('promo', PandaRequest::get('promo'));
            if (PandaRequest::getExists('action') && PandaRequest::get('action') === 'ajouter') {
               $this->setSubAction('addStudent');
               $this->setWindowTitle('Ajouter un étudiant');
            } else {
               if (preg_match('#^[aeiouy]#', PandaRequest::get('promo'))) {
                  $prefixPromo = 'd\'';
               } else {
                  $prefixPromo = 'de ';
               }
               $this->setWindowTitle('Gestion des étudiants ' . $prefixPromo . PandaRequest::get('promo'));
               $this->page()->addVar('prefixPromo', $prefixPromo);
            }
         } else {
            User::addPopup('Cette promotion n\'existe pas.', Popup::ERROR);
            PandaResponse::redirect('/admin/');
         }
      } else {
         User::addPopup('Veuillez sélectionner une promotion pour commencer.', Popup::ERROR);
         PandaResponse::redirect('/admin/');
      }
   }

   public function prof() {

   }

}
