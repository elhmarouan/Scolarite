<?php

/**
 * Admin controller
 *
 * Contrôleur du module réservé aux administrateurs
 * 
 * @author Stanislas Michalak <stanislas.michalak@gmail.com>
 *
 */
class AdminController extends PandaController {

   /**
    * Vérifie si l'utilisateur connecté est un administrateur,
    * et autorise ou non l'accès au module.
    * @return boolean
    */
   public function accessFilter() {
      if (User::isMemberOf('Administrateur')) {
         return true;
      } else {
         User::addPopup('Vous n\'êtes pas autorisé à accéder à la section administrateur.', Popup::ERROR);
         PandaResponse::redirect('/');
      }
   }
   
   /**
    * Page d'accueil du module admin
    */
   public function index() {
      $this->setWindowTitle('Accueil du panel d\'administration');
   }

   /**
    * Gestion des utilisateurs
    */
   public function utilisateur() {
      //Si l'on demande à ajouter un utilisateur
      if (PandaRequest::getExists('action') && PandaRequest::get('action') === 'ajouter') {
         //Si le formulaire d'ajout a été posté
         if (PandaRequest::postExists('nom', 'prenom', 'role', 'login', 'password', 'passwordConfirm')) {
            if (self::model('Role')->exists(array('idRole' => PandaRequest::post('role')))) {
               $utilisateur = self::model('Utilisateur');
               //On vérifie que le login n'est pas déjà utilisé
               if (!$utilisateur->exists(array('login' => PandaRequest::post('login')))) {
                  if (PandaRequest::post('password') === PandaRequest::post('passwordConfirm')) {
                     $utilisateur['login'] = PandaRequest::post('login');
                     $utilisateur['pass'] = __hash(PandaRequest::post('password'), Config::read('salt.user.prefix'), Config::read('salt.user.suffix'));
                     $utilisateur['nom'] = PandaRequest::post('nom');
                     $utilisateur['prenom'] = PandaRequest::post('prenom');
                     $utilisateur['idRole'] = PandaRequest::post('role');
                     if ($utilisateur->save()) {
                        User::addPopup('L\'utilisateur a bien été ajouté.', Popup::SUCCESS);
                        PandaResponse::redirect('/admin/utilisateurs');
                     } else {
                        //Récupération et affichage des erreurs
                        $erreurs = $utilisateur->errors();
                        foreach ($erreurs as $erreurId) {
                           switch ($erreurId) {
                              case UtilisateurModel::BAD_LOGIN_ERROR:
                                 User::addPopup('Login incorrect.', Popup::ERROR);
                                 break;
                              case UtilisateurModel::BAD_PASS_ERROR:
                                 User::addPopup('Mot de passe incorrect (7 caractères minimum).', Popup::ERROR);
                                 break;
                              case UtilisateurModel::BAD_NOM_ERROR:
                                 User::addPopup('Nom incorrect.', Popup::ERROR);
                                 break;
                              case UtilisateurModel::BAD_PRENOM_ERROR:
                                 User::addPopup('Prenom incorrect.', Popup::ERROR);
                                 break;
                           }
                        }
                     }
                  } else {
                     User::addPopup('Les deux mots de passe renseignés ne correspondent pas.', Popup::ERROR);
                  }
               } else {
                  User::addPopup('Le login renseigné appartient déjà à un autre utilisateur.', Popup::ERROR);
               }
            } else {
               User::addPopup('Le rôle renseigné n\'existe pas.', Popup::ERROR);
            }
         }
         $this->setSubAction('addUser');
         $this->setWindowTitle('Ajouter un utilisateur');
         $listeDesRoles = self::model('Role')->findAll();
         foreach ($listeDesRoles as &$role) {
            $role['libelle'] = htmlspecialchars(stripslashes($role['libelle']));
         }
         $this->page()->addVar('listeDesRoles', $listeDesRoles);
      } else if (PandaRequest::getExists('idUtil')) {
         $utilisateur = self::model('Utilisateur');
         if ($utilisateur->exists(array('idUtil' => PandaRequest::get('idUtil')))) {
            if (PandaRequest::getExists('action') && PandaRequest::get('action') === 'modifier') {
               //TODO! Formulaire de modification
            } else if (PandaRequest::getExists('action') && PandaRequest::get('action') === 'supprimer') {
               $utilisateur->delete(array('idUtil' => PandaRequest::get('idUtil')));
               User::addPopup('L\'utilisateur a bien été supprimé.', Popup::SUCCESS);
               PandaResponse::redirect('/admin/utilisateurs');
            } else {
               User::addPopup('Cette action n\'existe pas.', Popup::ERROR);
               PandaResponse::redirect('/admin/utilisateurs');
            }
         } else {
            User::addPopup('Cet utilisateur n\'existe pas.', Popup::ERROR);
            PandaResponse::redirect('/admin/utilisateurs');
         }
      } else {
         $this->setWindowTitle('Gestion des utilisateurs');
         $listeDesUtilisateurs = self::model('Utilisateur')->findAll('idUtil', 'login', 'nom', 'prenom', 'idRole');
         foreach ($listeDesUtilisateurs as &$utilisateur) {
            $utilisateur['login'] = htmlspecialchars(stripslashes($utilisateur['login']));
            $utilisateur['nom'] = htmlspecialchars(stripslashes($utilisateur['nom']));
            $utilisateur['prenom'] = htmlspecialchars(stripslashes($utilisateur['prenom']));
            $utilisateur['role'] = htmlspecialchars(stripslashes(self::model('Role')->first(array('idRole' => $utilisateur['idRole']), 'libelle')));
         }
         $this->page()->addVar('listeDesUtilisateurs', $listeDesUtilisateurs);
      }
   }
   
   /**
    * Gestion des promotions
    */
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

   /**
    * Gestion des enseignements rattachés à un promotion
    * @see promotion
    */
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

   /**
    * Gestion des étudiants rattachés à une promotion.
    * @see promotion
    */
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

   /**
    * Gestion des professeurs
    */
   public function prof() {

   }

}
