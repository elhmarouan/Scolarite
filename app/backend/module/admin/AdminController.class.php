<?php

/**
 * Admin controller
 *
 * Contrôleur du module réservé aux administrateurs
 * 
 * @author Céline Lepicard <lepicardce@eisti.eu> et Stanislas Michalak <stanislas.michalak@gmail.com>
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
    * Gestion des types d'examens
    */
   public function typesExams() {
      if (HTTPRequest::getExists('idTypeExam')) {
         if (HTTPRequest::getExists('action') && HTTPRequest::get('action') === 'modifier') {
            /**
             * Modification d'un type d'examen
             */
            if (HTTPRequest::postExists('libelle', 'coef')) {
               $typeExam = self::model('TypeExam');
               if (!$typeExam->exists(array('idType !=' => HTTPRequest::get('idTypeExam'), 'libelle' => HTTPRequest::post('libelle')))) {
                  $typeExam['idType'] = HTTPRequest::get('idTypeExam');
                  $typeExam['libelle'] = HTTPRequest::post('libelle');
                  $typeExam['coef'] = HTTPRequest::post('coef');
                  if ($typeExam->save()) {
                     User::addPopup('Le type d\'examen a bien été modifié.', Popup::SUCCESS);
                     HTTPResponse::redirect('/admin/typesExams');
                  } else {
                     //Traitement des erreurs
                     $erreurs = $typeExam->errors();
                     foreach ($erreurs as $erreurId) {
                        switch ($erreurId) {
                           case TypeExamModel::BAD_LIBELLE_ERROR:
                              User::addPopup('Libellé invalide.', Popup::ERROR);
                              break;
                           case TypeExamModel::BAD_COEF_ERROR:
                              User::addPopup('Coefficient invalide.', Popup::ERROR);
                              break;
                        }
                     }
                  }
               } else {
                  User::addPopup('Un autre type d\'examen porte déjà ce nom. Choisissez-en un autre.', Popup::ERROR);
               }
            }
            $this->setSubAction('editExamType');
            $this->setWindowTitle('Modifier un type d\'examen');
            $this->addVar('idTypeExam', HTTPRequest::get('idTypeExam'));
            //Récupération du contenu des champs du formulaire
            $typeExam = self::model('TypeExam')->first(array('idType' => HTTPRequest::get('idTypeExam')));
            $typeExam['libelle'] = htmlspecialchars(stripslashes($typeExam['libelle']));
            $typeExam['coef'] = str_replace('.', ',', round($typeExam['coef'], 2));
            $this->addVar('typeExam', $typeExam);
         } else if (HTTPRequest::getExists('action') && HTTPRequest::get('action') === 'supprimer') {
            /**
             * Suppression d'un type d'examen
             */
            if (!self::model('Examen')->exists(array('idType' => HTTPRequest::get('idTypeExam')))) {
               self::model('TypeExam')->delete(array('idType' => HTTPRequest::get('idTypeExam')));
               User::addPopup('Le type d\'examen a bien été supprimé.', Popup::SUCCESS);
            } else {
               User::addPopup('Ce type est utilisé par certains examens. Veuillez le modifier, changer le type des examens en question, ou les supprimer.', Popup::ERROR);
            }
            HTTPResponse::redirect('/admin/typesExams');
         }
      } else if (HTTPRequest::getExists('action') && HTTPRequest::get('action') === 'ajouter') {
         /**
          * Ajout d'un type d'examen
          */
         if (HTTPRequest::postExists('libelle', 'coef')) {
            $typeExam = self::model('TypeExam');
            if (!$typeExam->exists(array('libelle' => HTTPRequest::post('libelle')))) {
               $typeExam['libelle'] = HTTPRequest::post('libelle');
               $typeExam['coef'] = HTTPRequest::post('coef');
               if ($typeExam->save()) {
                  User::addPopup('Le type d\'examen a bien été ajouté.', Popup::SUCCESS);
                  HTTPResponse::redirect('/admin/typesExams');
               } else {
                  $erreurs = $typeExam->errors();
                  foreach ($erreurs as $erreurId) {
                     switch ($erreurId) {
                        case TypeExamModel::BAD_LIBELLE_ERROR:
                           User::addPopup('Libellé invalide.', Popup::ERROR);
                           break;
                        case TypeExamModel::BAD_COEF_ERROR:
                           User::addPopup('Coefficient invalide.', Popup::ERROR);
                           break;
                     }
                  }
               }
            } else {
               User::addPopup('Un autre type d\'examen porte déjà ce nom. Choisissez-en un autre.', Popup::ERROR);
            }
         }
         $this->setSubAction('addExamType');
         $this->setWindowTitle('Ajouter un type d\'examen');
      } else {
         $this->setWindowTitle('Gestion des types d\'examens');
         $listeDesTypesExams = self::model('TypeExam')->findAll();
         foreach ($listeDesTypesExams as &$typeExam) {
            $typeExam['libelle'] = htmlspecialchars(stripslashes($typeExam['libelle']));
            $typeExam['coef'] = str_replace('.', ',', round($typeExam['coef'], 2));
         }
         $this->addVar('listeDesTypesExams', $listeDesTypesExams);
      }
   }

   /**
    * Gestion des utilisateurs
    */
   public function utilisateur() {
      if (HTTPRequest::getExists('action') && HTTPRequest::get('action') === 'ajouter') {
         /**
          * Ajout d'un utilisateur
          */
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
                              $eleve['idPromo'] = HTTPRequest::post('idPromo');
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
                                       case EleveModel::BAD_ID_PROMO_ERROR:
                                          User::addPopup('Promotion invalide ou inconnue.', Popup::ERROR);
                                          break;
                                    }
                                 }
                              }
                           } else {
                              $utilisateur->delete(array('idUtil' => $idUtil));
                              User::addPopup('Les informations nécessaires à la création du profil n\'ont pas été renseignées.', Popup::ERROR);
                           }
                        }
                        if (empty($erreurs)) {
                           User::addPopup('L\'utilisateur a bien été ajouté.', Popup::SUCCESS);
                           HTTPResponse::redirect('/admin/utilisateurs');
                        }
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
         $listeDesPromos = self::model('Promo')->findAll('idPromo', 'libelle');
         foreach ($listeDesPromos as &$promo) {
            $promo['libelle'] = htmlspecialchars(stripslashes($promo['libelle']));
         }
         $this->addVar('listeDesPromos', $listeDesPromos);
      } else if (HTTPRequest::getExists('idUtil')) {
         $utilisateur = self::model('Utilisateur');
         if ($utilisateur->exists(array('idUtil' => HTTPRequest::get('idUtil')))) {
            if (HTTPRequest::getExists('action') && HTTPRequest::get('action') === 'modifier') {
               /**
                * Modification d'un utilisateur
                */
               if (HTTPRequest::postExists('nom', 'prenom', 'role', 'login', 'password', 'passwordConfirm')) {
                  if (self::model('Role')->exists(array('idRole' => HTTPRequest::post('role')))) {
                     $utilisateur = self::model('Utilisateur');
                     //On vérifie que le login est identique à l'actuel, ou qu'il n'est pas déjà utilisé.
                     if ($utilisateur->exists(array('idUtil' => HTTPRequest::get('idUtil'), 'login' => HTTPRequest::post('login'))) || !$utilisateur->exists(array('login' => HTTPRequest::post('login')))) {
                        $utilisateur['idUtil'] = HTTPRequest::get('idUtil');
                        $utilisateur['login'] = HTTPRequest::post('login');
                        $utilisateur['nom'] = HTTPRequest::post('nom');
                        $utilisateur['prenom'] = HTTPRequest::post('prenom');
                        $oldIdRole = $utilisateur->first(array('idUtil' => HTTPRequest::get('idUtil')), 'idRole');
                        $utilisateur['idRole'] = HTTPRequest::post('role');
                        if (HTTPRequest::post('password') !== '') {
                           if (HTTPRequest::post('password') === HTTPRequest::post('passwordConfirm')) {
                              $utilisateur['pass'] = __hash(HTTPRequest::post('password'), Config::read('salt.user.prefix'), Config::read('salt.user.suffix'));
                           } else {
                              User::addPopup('Les deux mots de passe renseignés ne correspondent pas.', Popup::ERROR);
                              $badPassword = true;
                           }
                        }
                        if ((int) HTTPRequest::get('idUtil') === User::id()) {
                           if ($oldIdRole !== $utilisateur['idRole']) {
                              User::addPopup('Impossible de modifier votre rôle. Si vous voulez vraiment le faire, faîtes-en la demande à un autre administrateur.', Popup::ERROR);
                              $badRole = true;
                           }
                        }
                        if (!$badPassword && !$badRole && $utilisateur->isValid()) {
                           //Si le rôle n'a pas changé, on ne fait qu'éditer l'entrée existante
                           if ((int) $utilisateur['idRole'] === 2) {
                              //Si c'est un professeur
                              $prof = self::model('Prof');
                              $prof['idUtil'] = HTTPRequest::get('idUtil');
                              $prof['numBureau'] = HTTPRequest::post('numBureau');
                              $prof['telBureau'] = HTTPRequest::post('telBureau');
                              if ($prof->isValid()) {
                                 $prof->save();
                                 //Si le rôle a changé, et que l'ancien rôle était "étudiant", on supprime l'ancienne entrée
                                 if ((int) $oldIdRole === 3) {
                                    self::model('Participe')->delete(array('numEtudiant' => self::model('Eleve')->first(array('idUtil' => HTTPResponse::get('idUtil')), 'numEtudiant')));
                                    self::model('Eleve')->delete(array('idUtil' => HTTPRequest::get('idUtil')));
                                 }
                              } else {
                                 $erreurs = $prof->errors();
                                 foreach ($erreurs as $erreurId) {
                                    switch ($erreurId) {
                                       case ProfModel::BAD_TEL_BUREAU_ERROR:
                                          User::addPopup('Numéro de téléphone incorrect', Popup::ERROR);
                                          break;
                                       case ProfModel::BAD_NUM_BUREAU_ERROR:
                                          User::addPopup('Numéro de bureau incorrect', Popup::ERROR);
                                          break;
                                    }
                                 }
                              }
                           } else if ((int) $utilisateur['idRole'] === 3) {
                              //Si c'est un élève
                              $etudiant = self::model('Eleve');
                              $etudiant['idUtil'] = HTTPRequest::get('idUtil');
                              $etudiant['numEtudiant'] = HTTPRequest::post('numEtudiant');
                              $etudiant['anneeRedouble'] = HTTPRequest::post('anneeRedouble');
                              $etudiant['idPromo'] = HTTPRequest::post('idPromo');
                              if ($etudiant->isValid()) {
                                 $etudiant->save();
                                 //Si le rôle a changé, et que l'ancien rôle était "prof", on supprime l'entrée
                                 if ((int) $oldIdRole === 2) {
                                    self::model('Prof')->delete(array('idUtil' => HTTPRequest::get('idUtil')));
                                 }
                              } else {
                                 $erreurs = $etudiant->errors();
                                 foreach ($erreurs as $erreurId) {
                                    switch ($erreurId) {
                                       case EleveModel::BAD_NUM_ETUDIANT_ERROR:
                                          User::addPopup('Numéro étudiant incorrect', Popup::ERROR);
                                          break;
                                       case EleveModel::BAD_ANNEE_REDOUBLE_ERROR:
                                          User::addPopup('Année de redoublement incorrecte');
                                          break;
                                       case EleveModel::BAD_ID_PROMO_ERROR:
                                          User::addPopup('Promotion invalide ou inconnue.', Popup::ERROR);
                                          break;
                                    }
                                 }
                              }
                           }
                           if (empty($erreurs)) {
                              $utilisateur->save();
                              User::addPopup('L\'utilisateur a bien été modifié.', Popup::SUCCESS);
                              HTTPResponse::redirect('/admin/utilisateurs');
                           }
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
               $listeDesPromos = self::model('Promo')->findAll('idPromo', 'libelle');
               foreach ($listeDesPromos as &$promo) {
                  $promo['libelle'] = htmlspecialchars(stripslashes($promo['libelle']));
               }
               $this->addVar('listeDesPromos', $listeDesPromos);
               $utilisateur = self::model('Utilisateur')->first(array('idUtil' => HTTPRequest::get('idUtil')));
               $utilisateur['nom'] = htmlspecialchars(stripslashes($utilisateur['nom']));
               $utilisateur['prenom'] = htmlspecialchars(stripslashes($utilisateur['prenom']));
               //Récupération des données propres au rôle
               if ((int) $utilisateur['idRole'] === 2) {
                  //Si l'utilisateur est un professeur
                  $prof = self::model('Prof')->first(array('idUtil' => $utilisateur['idUtil']));
                  $utilisateur['numBureau'] = $prof['numBureau'];
                  $utilisateur['telBureau'] = '0' . $prof['telBureau'];
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
                     self::model('Participe')->delete(array('idUtil' => HTTPResponse::get('idUtil')));
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
      /**
       * Ajout d'une promotion
       */
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
            $idPromo = self::model('Promo')->first(array('libelle' => HTTPRequest::get('promo')), 'idPromo');
            /**
             * Modification d'une promotion
             */
            if (HTTPRequest::getExists('action') && HTTPRequest::get('action') === 'modifier') {
               if (HTTPRequest::postExists('libelle')) {
                  $promo = self::model('Promo');
                  $promo['idPromo'] = $idPromo;
                  $promo['libelle'] = HTTPRequest::post('libelle');
                  if ($promo->save()) {
                     User::addPopup('Le nom de la promotion a bien été modifié.', Popup::SUCCESS);
                     HTTPResponse::redirect('/admin/' . $promo['libelle']);
                  } else {
                     $erreurs = $promo->errors();
                     foreach ($erreurs as $erreurId) {
                        switch ($erreurId) {
                           case PromoModel::BAD_LIBELLE_ERROR;
                              User::addPopup('Le nom de la promotion est invalide', Popup::ERROR);
                        }
                     }
                  }
               }
               $this->setWindowTitle('Modifier une promotion');
               $this->setSubAction('editPromo');
               $this->addVar('promo', HTTPRequest::get('promo'));
               /**
                * Suppression d'une promotion
                */
            } else if (HTTPRequest::getExists('action') && HTTPRequest::get('action') === 'supprimer') {
               if (!self::model('Module')->exists(array('idPromo' => $idPromo)) && !self::model('Eleve')->exists(array('idPromo' => $idPromo))) {
                  self::model('Promo')->delete(array('idPromo' => $idPromo));
                  User::addPopup('La promotion a bien été supprimée.', Popup::SUCCESS);
                  HTTPResponse::redirect('/admin/promos');
               } else {
                  User::addPopup('Impossible de supprimer cette promotion : veuillez supprimer tous les modules de la promotion, et ré-assigner les étudiants à une autre promo avant.', Popup::ERROR);
                  HTTPResponse::redirect('/admin/' . HTTPRequest::get('promo'));
               }
            } else {
               $this->setWindowTitle('Gestion de la promotion ' . HTTPRequest::get('promo'));
               $this->setSubAction('managePromo');
               $this->addVar('promo', htmlspecialchars(stripslashes(HTTPRequest::get('promo'))));
            }
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
                     $idMatiere = self::model('Matiere')->first(array('libelle' => HTTPRequest::get('matiere'), 'idMod' => $idModule), 'idMat');
                     if (HTTPRequest::getExists('idExam')) {
                        if (HTTPRequest::getExists('action')) {
                           if (HTTPRequest::get('action') === 'modifier') {
                              /**
                               * Modifier un examen
                               */
                              if (HTTPRequest::postExists('libelle', 'date', 'idType')) {
                                 $examen = self::model('Examen');
                                 $examen['idExam'] = HTTPRequest::get('idExam');
                                 $examen['libelle'] = HTTPRequest::post('libelle');
                                 $examen['idMat'] = $idMatiere;
                                 $examen['date'] = HTTPRequest::post('date');
                                 $examen['idType'] = HTTPRequest::post('idType');
                                 if ($examen->save()) {
                                    User::addPopup('L\'examen a bien été modifié.', Popup::SUCCESS);
                                    HTTPResponse::redirect('/admin/' . HTTPRequest::get('promo') . '/' . HTTPRequest::get('module') . '/' . HTTPRequest::get('matiere'));
                                 } else {
                                    //Traitement des erreurs
                                    $erreurs = $examen->errors();
                                    foreach ($erreurs as $erreurId) {
                                       switch ($erreurId) {
                                          case ExamenModel::BAD_LIBELLE_ERROR:
                                             User::addPopup('Libellé incorrect.', Popup::ERROR);
                                             break;
                                          case ExamenModel::BAD_DATE_ERROR:
                                             User::addPopup('Date incorrecte.', Popup::ERROR);
                                             break;
                                          case ExamenModel::BAD_ID_TYPE_ERROR:
                                             User::addPopup('Type d\'examen incorrect ou inconnu.', Popup::ERROR);
                                             break;
                                       }
                                    }
                                 }
                              }
                              $this->setWindowTitle('Modifier un examen');
                              $this->setSubAction('editExam');
                              //Récupération du contenu des champs du formulaire
                              $examen = self::model('Examen')->first(array('idExam' => HTTPRequest::get('idExam')));
                              $examen['libelle'] = htmlspecialchars(stripslashes($examen['libelle']));
                              $examen['date'] = $examen['date']->format('d/m/Y');
                              $this->addVar('examen', $examen);
                              //Récupération de la liste des types d'examens
                              $listeTypesExams = self::model('TypeExam')->findAll('idType', 'libelle', 'coef');
                              foreach ($listeTypesExams as &$typeExam) {
                                 $typeExam['libelle'] = htmlspecialchars(stripslashes($typeExam['libelle']));
                                 $typeExam['coef'] = str_replace('.', ',', round($typeExam['coef'], 2));
                              }
                              $this->addVar('listeTypesExams', $listeTypesExams);
                           } else if (HTTPRequest::get('action') === 'supprimer') {
                              /**
                               * Supprimer un examen
                               */
                              self::model('Participe')->delete(array('idExam' => HTTPRequest::get('idExam')));
                              self::model('Examen')->delete(array('idExam' => HTTPRequest::get('idExam')));
                              User::addPopup('L\'examen a bien été supprimé.', Popup::SUCCESS);
                              HTTPResponse::redirect('/admin/' . HTTPRequest::get('promo') . '/' . HTTPRequest::get('module') . '/' . HTTPRequest::get('matiere'));
                           }
                        }
                     } else {
                        if (HTTPRequest::getExists('action')) {
                           if (HTTPRequest::get('action') === 'ajouter') {
                              /**
                               * Ajouter un examen
                               */
                              if (HTTPRequest::postExists('libelle', 'date', 'idType')) {
                                 $examen = self::model('Examen');
                                 $examen['libelle'] = HTTPRequest::post('libelle');
                                 $examen['idMat'] = $idMatiere;
                                 $examen['date'] = HTTPRequest::post('date');
                                 $examen['idType'] = HTTPRequest::post('idType');
                                 if ($examen->save()) {
                                    User::addPopup('L\'examen a bien été ajouté.', Popup::SUCCESS);
                                    HTTPResponse::redirect('/admin/' . HTTPRequest::get('promo') . '/' . HTTPRequest::get('module') . '/' . HTTPRequest::get('matiere'));
                                 } else {
                                    //Traitement des erreurs
                                    $erreurs = $examen->errors();
                                    foreach ($erreurs as $erreurId) {
                                       switch ($erreurId) {
                                          case ExamenModel::BAD_LIBELLE_ERROR:
                                             User::addPopup('Libellé incorrect.', Popup::ERROR);
                                             break;
                                          case ExamenModel::BAD_DATE_ERROR:
                                             User::addPopup('Date incorrecte.', Popup::ERROR);
                                             break;
                                          case ExamenModel::BAD_ID_TYPE_ERROR:
                                             User::addPopup('Type d\'examen incorrect ou inconnu.', Popup::ERROR);
                                             break;
                                       }
                                    }
                                 }
                              }
                              $this->setWindowTitle('Ajouter un examen');
                              $this->setSubAction('addExam');
                              //Récupération de la liste des types d'examens
                              $listeTypesExams = self::model('TypeExam')->findAll('idType', 'libelle', 'coef');
                              foreach ($listeTypesExams as &$typeExam) {
                                 $typeExam['libelle'] = htmlspecialchars(stripslashes($typeExam['libelle']));
                                 $typeExam['coef'] = str_replace('.', ',', round($typeExam['coef'], 2));
                              }
                              $this->addVar('listeTypesExams', $listeTypesExams);
                           } else if (HTTPRequest::get('action') === 'modifier') {
                              /**
                               * Modifier une matière
                               */
                              if (HTTPRequest::postExists('libelle', 'coef', 'idProf')) {
                                 $matiere = self::model('Matiere');
                                 //On vérifie si une autre matière ne porte pas déjà le même nom dans le module concerné
                                 if (!$matiere->exists(array('idMod' => $idModule, 'libelle' => HTTPRequest::post('libelle'), 'idMat !=' => $idMatiere))) {
                                    $matiere['idMat'] = $idMatiere;
                                    $matiere['idMod'] = $idModule;
                                    $matiere['libelle'] = HTTPRequest::post('libelle');
                                    $matiere['coefMat'] = HTTPRequest::post('coef');
                                    $matiere['idProf'] = HTTPRequest::post('idProf');
                                    if ($matiere->save()) {
                                       User::addPopup('La matière a bien été modifiée.', Popup::SUCCESS);
                                       HTTPResponse::redirect('/admin/' . HTTPRequest::get('promo') . '/' . HTTPRequest::get('module') . '/' . HTTPRequest::post('libelle'));
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
                              $this->setSubAction('editMatiere');
                              $this->setWindowTitle('Modifier une matière');
                              $this->addVar('coef', number_format(self::model('Matiere')->first(array('idMat' => $idMatiere), 'coefMat'), 2, ',', ' '));
                              //Liste des professeurs
                              $listeDesProfs = self::model('Utilisateur')->find(array('idUtil' => self::model('Prof')->findAll('idUtil')));
                              $idProfResponsable = self::model('Matiere')->first(array('idMat' => $idMatiere), 'idProf');
                              //Fusion des ids prof et de la liste de profs
                              foreach ($listeDesProfs as &$prof) {
                                 $prof['idProf'] = self::model('Prof')->first(array('idUtil' => $prof['idUtil']), 'idProf');
                                 $prof['responsable'] = ($prof['idProf'] === $idProfResponsable) ? true : false;
                                 $prof['login'] = htmlspecialchars(stripslashes($prof['login']));
                                 $prof['nom'] = htmlspecialchars(stripslashes($prof['nom']));
                                 $prof['prenom'] = htmlspecialchars(stripslashes($prof['prenom']));
                              }
                              $this->addVar('listeProfsResponsables', $listeDesProfs);
                           } else if (HTTPRequest::get('action') === 'supprimer') {
                              /**
                               * Supprimer une matière
                               */
                              if (!self::model('Examen')->exists(array('idMat' => $idMatiere))) {
                                 self::model('Matiere')->delete(array('idMat' => $idMatiere));
                                 User::addPopup('La matière a bien été supprimée.', Popup::SUCCESS);
                                 HTTPResponse::redirect('/admin/' . HTTPRequest::get('promo') . '/' . HTTPRequest::get('module') . '/matières');
                              } else {
                                 User::addPopup('Impossible de supprimer cette matière : veuillez supprimer tous les examens contenus dans celle-ci avant.', Popup::ERROR);
                                 HTTPResponse::redirect('/admin/' . HTTPRequest::get('promo') . '/' . HTTPRequest::get('module') . '/' . HTTPRequest::get('matiere'));
                              }
                           }
                        } else {
                           /**
                            * Gestion d'une matière
                            */
                           $this->addVar('coef', str_replace('.', ',', round(self::model('Matiere')->first(array('libelle' => HTTPRequest::get('matiere'), 'idMod' => $idModule), 'coefMat'))));
                           $numsEtudiants = self::model('Eleve')->field('numEtudiant', array('idPromo' => $idPromo));
                           //Liste des examens
                           $listeDesExamens = self::model('Examen')->find(array('idMat' => $idMatiere));
                           foreach ($listeDesExamens as &$examen) {
                              $examen['libelle'] = htmlspecialchars(stripslashes($examen['libelle']));
                              $examen['date'] = $examen['date']->format('d/m/Y');
                              $typeExam = self::model('TypeExam')->first(array('idType' => $examen['idType']));
                              $examen['type'] = htmlspecialchars(stripslashes($typeExam['libelle']));
                              $examen['coef'] = str_replace('.', ',', round($typeExam['coef'], 2));
                              $notesPromo = self::model('Participe')->field('note', array('numEtudiant' => $numsEtudiants, 'idExam' => $examen['idExam'], 'note !=' => null));
                              $examen['moyennePromo'] = !empty($notesPromo) ? str_replace('.', ',', round(array_sum($notesPromo) / count($notesPromo), 2)) : null;
                           }
                           $this->addVar('listeDesExamens', $listeDesExamens);

                           //Récupération de la moyenne de la promotion, en prenant en compte les coefficients de chaque examen
                           $idsExams = self::model('Examen')->field('idExam', array('idMat' => $idMatiere));
                           $participationsPromo = self::model('Participe')->find(array('numEtudiant' => $numsEtudiants, 'idExam' => $idsExams, 'note !=' => null));
                           $notesPromo = array();
                           $quotient = 0;
                           foreach ($participationsPromo as $participation) {
                              $coef = self::model('TypeExam')->first(array('idType' => self::model('Examen')->first(array('idExam' => $participation['idExam']), 'idType')), 'coef');
                              $notesPromo[] = self::model('Participe')->first(array('numEtudiant' => $participation['numEtudiant'], 'idExam' => $participation['idExam']), 'note') * $coef;
                              $quotient += $coef;
                           }
                           $this->addVar('moyennePromo', !empty($notesPromo) ? str_replace('.', ',', round(array_sum($notesPromo) / $quotient, 2)) : null);

                           $idProfResponsable = self::model('Matiere')->first(array('idMat' => $idMatiere), 'idProf');
                           $profResponsable = self::model('Utilisateur')->first(array('idUtil' => self::model('Prof')->first(array('idProf' => $idProfResponsable), 'idUtil')));
                           $profResponsable['login'] = htmlspecialchars(stripslashes($profResponsable['login']));
                           $profResponsable['nom'] = htmlspecialchars(stripslashes($profResponsable['nom']));
                           $profResponsable['prenom'] = htmlspecialchars(stripslashes($profResponsable['prenom']));
                           $profResponsable['idProf'] = $idProfResponsable;
                           $this->addVar('profResponsable', $profResponsable);

                           $this->setWindowTitle('Gestion de la matière ' . HTTPRequest::get('matiere'));
                           $this->setSubAction('manageMatiere');
                        }
                     }
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
                     for ($i = 0; $i < count($listeDesProfs); ++$i) {
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
                     if (!self::model('Matiere')->exists(array('idMod' => $idModule))) {
                        $module->delete(array('idMod' => $idModule));
                        User::addPopup('Le module a bien été supprimé.', Popup::SUCCESS);
                        HTTPResponse::redirect('/admin/' . HTTPRequest::get('promo') . '/modules');
                     } else {
                        User::addPopup('Impossible de supprimer ce module : veuillez supprimer toutes les matières du module avant.', Popup::ERROR);
                        HTTPResponse::redirect('/admin/' . HTTPRequest::get('promo') . '/' . HTTPRequest::get('module') . '/matières');
                     }
                  }
               } else {
                  /**
                   * Gestion d'un module
                   */
                  $this->setSubAction('manageModule');
                  $listeProfsResponsables = self::model('Utilisateur')->find(array('idUtil' => self::model('Prof')->field('idUtil', array('idProf' => self::model('Matiere')->field('idProf', array('idMod' => $idModule))))));
                  foreach ($listeProfsResponsables as &$profResponsable) {
                     $profResponsable['nom'] = htmlspecialchars(stripslashes($profResponsable['nom']));
                     $profResponsable['prenom'] = htmlspecialchars(stripslashes($profResponsable['prenom']));
                     $profResponsable['login'] = htmlspecialchars(stripslashes($profResponsable['login']));
                  }
                  $this->addVar('listeProfsResponsables', $listeProfsResponsables);
                  //Récupération de la liste des matières
                  $matieresList = self::model('Matiere')->field('libelle', array('idMod' => $idModule));
                  foreach ($matieresList as &$matiere) {
                     $matiere = htmlspecialchars(stripslashes($matiere));
                  }
                  $this->addVar('listeDesMatieres', $matieresList);
                  $this->addVar('coefModule', str_replace('.', ',', round(self::model('Matiere')->avg('coefMat', array('idMod' => $idModule)), 2)));
               }
            } else {
               User::addPopup('Le module « ' . HTTPRequest::get('module') . ' » n\'existe pas.', Popup::ERROR);
               HTTPResponse::redirect('/admin/' . HTTPRequest::get('promo') . '/modules');
            }
         } else {
            if (HTTPRequest::getExists('action') && HTTPRequest::get('action') === 'ajouter') {
               /**
                * Ajout d'un module
                */
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
            /**
             * Liste des étudiants d'un promotion
             */
            $idPromo = self::model('Promo')->first(array('libelle' => HTTPRequest::get('promo')), 'idPromo');
            $this->addVar('promo', HTTPRequest::get('promo'));
            if (preg_match('#^[aeiouy]#', HTTPRequest::get('promo'))) {
               $prefixPromo = 'd\'';
            } else {
               $prefixPromo = 'de ';
            }
            $idsEtudiantsPromo = self::model('Eleve')->field('idUtil', array('idPromo' => $idPromo));
            $listeDesEtudiants = self::model('Utilisateur')->find(array('idUtil' => $idsEtudiantsPromo));
            foreach ($listeDesEtudiants as &$etudiant) {
               $etudiant['numEtudiant'] = self::model('Eleve')->first(array('idUtil' => $etudiant['idUtil']), 'numEtudiant');
               $etudiant['login'] = htmlspecialchars(stripslashes($etudiant['login']));
               $etudiant['nom'] = htmlspecialchars(stripslashes($etudiant['nom']));
               $etudiant['prenom'] = htmlspecialchars(stripslashes($etudiant['prenom']));
            }
            $this->addVar('listeDesEtudiants', $listeDesEtudiants);
            $this->setWindowTitle('Gestion des étudiants ' . $prefixPromo . HTTPRequest::get('promo'));
            $this->addVar('prefixPromo', $prefixPromo);
         } else {
            User::addPopup('Cette promotion n\'existe pas.', Popup::ERROR);
            HTTPResponse::redirect('/admin/');
         }
      } else if (HTTPRequest::getExists('idUtil')) {
         if (self::model('Eleve')->exists(array('idUtil' => HTTPRequest::get('idUtil')))) {
            /**
             * Profil d'un étudiant
             */
            $this->setWindowTitle('Profil étudiant');
            $this->setSubAction('showProfil');
            $etudiant = array_merge(self::model('Eleve')->first(array('idUtil' => HTTPRequest::get('idUtil'))), self::model('Utilisateur')->first(array('idUtil' => HTTPRequest::get('idUtil'))));
            $etudiant['promo'] = htmlspecialchars(stripslashes(self::model('Promo')->first(array('idPromo' => $etudiant['idPromo']), 'libelle')));
            $etudiant['nom'] = htmlspecialchars(stripslashes($etudiant['nom']));
            $etudiant['prenom'] = htmlspecialchars(stripslashes($etudiant['prenom']));
            $etudiant['login'] = htmlspecialchars(stripslashes($etudiant['login']));
            $etudiant['listeDesModules'] = self::model('Module')->find(array('idPromo' => $etudiant['idPromo']));
            $numEtudiantsPromo = self::model('Eleve')->field('numEtudiant', array('idPromo' => $etudiant['idPromo']));
            $moyennesEleveModules = array();
            $quotientMoyennesEleveModules = 0;
            foreach ($etudiant['listeDesModules'] as &$module) {
               $module['libelle'] = htmlspecialchars(stripslashes($module['libelle']));
               $module['listeDesMatieres'] = self::model('Matiere')->find(array('idMod' => $module['idMod']));
               $moyennesEleveMatieres = array();
               $quotientMoyennesEleveMatieres = 0;
               $moyennesPromoMatieres = array();
               $quotientMoyennesPromoMatieres = 0;
               foreach ($module['listeDesMatieres'] as &$matiere) {
                  $matiere['libelle'] = htmlspecialchars(stripslashes($matiere['libelle']));
                  $matiere['listeDesExamens'] = self::model('Examen')->find(array('idMat' => $matiere['idMat'], 'idExam' => self::model('Participe')->field('idExam')));
                  //Calcul de la moyenne de la promo
                  $idsExams = self::model('Examen')->field('idExam', array('idMat' => $matiere['idMat']));
                  $participationsPromo = self::model('Participe')->find(array('numEtudiant' => $numEtudiantsPromo, 'idExam' => $idsExams, 'note !=' => null));
                  $notesPromo = array();
                  $quotient = 0;
                  foreach ($participationsPromo as $participation) {
                     $coef = self::model('TypeExam')->first(array('idType' => self::model('Examen')->first(array('idExam' => $participation['idExam']), 'idType')), 'coef');
                     $notesPromo[] = self::model('Participe')->first(array('numEtudiant' => $participation['numEtudiant'], 'idExam' => $participation['idExam']), 'note') * $coef;
                     $quotient += $coef;
                  }
                  if (!empty($notesPromo)) {
                     $moyennesPromoMatieres[] = (array_sum($notesPromo) / $quotient) * $matiere['coefMat'];
                     $quotientMoyennesPromoMatieres += $matiere['coefMat'];
                  }
                  $matiere['moyennePromo'] = !empty($notesPromo) ? str_replace('.', ',', round(array_sum($notesPromo) / $quotient, 2)) : null;

                  //Calcul de la moyenne de l'élève
                  $participationsEleve = self::model('Participe')->find(array('numEtudiant' => $etudiant['numEtudiant'], 'idExam' => $idsExams, 'note !=' => null));
                  $notesEleve = array();
                  $quotient = 0;
                  foreach ($participationsEleve as $participation) {
                     $coef = self::model('TypeExam')->first(array('idType' => self::model('Examen')->first(array('idExam' => $participation['idExam']), 'idType')), 'coef');
                     $notesEleve[] = self::model('Participe')->first(array('numEtudiant' => $participation['numEtudiant'], 'idExam' => $participation['idExam']), 'note') * $coef;
                     $quotient += $coef;
                  }
                  if (!empty($notesEleve)) {
                     $moyennesEleveMatieres[] = (array_sum($notesEleve) / $quotient) * $matiere['coefMat'];
                     $quotientMoyennesEleveMatieres += $matiere['coefMat'];
                  }
                  $matiere['moyenneEleve'] = !empty($notesEleve) ? str_replace('.', ',', round(array_sum($notesEleve) / $quotient, 2)) : null;

                  foreach ($matiere['listeDesExamens'] as &$examen) {
                     $examen['libelle'] = htmlspecialchars(stripslashes($examen['libelle']));
                     $examen['note'] = str_replace('.', ',', round(self::model('Participe')->first(array('idExam' => $examen['idExam'], 'numEtudiant' => $etudiant['numEtudiant']), 'note'), 2));
                     $notesPromo = self::model('Participe')->field('note', array('idExam' => $examen['idExam'], 'numEtudiant' => self::model('Eleve')->field('numEtudiant', array('idPromo' => $etudiant['idPromo']))));
                     $examen['moyennePromo'] = !empty($notesPromo) ? str_replace('.', ',', round(array_sum($notesPromo) / count($notesPromo), 2)) : null;
                  }
               }
               $module['coef'] = self::model('Matiere')->avg('coefMat', array('idMod' => $module['idMod']));
               if (!empty($moyennesEleveMatieres)) {
                  $moyennesEleveModules[] = (array_sum($moyennesEleveMatieres) / $quotientMoyennesEleveMatieres) * $module['coef'];
                  $quotientMoyennesEleveModules += $module['coef'];
               }
               $module['moyennePromo'] = !empty($moyennesPromoMatieres) ? str_replace('.', ',', round(array_sum($moyennesPromoMatieres) / $quotientMoyennesPromoMatieres, 2)) : null;
               $module['moyenneEleve'] = !empty($moyennesEleveMatieres) ? str_replace('.', ',', round(array_sum($moyennesEleveMatieres) / $quotientMoyennesEleveMatieres, 2)) : null;
            }
            $etudiant['moyenneGenerale'] = !empty($moyennesEleveModules) ? str_replace('.', ',', round(array_sum($moyennesEleveModules) / $quotientMoyennesEleveModules, 2)) : null;
            $this->addVar('etudiant', $etudiant);
         } else {
            User::addPopup('Cet étudiant n\'existe pas.', Popup::ERROR);
            HTTPResponse::redirect('/admin/');
         }
      }
   }

   /**
    * Gestion des professeurs
    */
   public function prof() {
      if (HTTPRequest::getExists('idUtil')) {
         if (self::model('Prof')->exists(array('idUtil' => HTTPRequest::get('idUtil')))) {
            /**
             * Profil d'un professeur
             */
            $prof = array_merge(self::model('Prof')->first(array('idUtil' => HTTPRequest::get('idUtil'))), self::model('Utilisateur')->first(array('idUtil' => HTTPRequest::get('idUtil'))));
            $prof['nom'] = htmlspecialchars(stripslashes($prof['nom']));
            $prof['prenom'] = htmlspecialchars(stripslashes($prof['prenom']));
            $prof['login'] = htmlspecialchars(stripslashes($prof['login']));
            //Récupération de la liste des responsabilités
            $prof['responsabilites'] = array();
            $matieres = self::model('Matiere')->find(array('idProf' => $prof['idProf']));
            foreach ($matieres as $matiere) {
               $module = self::model('Module')->first(array('idMod' => $matiere['idMod']));
               $promo = self::model('Promo')->first(array('idPromo' => $module['idPromo']));
               $prof['responsabilites'][] = array('matiere' => htmlspecialchars(stripslashes($matiere['libelle'])), 'module' => htmlspecialchars(stripslashes($module['libelle'])), 'promo' => htmlspecialchars(stripslashes($promo['libelle'])));
            }
            $this->addVar('prof', $prof);
            $this->setWindowTitle('Profil professeur');
         } else {
            User::addPopup('Ce professeur n\'existe pas.', Popup::ERROR);
            HTTPResponse::redirect('/admin/');
         }
      }
   }

   /**
    * Export au format CSV
    */
   public function exporterCsv() {
      if (HTTPRequest::getExists('what')) {
         if (HTTPRequest::get('what') === 'étudiant') {
            /**
             * Exportation des données d'un étudiant
             */
            if (HTTPRequest::postExists('idUtil')) {
               //Si le formulaire a été validé
               $data = array();
               $fields = array();
               $donneesUtil = self::model('Utilisateur')->first(array('idUtil' => HTTPRequest::post('idUtil')));
               $donneesUtil['numEtudiant'] = self::model('Eleve')->first(array('idUtil' => $donneesUtil['idUtil']), 'numEtudiant');
               $donneesUtil['idPromo'] = self::model('Eleve')->first(array('idUtil' => $donneesUtil['idUtil']), 'idPromo');
               if (HTTPRequest::postExists('login')) {
                  $data['login'] = htmlspecialchars(stripslashes($donneesUtil['login']));
                  $fields[] = 'login';
               }
               if (HTTPRequest::postExists('nom')) {
                  $data['nom'] = htmlspecialchars(stripslashes($donneesUtil['nom']));
                  $fields[] = 'nom';
               }
               if (HTTPRequest::postExists('prenom')) {
                  $data['prenom'] = htmlspecialchars(stripslashes($donneesUtil['prenom']));
                  $fields[] = 'prenom';
               }
               if (HTTPRequest::postExists('promotion')) {
                  $data['promotion'] = htmlspecialchars(stripslashes(self::model('Promo')->first(array('idPromo' => $donneesUtil['idPromo']), 'libelle')));
                  $fields[] = 'promotion';
               }
               if (HTTPRequest::postExists('numEtudiant')) {
                  $data['numEtudiant'] = $donneesUtil['numEtudiant'];
                  $fields[] = 'numEtudiant';
               }
               if (HTTPRequest::postExists('anneeRedouble')) {
                  $anneeRedouble = self::model('Eleve')->first(array('idUtil' => $donneesUtil['idUtil']), 'anneeRedouble');
                  $data['anneeRedouble'] = ($anneeRedouble !== 0) ? $anneeRedouble : 'Aucune';
                  $fields[] = 'anneeRedouble';
               }
               if (HTTPRequest::postExists('moyenneGenerale') || HTTPRequest::postExists('moyenneMatieres')) {
                  $moyennesEleveModules = array();
                  $quotientMoyennesEleveModules = 0;
                  $listeDesModules = self::model('Module')->find(array('idPromo' => $donneesUtil['idPromo']));
                  foreach ($listeDesModules as &$module) {
                     $module['libelle'] = htmlspecialchars(stripslashes($module['libelle']));
                     $module['listeDesMatieres'] = self::model('Matiere')->find(array('idMod' => $module['idMod']));
                     $moyennesEleveMatieres = array();
                     $quotientMoyennesEleveMatieres = 0;
                     foreach ($module['listeDesMatieres'] as &$matiere) {
                        $matiere['libelle'] = htmlspecialchars(stripslashes($matiere['libelle']));
                        $matiere['listeDesExamens'] = self::model('Examen')->find(array('idMat' => $matiere['idMat'], 'idExam' => self::model('Participe')->field('idExam')));
                        $idsExams = self::model('Examen')->field('idExam', array('idMat' => $matiere['idMat']));

                        //Calcul de la moyenne de l'élève
                        $participationsEleve = self::model('Participe')->find(array('numEtudiant' => $donneesUtil['numEtudiant'], 'idExam' => $idsExams, 'note !=' => null));
                        $notesEleve = array();
                        $quotient = 0;
                        foreach ($participationsEleve as $participation) {
                           $coef = self::model('TypeExam')->first(array('idType' => self::model('Examen')->first(array('idExam' => $participation['idExam']), 'idType')), 'coef');
                           $notesEleve[] = self::model('Participe')->first(array('numEtudiant' => $participation['numEtudiant'], 'idExam' => $participation['idExam']), 'note') * $coef;
                           $quotient += $coef;
                        }
                        if (!empty($notesEleve)) {
                           $moyennesEleveMatieres[] = (array_sum($notesEleve) / $quotient) * $matiere['coefMat'];
                           $quotientMoyennesEleveMatieres += $matiere['coefMat'];
                        }
                        if (HTTPRequest::postExists('moyenneMatieres')) {
                           $data[$matiere['libelle']] = !empty($notesEleve) ? str_replace('.', ',', round(array_sum($notesEleve) / $quotient, 2)) : '"Moyenne indisponible"';
                           $fields[] = '"' . $matiere['libelle'] . '"';
                        }
                     }
                     $module['coef'] = self::model('Matiere')->avg('coefMat', array('idMod' => $module['idMod']));
                     if (!empty($moyennesEleveMatieres)) {
                        $moyennesEleveModules[] = (array_sum($moyennesEleveMatieres) / $quotientMoyennesEleveMatieres) * $module['coef'];
                        $quotientMoyennesEleveModules += $module['coef'];
                     }
                  }
                  if (HTTPRequest::postExists('moyenneGenerale')) {
                     $data['moyenneGenerale'] = !empty($moyennesEleveModules) ? str_replace('.', ',', round(array_sum($moyennesEleveModules) / $quotientMoyennesEleveModules, 2)) : null;
                     $fields[] = 'moyenneGenerale';
                  }
               }
               $this->addVar('fields', implode(';', $fields));
               $this->addVar('data', implode(';', $data));
               HTTPResponse::addHeader('Content-Type: text/csv');
               HTTPResponse::addHeader('Content-Disposition: attachment;filename=etudiant_' . $donneesUtil['numEtudiant'] . '.csv');
               $this->page()->useRawView();
            }
            if (HTTPRequest::getExists('idUtil')) {
               if (self::model('Eleve')->exists(array('idUtil' => HTTPRequest::get('idUtil')))) {
                  $this->addVar('idUtil', (int) HTTPRequest::get('idUtil'));
               } else {
                  User::addPopup('Cet étudiant n\'existe pas.', Popup::ERROR);
                  HTTPResponse::redirect('/admin/exporter/étudiant');
               }
            }
            $this->setWindowTitle('Exporter les données d\'un étudiant');
            $listeDesEtudiants = self::model('Utilisateur')->find(array('idUtil' => self::model('Eleve')->field('idUtil')));
            foreach ($listeDesEtudiants as &$etudiant) {
               $etudiant['login'] = htmlspecialchars(stripslashes($etudiant['login']));
               $etudiant['nom'] = htmlspecialchars(stripslashes($etudiant['nom']));
               $etudiant['prenom'] = htmlspecialchars(stripslashes($etudiant['prenom']));
            }
            $this->addVar('listeDesEtudiants', $listeDesEtudiants);
            $this->setSubAction('exportUser');
         } else if (HTTPRequest::get('what') === 'promotion') {
            if (HTTPRequest::getExists('promo')) {
               if (self::model('Promo')->exists(array('libelle' => HTTPRequest::get('promo')))) {
                  /**
                   * Export des données d'une promotion
                   */
                  $this->addVar('promo', htmlspecialchars(stripslashes(HTTPRequest::get('promo'))));
                  $this->setWindowTitle('Exporter les données de la promotion « ' . HTTPRequest::get('promo') . ' »');
                  $this->setSubAction('exportPromo');
               } else {
                  User::addPopup('Cette promotion n\'existe pas.', Popup::ERROR);
                  HTTPResponse::redirect('/admin/exporter/promotion');
               }
            } else {
               /**
                * Choix d'une promotion
                */
               $this->setWindowTitle('Exporter les données d\'une promotion');
               $this->setSubAction('exportPromos');
               $promosList = self::model('Promo')->field('libelle');
               foreach ($promosList as &$promo) {
                  $promo = htmlspecialchars(stripslashes($promo));
               }
               $this->addVar('promosList', $promosList);
            }
         }
      } else {
         $this->setWindowTitle('Exporter des informations');
      }
   }

}