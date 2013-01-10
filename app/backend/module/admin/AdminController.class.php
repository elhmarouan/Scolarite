<?php

/**
 * Admin controller
 *
 * Contrôleur du module réservé aux administrateurs
 * 
 * @author Stanislas Michalak <stanislas.michalak@gmail.com>
 *
 */
class AdminController extends Controller {

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
         HTTPResponse::redirect('/');
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
      if (HTTPRequest::getExists('action') && HTTPRequest::get('action') === 'ajouter') {
         //Si le formulaire d'ajout a été posté
         if (HTTPRequest::postExists('nom', 'prenom', 'role', 'login', 'password', 'passwordConfirm')) {
            if (self::model('Role')->exists(array('idRole' => HTTPRequest::post('role')))) {
               $utilisateur = self::model('Utilisateur');
               //On vérifie que le login n'est pas déjà utilisé
               if (!$utilisateur->exists(array('login' => HTTPRequest::post('login')))) {
                  if (HTTPRequest::post('password') === HTTPRequest::post('passwordConfirm')) {
                     $utilisateur['login'] = HTTPRequest::post('login');
                     $utilisateur['pass'] = __hash(HTTPRequest::post('password'), Config::read('salt.user.prefix'), Config::read('salt.user.suffix'));
                     $utilisateur['nom'] = HTTPRequest::post('nom');
                     $utilisateur['prenom'] = HTTPRequest::post('prenom');
                     $utilisateur['idRole'] = HTTPRequest::post('role');
                     if ($utilisateur->save()) {
                        $idUtil = $utilisateur->lastInsertId();
                        //Si le nouvel utilisateur est un professeur
                        if ((int) HTTPRequest::post('role') === 2) {
                           $prof = self::model('Prof');
                           //On vérifie que les champs nécessaires existent
                           if (HTTPRequest::postExists('numBureau', 'telBureau')) {
                              $prof['idUtil'] = $idUtil;
                              $prof['numBureau'] = HTTPRequest::post('numBureau');
                              $prof['telBureau'] = HTTPRequest::post('telBureau');
                              //Si la création du prof échoue
                              if (!$prof->save()) {
                                 //On supprime la nouvelle fiche utilisateur
                                 $utilisateur->delete(array('idUtil' => $idUtil));
                                 //Et on récupère les erreurs
                                 $erreurs = $prof->errors();
                                 foreach ($erreurs as $erreurId) {
                                    switch ($erreurId) {
                                       case ProfModel::BAD_NUM_BUREAU_ERROR:
                                          User::addPopup('Numéro de bureau invalide.', Popup::ERROR);
                                          break;
                                       case ProfModel::BAD_TEL_BUREAU_ERROR;
                                          User::addPopup('Numéro de téléphone invalide.', Popup::ERROR);
                                          break;
                                    }
                                 }
                              }  
                           } else {
                              $utilisateur->delete(array('idUtil' => $idUtil));
                              User::addPopup('Les informations nécessaires à la création du profil n\'ont pas été renseignées.', Popup::ERROR);
                           }
                        } else if ((int) HTTPRequest::post('role') === 3) {
                           $eleve = self::model('Eleve');
                           //On vérifie que les champs nécessaires existent
                           if (HTTPRequest::postExists('numEtudiant', 'anneeRedouble')) {
                              $eleve['idUtil'] = $idUtil;
                              $eleve['numEtudiant'] = HTTPRequest::post('numEtudiant');
                              $eleve['anneeRedouble'] = HTTPRequest::post('anneeRedouble');
                              //Si la création de l'élève échoue
                              if (!$eleve->save()) {
                                 //On supprime la nouvelle fiche utilisateur
                                 $utilisateur->delete(array('idUtil' => $idUtil));
                                 //Et on récupère les erreurs
                                 $erreurs = $eleve->errors();
                                 foreach ($erreurs as $erreurId) {
                                    switch ($erreurId) {
                                       case EleveModel::BAD_NUM_ETUDIANT_ERROR:
                                          User::addPopup('Numéro d\'étudiant invalide.', Popup::ERROR);
                                          break;
                                       case EleveModel::BAD_ANNEE_REDOUBLE_ERROR;
                                          User::addPopup('Année de redoublement invalide.', Popup::ERROR);
                                          break;
                                    }
                                 }
                              } 
                           } else {
                              $utilisateur->delete(array('idUtil' => $idUtil));
                              User::addPopup('Les informations nécessaires à la création du profil n\'ont pas été renseignées.', Popup::ERROR);
                           }
                        }
                        User::addPopup('L\'utilisateur a bien été ajouté.', Popup::SUCCESS);
                        HTTPResponse::redirect('/admin/utilisateurs');
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
         $this->addVar('listeDesRoles', $listeDesRoles);
      } else if (HTTPRequest::getExists('idUtil')) {
         $utilisateur = self::model('Utilisateur');
         if ($utilisateur->exists(array('idUtil' => HTTPRequest::get('idUtil')))) {
            //Si l'on demande à modifier un utilisateur
            if (HTTPRequest::getExists('action') && HTTPRequest::get('action') === 'modifier') {
               //Si le formulaire de modification a été posté
               if (HTTPRequest::postExists('nom', 'prenom', 'role', 'login', 'password', 'passwordConfirm')) {
                  if (self::model('Role')->exists(array('idRole' => HTTPRequest::post('role')))) {
                     $utilisateur = self::model('Utilisateur');
                     //On vérifie que le login est identique à l'actuel, ou qu'il n'est pas déjà utilisé.
                     if ($utilisateur->exists(array('idUtil' => HTTPRequest::get('idUtil'), 'login' => HTTPRequest::post('login'))) || !$utilisateur->exists(array('login' => HTTPRequest::post('login')))) {
                        $utilisateur['idUtil'] = HTTPRequest::get('idUtil');
                        $utilisateur['login'] = HTTPRequest::post('login');
                        $utilisateur['nom'] = HTTPRequest::post('nom');
                        $utilisateur['prenom'] = HTTPRequest::post('prenom');
                        $utilisateur['idRole'] = HTTPRequest::post('role');
                        if (HTTPRequest::post('password') !== '') {
                           if (HTTPRequest::post('password') === HTTPRequest::post('passwordConfirm')) {
                              $utilisateur['pass'] = __hash(HTTPRequest::post('password'), Config::read('salt.user.prefix'), Config::read('salt.user.suffix'));
                           } else {
                              User::addPopup('Les deux mots de passe renseignés ne correspondent pas.', Popup::ERROR);
                              $badPassword = true;
                           }
                        }
                        if (!$badPassword && $utilisateur->save()) {
                           //TODO! Traitement des cas particuliers (prof et élève)
                           User::addPopup('L\'utilisateur a bien été modifié.', Popup::SUCCESS);
                           HTTPResponse::redirect('/admin/utilisateurs');
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
                        User::addPopup('Le login renseigné est déjà utilisé.', Popup::ERROR);
                     }
                  } else {
                     User::addPopup('Le rôle renseigné n\'existe pas.', Popup::ERROR);
                  }
               }
               $this->setWindowTitle('Modifier un utilisateur');
               $this->setSubAction('editUser');
               $listeDesRoles = self::model('Role')->findAll();
               foreach ($listeDesRoles as &$role) {
                  $role['libelle'] = htmlspecialchars(stripslashes($role['libelle']));
               }
               $this->addVar('listeDesRoles', $listeDesRoles);
               $utilisateur = self::model('Utilisateur')->first(array('idUtil' => HTTPRequest::get('idUtil')));
               $utilisateur['nom'] = htmlspecialchars(stripslashes($utilisateur['nom']));
               $utilisateur['prenom'] = htmlspecialchars(stripslashes($utilisateur['prenom']));
               //Récupération des données propres au rôle
               if ((int) $utilisateur['idRole'] === 2) {
                  //Si l'utilisateur est un professeur
                  $prof = self::model('Prof')->first(array('idUtil' => $utilisateur['idUtil']));
                  $utilisateur['numBureau'] = $prof['numBureau'];
                  $utilisateur['telBureau'] = $prof['telBureau'];
               } else if ((int) $utilisateur['idRole'] === 3) {
                  //Si l'utilisateur est un étudiant
                  $etudiant = self::model('Eleve')->first(array('idUtil' => $utilisateur['idUtil']));
                  $utilisateur['numEtudiant'] = $etudiant['numEtudiant'];
                  $utilisateur['anneeRedouble'] = $etudiant['anneeRedouble'];
               }
               $this->addVar('utilisateur', $utilisateur);
            } else if (HTTPRequest::getExists('action') && HTTPRequest::get('action') === 'supprimer') {
               if ((int) HTTPRequest::get('idUtil') !== User::id()) {
                  $idRole = (int) $utilisateur->first(array('idUtil' => HTTPRequest::get('idUtil')), 'idRole');
                  //Si l'utilisateur était un prof ou un élève, on supprime l'entrée associée
                  if ($idRole === 2) {
                     self::model('Prof')->delete(array('idUtil' => HTTPRequest::get('idUtil')));
                  } else if ($idRole === 3) {
                     self::model('Eleve')->delete(array('idUtil' => HTTPRequest::get('idUtil')));
                  }
                  $utilisateur->delete(array('idUtil' => HTTPRequest::get('idUtil')));
                  User::addPopup('L\'utilisateur a bien été supprimé.', Popup::SUCCESS);
               } else {
                  User::addPopup('Impossible de supprimer votre propre compte. Si vous voulez vraiment le faire, faîtes-en la demande à un autre administrateur.', Popup::ERROR);
               }
               HTTPResponse::redirect('/admin/utilisateurs');
            } else {
               User::addPopup('Cette action n\'existe pas.', Popup::ERROR);
               HTTPResponse::redirect('/admin/utilisateurs');
            }
         } else {
            User::addPopup('Cet utilisateur n\'existe pas.', Popup::ERROR);
            HTTPResponse::redirect('/admin/utilisateurs');
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
         $this->addVar('listeDesUtilisateurs', $listeDesUtilisateurs);
      }
   }

   /**
    * Gestion des promotions
    */
   public function promotion() {
      if (HTTPRequest::getExists('action') && HTTPRequest::get('action') === 'ajouter') {
         $this->setWindowTitle('Ajouter une promotion');
         $this->setSubAction('addPromo');
         if (HTTPRequest::postExists('libelle')) {
            $promo = self::model('Promo');
            if (!$promo->exists(array('libelle' => HTTPRequest::post('libelle')))) {
               $promo['libelle'] = HTTPRequest::post('libelle');
               if ($promo->save()) {
                  User::addPopup('La promotion a bien été ajoutée.', Popup::SUCCESS);
                  HTTPResponse::redirect('/admin/promos');
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
      } else if (HTTPRequest::getExists('promo')) {
         //Si la promotion existe
         if (self::model('Promo')->exists(array('libelle' => HTTPRequest::get('promo')))) {
            $this->setWindowTitle('Gestion de la promotion ' . HTTPRequest::get('promo'));
            $this->setSubAction('managePromo');
            $this->addVar('promo', htmlspecialchars(stripslashes(HTTPRequest::get('promo'))));
         } else {
            $this->app()->user()->addPopup('Désolé, la promo « ' . HTTPRequest::get('promo') . ' » n\'existe pas.', Popup::ERROR);
            HTTPResponse::redirect('/admin/promos');
         }
      } else {
         //Par défaut, on affiche la liste des promotions
         $this->setWindowTitle('Gestion des promotions');
         $promosList = self::model('Promo')->field('libelle');
         foreach ($promosList as &$promo) {
            $promo = htmlspecialchars(stripslashes($promo));
         }
         $this->addVar('promosList', $promosList);
      }
   }

   /**
    * Gestion des enseignements rattachés à un promotion
    * @see promotion
    */
   public function enseignement() {
      if (HTTPRequest::getExists('promo') && self::model('Promo')->exists(array('libelle' => HTTPRequest::get('promo')))) {
         $this->addVar('promo', HTTPRequest::get('promo'));
         if (HTTPRequest::getExists('module')) {
            $idPromo = self::model('Promo')->first(array('libelle' => HTTPRequest::get('promo')), 'idPromo');
            //Si le module existe (le libelle existe et correspond à la promo actuelle)
            if (self::model('Module')->exists(array('libelle' => HTTPRequest::get('module'), 'idPromo' => $idPromo))) {
               $idModule = self::model('Module')->first(array('libelle' => HTTPRequest::get('module'), 'idPromo' => $idPromo), 'idMod');
               $this->addVar('module', HTTPRequest::get('module'));
               $this->setWindowTitle('Gestion du module ' . HTTPRequest::get('module'));
               if (HTTPRequest::getExists('matiere')) {
                  //Si la matière existe (le libelle existe et correspond au module actuel)
                  if (self::model('Matiere')->exists(array('libelle' => HTTPRequest::get('matiere'), 'idMod' => $idModule))) {
                     $this->addVar('matiere', HTTPRequest::get('matiere'));
                     $this->addVar('coef', number_format(self::model('Matiere')->first(array('libelle' => HTTPRequest::get('matiere'), 'idMod' => $idModule), 'coefMat'), 2, ',', ' '));
                     $this->setWindowTitle('Gestion de la matière ' . HTTPRequest::get('matiere'));
                     $this->setSubAction('manageMatiere');
                  } else {
                     User::addPopup('La matière « ' . HTTPRequest::get('matiere') . ' » n\'existe pas.', Popup::ERROR);
                     HTTPResponse::redirect('/admin/' . HTTPRequest::get('promo') . '/' . HTTPRequest::get('module') . '/matières');
                  }
               } else if (HTTPRequest::getExists('action')) {
                  $module = self::model('Module');
                  if (HTTPRequest::get('action') === 'ajouter') {
                     /**
                      * Ajout d'une matière
                      */
                     $this->setSubAction('addMatiere');
                     //Si le formulaire a été bien été envoyé
                     if (HTTPRequest::postExists('libelle', 'coef', 'idProf')) {
                        $matiere = self::model('Matiere');
                        //On vérifie si une autre matière ne porte pas déjà le même nom dans le module concerné
                        if (!$matiere->exists(array('idMod' => $idModule, 'libelle' => HTTPRequest::post('libelle')))) {
                           $matiere['idMod'] = $idModule;
                           $matiere['libelle'] = HTTPRequest::post('libelle');
                           $matiere['coefMat'] = HTTPRequest::post('coef');
                           $matiere['idProf'] = HTTPRequest::post('idProf');
                           if ($matiere->save()) {
                              User::addPopup('La matière a bien été ajoutée.', Popup::SUCCESS);
                              HTTPResponse::redirect('/admin/' . HTTPRequest::get('promo') . '/' . HTTPRequest::get('module') . '/matières');
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
                     $this->setWindowTitle('Ajouter une matière');
                     $listeDesProfs = self::model('Utilisateur')->find(array('idUtil' => self::model('Prof')->findAll('idUtil')));
                     $idProfs = self::model('Prof')->findAll('idProf');
                     //Fusion des ids prof et de la liste de profs
                     for ($i = 0 ; $i < count($listeDesProfs) ; ++$i) {
                        $listeDesProfs[$i]['idProf'] = $idProfs[$i];
                        $listeDesProfs[$i]['login'] = htmlspecialchars(stripslashes($listeDesProfs[$i]['login']));
                        $listeDesProfs[$i]['nom'] = htmlspecialchars(stripslashes($listeDesProfs[$i]['nom']));
                        $listeDesProfs[$i]['prenom'] = htmlspecialchars(stripslashes($listeDesProfs[$i]['prenom']));
                     }
                     $this->addVar('listeProfsResponsables', $listeDesProfs);
                  } else if (HTTPRequest::get('action') === 'modifier') {
                     /**
                      * Modification d'un module
                      */
                     $this->setSubAction('editModule');
                     $this->setWindowTitle('Modifier un module');
                     //Si le formulaire a été bien été envoyé
                     if (HTTPRequest::postExists('libelle')) {
                        $module['idMod'] = $idModule;
                        $module['libelle'] = HTTPRequest::post('libelle');
                        $module['idPromo'] = self::model('Promo')->first(array('libelle' => HTTPRequest::get('promo')), 'idPromo');
                        if ($module->save()) {
                           User::addPopup('Le module a bien été modifié.', Popup::SUCCESS);
                           HTTPResponse::redirect('/admin/' . HTTPRequest::get('promo') . '/' . $module['libelle'] . '/matières');
                        } else {
                           //Récupération et affichage des erreurs
                           $erreurs = $module->errors();
                           foreach ($erreurs as $erreurId) {
                              switch ($erreurId) {
                                 case ModuleModel::BAD_LIBELLE_ERROR:
                                    User::addPopup('Le nom du module est invalide.', Popup::ERROR);
                                    break;
                              }
                           }
                        }
                     }
                  } else if (HTTPRequest::get('action') === 'supprimer') {
                     /**
                      * Suppression d'un module
                      */
                     $module->delete(array('idMod' => $idModule));
                     User::addPopup('Le module a bien été supprimé.', Popup::SUCCESS);
                     HTTPResponse::redirect('/admin/' . HTTPRequest::get('promo') . '/modules');
                  }
               } else {
                  $this->setSubAction('manageModule');
                  $matieresList = self::model('Matiere')->field('libelle', array('idMod' => $idModule));
                  foreach ($matieresList as &$matiere) {
                     $matiere = htmlspecialchars(stripslashes($matiere));
                  }
                  $this->addVar('listeDesMatieres', $matieresList);
               }
            } else {
               User::addPopup('Le module « ' . HTTPRequest::get('module') . ' » n\'existe pas.', Popup::ERROR);
               HTTPResponse::redirect('/admin/' . HTTPRequest::get('promo') . '/modules');
            }
         } else {
            if (HTTPRequest::getExists('action') && HTTPRequest::get('action') === 'ajouter') {
               $this->setSubAction('addModule');
               $this->setWindowTitle('Ajouter un module');
               if (HTTPRequest::postExists('libelle')) {
                  $module = self::model('Module');
                  $idPromo = self::model('Promo')->first(array('libelle' => HTTPRequest::get('promo')), 'idPromo');
                  if (!$module->exists(array('idPromo' => $idPromo, 'libelle' => HTTPRequest::post('libelle')))) {
                     $module['libelle'] = HTTPRequest::post('libelle');
                     $module['idPromo'] = $idPromo;
                     if ($module->save()) {
                        User::addPopup('Le module a bien été ajouté.', Popup::SUCCESS);
                        HTTPResponse::redirect('/admin/' . HTTPRequest::get('promo') . '/modules');
                     } else {
                        //Récupération et affichage des erreurs
                        $erreurs = $module->errors();
                        foreach ($erreurs as $erreurId) {
                           switch ($erreurId) {
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
               if (preg_match('#^[aeiouy]#', HTTPRequest::get('promo'))) {
                  $prefixPromo = 'd\'';
               } else {
                  $prefixPromo = 'de ';
               }
               $this->addVar('prefixPromo', $prefixPromo);
               $this->setWindowTitle('Gestion des modules ' . $prefixPromo . HTTPRequest::get('promo'));
               //Récupèration de la liste des modules correspondants à la promo
               $modulesList = self::model('Module')->field('libelle', array('idPromo' => self::model('Promo')->first(array('libelle' => HTTPRequest::get('promo')), 'idPromo')));
               foreach ($modulesList as &$module) {
                  $module = htmlspecialchars(stripslashes($module));
               }
               $this->addVar('listeDesModules', $modulesList);
            }
         }
      } else {
         User::addPopup('Désolé, la promo « ' . HTTPRequest::get('promo') . ' » n\'existe pas.', Popup::ERROR);
         HTTPResponse::redirect('/admin/promos');
      }
   }

   /**
    * Gestion des étudiants rattachés à une promotion.
    * @see promotion
    */
   public function etudiant() {
      if (HTTPRequest::getExists('promo')) {
         if (self::model('Promo')->exists(array('libelle' => HTTPRequest::get('promo')))) {
            $idPromo = self::model('Promo')->first(array('libelle' => HTTPRequest::get('promo')), 'idPromo');
            $this->addVar('promo', HTTPRequest::get('promo'));
            if (HTTPRequest::getExists('action') && HTTPRequest::get('action') === 'ajouter') {
               $this->setSubAction('addStudent');
               //TODO! Traitement du formulaire
               $this->setWindowTitle('Ajouter un étudiant');
               $idsEtudiants = self::model('Eleve')->field('idUtil');
               $listeDesEtudiants = self::model('Utilisateur')->find(array('idUtil' => $idsEtudiants));
               foreach ($listeDesEtudiants as &$etudiant) {
                  $etudiant['login'] = htmlspecialchars(stripslashes($etudiant['login']));
                  $etudiant['nom'] = htmlspecialchars(stripslashes($etudiant['nom']));
                  $etudiant['prenom'] = htmlspecialchars(stripslashes($etudiant['prenom']));
               }
               $this->addVar('listeDesEtudiants', $listeDesEtudiants);
            } else {
               if (preg_match('#^[aeiouy]#', HTTPRequest::get('promo'))) {
                  $prefixPromo = 'd\'';
               } else {
                  $prefixPromo = 'de ';
               }
               $idsEtudiantsPromo = self::model('Eleve')->field('idUtil', array('idPromo' => $idPromo));
               $listeDesEtudiants = self::model('Utilisateur')->find(array('idUtil' => $idsEtudiantsPromo));
               foreach ($listeDesEtudiants as &$etudiant) {
                  $etudiant['login'] = htmlspecialchars(stripslashes($etudiant['login']));
                  $etudiant['nom'] = htmlspecialchars(stripslashes($etudiant['nom']));
                  $etudiant['prenom'] = htmlspecialchars(stripslashes($etudiant['prenom']));
               }
               $this->addVar('listeDesEtudiants', $listeDesEtudiants);
               $this->setWindowTitle('Gestion des étudiants ' . $prefixPromo . HTTPRequest::get('promo'));
               $this->addVar('prefixPromo', $prefixPromo);
            }
         } else {
            User::addPopup('Cette promotion n\'existe pas.', Popup::ERROR);
            HTTPResponse::redirect('/admin/');
         }
      } else {
         User::addPopup('Veuillez sélectionner une promotion pour commencer.', Popup::ERROR);
         HTTPResponse::redirect('/admin/');
      }
   }

   /**
    * Gestion des professeurs
    */
   public function prof() {
      
   }

}