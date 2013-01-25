<?php

/**
 * Prof controller
 * 
 * @author Soheil Dahmani <dahmanisou@eisti.eu> et Stanislas Michalak <stanislas.michalak@gmail.com>
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

   /**
    * Accueil professeur et liste des promotions
    */
   public function index() {
      $this->setWindowTitle('Accueil professeurs');
      $promosList = self::model('Promo')->field('libelle');
      foreach ($promosList as &$promo) {
         $promo = htmlspecialchars(stripslashes($promo));
      }
      $this->addVar('promosList', $promosList);
   }

   /**
    * Options d'une promotion
    */
   public function promo() {
      if (HTTPRequest::getExists('promo')) {
         //Si la promotion existe
         if (self::model('Promo')->exists(array('libelle' => HTTPRequest::get('promo')))) {
            $this->setWindowTitle('Gestion de la promotion ' . HTTPRequest::get('promo'));
            $this->setSubAction('managePromo');
            $this->addVar('promo', htmlspecialchars(stripslashes(HTTPRequest::get('promo'))));
         } else {
            User::addPopup('Désolé, la promo « ' . HTTPRequest::get('promo') . ' » n\'existe pas.', Popup::ERROR);
            HTTPResponse::redirect('/prof/');
         }
      } else {
         User::addPopup('Veuillez sélectionner une promotion avant.');
         HTTPResponse::redirect('/prof/');
      }
   }

   public function module() {
      if (HTTPRequest::getExists('promo')) {
         //Si la promotion existe
         if (self::model('Promo')->exists(array('libelle' => HTTPRequest::get('promo')))) {
            $this->addVar('promo', HTTPRequest::get('promo'));
            if (HTTPRequest::getExists('module')) {
               //Si le module existe
               if (self::model('Module')->exists(array('libelle' => HTTPRequest::get('module')))) {
                  $idModule = self::model('Module')->first(array('libelle' => HTTPRequest::get('module'), 'idPromo' => self::model('Promo')->first(array('libelle' => HTTPRequest::get('promo')), 'idPromo')), 'idMod');
                  $this->setWindowTitle('Matières du module ' . HTTPRequest::get('module'));
                  $this->setSubAction('manageMatieres');
                  $this->addVar('module', HTTPRequest::get('module'));

                  $listeProfsResponsables = self::model('Utilisateur')->find(array('idUtil' => self::model('Prof')->field('idUtil', array('idProf' => self::model('Matiere')->field('idProf', array('idMod' => $idModule))))));
                  foreach ($listeProfsResponsables as &$profResponsable) {
                     $profResponsable['nom'] = htmlspecialchars(stripslashes($profResponsable['nom']));
                     $profResponsable['prenom'] = htmlspecialchars(stripslashes($profResponsable['prenom']));
                     $profResponsable['login'] = htmlspecialchars(stripslashes($profResponsable['login']));
                  }
                  $this->addVar('listeProfsResponsables', $listeProfsResponsables);

                  //Récupération de la liste des matières
                  $listeDesMatieres = self::model('Matiere')->field('libelle', array('idMod' => $idModule));
                  foreach ($listeDesMatieres as &$matiere) {
                     $matiere = htmlspecialchars(stripslashes($matiere));
                  }
                  $this->addVar('listeDesMatieres', $listeDesMatieres);
                  $this->addVar('coefModule', str_replace('.', ',', round(self::model('Matiere')->avg('coefMat', array('idMod' => $idModule)), 2)));
               } else {
                  User::addPopup('Ce module n\'existe pas.');
                  HTTPResponse::redirect('/prof/' . HTTPRequest::get('promo'));
               }
            } else {
               if (preg_match('#^[aeiouy]#', HTTPRequest::get('promo'))) {
                  $prefixPromo = 'd\'';
               } else {
                  $prefixPromo = 'de ';
               }
               $this->addVar('prefixPromo', $prefixPromo);
               $this->setWindowTitle('Liste des modules ' . $prefixPromo . HTTPRequest::get('promo'));

               //Récupèration de la liste des modules correspondants à la promo
               $modulesList = self::model('Module')->field('libelle', array('idPromo' => self::model('Promo')->first(array('libelle' => HTTPRequest::get('promo')), 'idPromo')));
               foreach ($modulesList as &$module) {
                  $module = htmlspecialchars(stripslashes($module));
               }
               $this->addVar('listeDesModules', $modulesList);
            }
         } else {
            User::addPopup('Désolé, cette promotion n\'existe pas.', Popup::ERROR);
            HTTPResponse::redirect('/prof/');
         }
      } else {
         HTTPResponse::redirect('/prof/');
      }
   }

   public function matiere() {
      if (HTTPRequest::getExists('promo', 'module', 'matiere')) {
         if (self::model('Promo')->exists(array('libelle' => HTTPRequest::get('promo')))) {
            $this->addVar('promo', HTTPRequest::get('promo'));
            $idPromo = self::model('Promo')->first(array('libelle' => HTTPRequest::get('promo')), 'idPromo');
            if (self::model('Module')->exists(array('libelle' => HTTPRequest::get('module'), 'idPromo' => $idPromo))) {
               $this->addVar('module', HTTPRequest::get('module'));
               $idModule = self::model('Module')->first(array('libelle' => HTTPRequest::get('module'), 'idPromo' => $idPromo), 'idMod');
               if (self::model('Matiere')->exists(array('libelle' => HTTPRequest::get('matiere'), 'idMod' => $idModule))) {
                  $this->addVar('matiere', HTTPRequest::get('matiere'));
                  $idMatiere = self::model('Matiere')->first(array('libelle' => HTTPRequest::get('matiere'), 'idMod' => $idModule), 'idMat');
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
               } else {
                  User::addPopup('Désolé, cette matière n\'existe pas.', Popup::ERROR);
                  HTTPResponse::redirect('/prof/' . HTTPRequest::get('promo') . '/' . HTTPRequest::get('module') . '/matières');
               }
            } else {
               User::addPopup('Désolé, ce module n\'existe pas.', Popup::ERROR);
               HTTPResponse::redirect('/prof/' . HTTPRequest::get('promo'));
            }
         } else {
            User::addPopup('Désolé, cette promotion n\'existe pas.', Popup::ERROR);
            HTTPResponse::redirect('/prof/');
         }
      } else {
         HTTPResponse::redirect('/prof/');
      }
   }

   public function examen() {
      if (HTTPRequest::getExists('promo', 'module', 'matiere')) {
         if (self::model('Promo')->exists(array('libelle' => HTTPRequest::get('promo')))) {
            $this->addVar('promo', HTTPRequest::get('promo'));
            $idPromo = self::model('Promo')->first(array('libelle' => HTTPRequest::get('promo')), 'idPromo');
            if (self::model('Module')->exists(array('libelle' => HTTPRequest::get('module'), 'idPromo' => $idPromo))) {
               $this->addVar('module', HTTPRequest::get('module'));
               $idModule = self::model('Module')->first(array('libelle' => HTTPRequest::get('module'), 'idPromo' => $idPromo), 'idMod');
               if (self::model('Matiere')->exists(array('libelle' => HTTPRequest::get('matiere'), 'idMod' => $idModule))) {
                  $idMatiere = self::model('Matiere')->first(array('libelle' => HTTPRequest::get('matiere'), 'idMod' => $idModule), 'idMat');
                  $estResponsable = self::model('Matiere')->exists(array('idProf' => self::model('Prof')->first(array('idUtil' => User::id()), 'idProf'), 'idMat' => $idMatiere));
                  if (self::model('Examen')->exists(array('idExam' => HTTPRequest::get('idExam')))) {
                     if (HTTPRequest::getExists('numEtudiant')) {
                        if (self::model('Participe')->exists(array('numEtudiant' => HTTPRequest::get('numEtudiant'), 'idExam' => HTTPRequest::get('idExam')))) {
                           if (HTTPRequest::getExists('action') && HTTPRequest::get('action') === 'supprimer') {
                              /**
                               * Suppression d'une note
                               */
                              self::model('Participe')->delete(array('numEtudiant' => HTTPRequest::get('numEtudiant'), 'idExam' => HTTPRequest::get('idExam')));
                              User::addPopup('La note a bien été supprimée.', Popup::SUCCESS);
                              HTTPResponse::redirect('/prof/' . HTTPRequest::get('promo') . '/' . HTTPRequest::get('module') . '/' . HTTPRequest::get('matiere') . '/' . HTTPRequest::get('idExam'));
                           }
                        }
                     } else {
                        $this->addVar('matiere', HTTPRequest::get('matiere'));
                        $this->addVar('idExam', HTTPRequest::get('idExam'));
                        /**
                         * Ajout d'une note en Ajax
                         */
                        if (HTTPRequest::postExists('login', 'nom', 'prenom', 'note')) {
                           //On vérifie que les champs ne sont pas vides...
                           if (HTTPRequest::post('login') !== '' && HTTPRequest::post('nom') !== '' && HTTPRequest::post('prenom') !== '' && HTTPRequest::post('note') !== '') {
                              //Que l'étudiant existe
                              if (self::model('Utilisateur')->exists(array('nom' => HTTPRequest::post('nom'), 'prenom' => HTTPRequest::post('prenom'), 'login' => HTTPRequest::post('login')))) {
                                 $participe = self::model('Participe');
                                 $participe['idExam'] = HTTPRequest::get('idExam');
                                 $participe['note'] = HTTPRequest::post('note') === 'ABS' ? null : HTTPRequest::post('note');
                                 $participe['numEtudiant'] = self::model('Eleve')->first(array('idUtil' => self::model('Utilisateur')->first(array('nom' => HTTPRequest::post('nom'), 'prenom' => HTTPRequest::post('prenom'), 'login' => HTTPRequest::post('login')), 'idUtil')), 'numEtudiant');
                                 //On vérifie également qu'il n'existe pas déjà une note pour cet élève
                                 if (!self::model('Participe')->exists(array('numEtudiant' => $participe['numEtudiant'], 'idExam' => $participe['idExam']))) {
                                    if ($participe->save()) {
                                       $this->addVar('data', array('login' => htmlspecialchars(stripslashes(HTTPRequest::post('login'))), 'nom' => htmlspecialchars(stripslashes(HTTPRequest::post('nom'))), 'prenom' => htmlspecialchars(stripslashes(HTTPRequest::post('prenom'))), 'note' => str_replace('.', ',', HTTPRequest::post('note')), 'numEtudiant' => $participe['numEtudiant'], 'idUtil' => self::model('Eleve')->first(array('numEtudiant' => $participe['numEtudiant']), 'idUtil')));
                                    } else {
                                       $erreurs = $participe->errors();
                                       foreach ($erreurs as &$erreurId) {
                                          switch ($erreurId) {
                                             case ParticipeModel::BAD_NOTE_ERROR:
                                                $erreurId = 'Note incorrecte.';
                                                break;
                                          }
                                       }
                                       $this->addVar('erreurs', $erreurs);
                                    }
                                 } else {
                                    $this->addVar('erreurs', array('Impossible d\'ajouter cette note : une autre note existe déjà pour cet élève.'));
                                 }
                              } else {
                                 $this->addVar('erreurs', array('Impossible d\'ajouter cette note : cet élève n\'existe pas.'));
                              }
                           } else {
                              $this->addVar('erreurs', array('Impossible d\'ajouter cette note : au moins l\'un des champs est vide.'));
                           }
                           $this->setSubAction('addNote');
                           HTTPResponse::addHeader('Content-Type: application/xml');
                           $this->page()->useRawView();
                        } else if (HTTPRequest::postExists('note', 'numEtudiant')) {
                           /**
                            * Modification d'une note en Ajax
                            */
                           if (HTTPRequest::post('note') !== '' && HTTPRequest::post('numEtudiant') !== '') {
                              if (self::model('Participe')->exists(array('numEtudiant' => HTTPRequest::post('numEtudiant'), 'idExam' => HTTPRequest::get('idExam')))) {
                                 $participe = self::model('Participe');
                                 $participe['idExam'] = HTTPRequest::get('idExam');
                                 $participe['note'] = HTTPRequest::post('note') === 'ABS' ? null : HTTPRequest::post('note');
                                 $participe['numEtudiant'] = HTTPRequest::post('numEtudiant');
                                 if ($participe->isValid()) {
                                    if ($participe['note'] === null) {
                                       $participe->delete(array('idExam' => $participe['idExam'], 'numEtudiant' => $participe['numEtudiant']));
                                    }
                                    $participe->save();
                                 } else {
                                    $erreurs = $participe->errors();
                                    foreach ($erreurs as &$erreurId) {
                                       switch ($erreurId) {
                                          case ParticipeModel::BAD_NOTE_ERROR:
                                             $erreurId = 'Note incorrecte.';
                                             break;
                                       }
                                    }
                                    $this->addVar('erreurs', $erreurs);
                                 }
                              } else {
                                 $this->addVar('erreurs', array('Impossible de modifier cette note : note inconnue.'));
                              }
                           } else {
                              $this->addVar('erreurs', array('Impossible de modifier cette note : au moins l\'un des champs est vide.'));
                           }
                           $this->setSubAction('editNote');
                           HTTPResponse::addHeader('Content-Type: application/xml');
                           $this->page()->useRawView();
                        } else {
                           $this->addVar('urlPage', HTTPRequest::requestURI());
                           $examen = htmlspecialchars(stripslashes(self::model('Examen')->first(array('idExam' => HTTPRequest::get('idExam')), 'libelle')));
                           $this->addVar('examen', $examen);
                           $listeDesNotes = self::model('Participe')->find(array('idExam' => self::model('Examen')->field('idExam', array('idMat' => $idMatiere))));
                           foreach ($listeDesNotes as &$note) {
                              $idUtil = self::model('Eleve')->first(array('numEtudiant' => $note['numEtudiant']), 'idUtil');
                              $etudiant = self::model('Utilisateur')->first(array('idUtil' => $idUtil));
                              $note['idUtil'] = $idUtil;
                              $note['login'] = htmlspecialchars(stripslashes($etudiant['login']));
                              $note['nom'] = htmlspecialchars(stripslashes($etudiant['nom']));
                              $note['prenom'] = htmlspecialchars(stripslashes($etudiant['prenom']));
                              $note['note'] = !empty($note['note']) ? str_replace('.', ',', $note['note']) : null;
                           }
                           $this->addVar('listeDesNotes', $listeDesNotes);
                           $this->addVar('estResponsable', $estResponsable);
                           $this->setWindowTitle('Notes de l\'examen « ' . $examen . ' »');
                        }
                     }
                  } else {
                     User::addPopup('Désolé, cet examen n\'existe pas.', Popup::ERROR);
                     HTTPResponse::redirect('/prof/' . HTTPRequest::get('promo') . '/' . HTTPRequest::get('module') . '/' . HTTPRequest::get('matiere'));
                  }
               } else {
                  User::addPopup('Désolé, cette matière n\'existe pas.', Popup::ERROR);
                  HTTPResponse::redirect('/prof/' . HTTPRequest::get('promo') . '/' . HTTPRequest::get('module') . '/matières');
               }
            } else {
               User::addPopup('Désolé, ce module n\'existe pas.', Popup::ERROR);
               HTTPResponse::redirect('/prof/' . HTTPRequest::get('promo'));
            }
         } else {
            User::addPopup('Désolé, cette promotion n\'existe pas.', Popup::ERROR);
            HTTPResponse::redirect('/prof/');
         }
      } else {
         HTTPResponse::redirect('/prof/');
      }
   }

   public function etudiant() {
      if (HTTPRequest::get('promo')) {
         //Si la promotion existe
         if (self::model('Promo')->exists(array('libelle' => HTTPRequest::get('promo')))) {
            $this->addVar('promo', HTTPRequest::get('promo'));
            if (preg_match('#^[aeiouy]#', HTTPRequest::get('promo'))) {
               $prefixPromo = 'd\'';
            } else {
               $prefixPromo = 'de ';
            }
            $this->addVar('prefixPromo', $prefixPromo);
            $this->setWindowTitle('Liste des étudiants ' . $prefixPromo . HTTPRequest::get('promo'));
            //Récupèration de la liste des modules correspondants à la promo
            $idPromo = self::model('Promo')->first(array('libelle' => HTTPRequest::get('promo')), 'idPromo');
            $studentsList = self::model('Utilisateur')->find(array('idUtil' => self::model('Eleve')->field('idUtil', array('idPromo' => $idPromo))));
            foreach ($studentsList as &$student) {
               $student['nom'] = htmlspecialchars(stripslashes($student['nom']));
               $student['prenom'] = htmlspecialchars(stripslashes($student['prenom']));
            }
            $this->addVar('listeDesEtudiants', $studentsList);
         } else {
            User::addPopup('Désolé, la promo « ' . HTTPRequest::get('promo') . ' » n\'existe pas.', Popup::ERROR);
            HTTPResponse::redirect('/prof/');
         }
      } else if (HTTPRequest::getExists('idUtil')) {
         if (self::model('Eleve')->exists(array('idUtil' => HTTPRequest::get('idUtil')))) {
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

}