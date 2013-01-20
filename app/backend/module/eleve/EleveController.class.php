<?php

/**
 * Élève controller
 * 
 * @author Vincent Simon <simonvince@eisti.eu>
 * 
 */
class EleveController extends Controller {

   public function accessFilter() {
      if (User::isMemberOf('Élève')) {
         return true;
      } else {
         User::addPopup('Vous n\'êtes pas autorisé à accéder à la section élève.', Popup::ERROR);
         HTTPResponse::redirect('/');
      }
   }

   public function index() {
      $this->setWindowTitle('Accueil étudiant');
   }

   /**
    * Résultats personnels
    */
   public function perso() {
      $idPromo = self::model('Eleve')->first(array('idUtil' => User::id()), 'idPromo');
      $numEtudiant = self::model('Eleve')->first(array('idUtil' => User::id()), 'numEtudiant');
      if (HTTPRequest::getExists('module')) {
         if (self::model('Module')->exists(array('libelle' => HTTPRequest::get('module'), 'idPromo' => $idPromo))) {
            $idModule = self::model('Module')->first(array('libelle' => HTTPRequest::get('module'), 'idPromo' => $idPromo), 'idMod');
            $this->addVar('module', htmlspecialchars(stripslashes(HTTPRequest::get('module'))));
            if (HTTPRequest::getExists('matiere')) {
               if (self::model('Matiere')->exists(array('libelle' => HTTPRequest::get('matiere'), 'idMod' => $idModule))) {
                  /**
                   * Consulter une matière
                   */
                  $idMatiere = self::model('Matiere')->first(array('libelle' => HTTPRequest::get('matiere'), 'idMod' => $idModule), 'idMat');
                  $this->addVar('matiere', htmlspecialchars(stripslashes(HTTPRequest::get('matiere'))));
                  $this->setSubAction('voirMatiere');
                  $this->setWindowTitle(HTTPRequest::get('matiere'));

                  //Liste des examens
                  $listeDesExamens = self::model('Examen')->find(array('idMat' => $idMatiere));
                  foreach ($listeDesExamens as &$examen) {
                     $examen['libelle'] = htmlspecialchars(stripslashes($examen['libelle']));
                     $examen['date'] = $examen['date']->format('d/m/Y');
                     $noteEtudiant = self::model('Participe')->first(array('idExam' => $examen['idExam'], 'numEtudiant' => $numEtudiant), 'note');
                     $examen['note'] = !empty($noteEtudiant) ? str_replace('.', ',', round($noteEtudiant, 2)) : null;
                     $notesPromo = self::model('Participe')->field('note', array('idExam' => $examen['idExam'], 'note !=' => null));
                     $examen['moyennePromo'] = !empty($notesPromo) ? str_replace('.', ',', round(array_sum($notesPromo) / count($notesPromo), 2)) : null;
                  }
                  $this->addVar('listeDesExamens', $listeDesExamens);
               } else {
                  User::addPopup('Cette matière n\'existe pas.', Popup::ERROR);
                  HTTPResponse::redirect('/étudiant/perso/' . HTTPRequest::get('module'));
               }
            } else {
               /**
                * Consulter un module
                */
               $this->setSubAction('voirModule');
               $this->setWindowTitle(HTTPRequest::get('module'));
               $listeDesMatieres = self::model('Matiere')->find(array('idMod' => $idModule));
               $quotientModule = 0;
               $moyennesModule = 0;
               foreach ($listeDesMatieres as &$matiere) {
                  $matiere['libelle'] = htmlspecialchars(stripslashes($matiere['libelle']));
                  //Calcul de la moyenne de la matière
                  $listeDesExamens = self::model('Examen')->find(array('idExam' => self::model('Participe')->field('idExam', array('numEtudiant' => $numEtudiant, 'note !=' => null)), 'idMat' => $matiere['idMat']));
                  $quotientExams = 0;
                  $notesPonderes = 0;
                  foreach ($listeDesExamens as &$examen) {
                     $examen['note'] = self::model('Participe')->first(array('idExam' => $examen['idExam'], 'numEtudiant' => $numEtudiant), 'note');
                     $examen['coefExam'] = self::model('TypeExam')->first(array('idType' => $examen['idType']), 'coef');
                     $notesPonderes += $examen['note'] * $examen['coefExam'];
                     $quotientExams += $examen['coefExam'];
                  }
                  if ($notesPonderes !== 0) {
                     $matiere['moyenne'] = str_replace('.', ',', round($notesPonderes / $quotientExams, 2));
                     $moyennesModule += ($notesPonderes / $quotientExams) * $matiere['coefMat'];
                     $quotientModule += $matiere['coefMat'];
                  } else {
                     $matiere['moyenne'] = null;
                  }
               }
               $this->addVar('moyenneModule', $quotientModule !== 0 ? str_replace('.', ',', round($moyennesModule / $quotientModule, 2)) : null);
               $this->addVar('listeDesMatieres', $listeDesMatieres);
            }
         } else {
            User::addPopup('Ce module n\'existe pas.', Popup::ERROR);
            HTTPResponse::redirect('/étudiant/perso');
         }
      } else {
         /**
          * Résultats généraux et liste des modules
          */
         $this->setWindowTitle('Consulter vos résultats');
         $listeDesModules = self::model('Module')->find(array('idPromo' => $idPromo));
         $moyennesModules = array();
         $quotientModules = 0;
         foreach ($listeDesModules as &$module) {
            $module['libelle'] = htmlspecialchars(stripslashes($module['libelle']));
            //Calcul de la moyenne du module
            $listeDesMatieres = self::model('Matiere')->find(array('idMod' => $module['idMod']));
            $moyennesMatieres = array();
            $quotientMatieres = 0;
            foreach ($listeDesMatieres as &$matiere) {
               $listeDesExamens = self::model('Examen')->find(array('idExam' => self::model('Participe')->field('idExam', array('numEtudiant' => $numEtudiant, 'note !=' => null)), 'idMat' => $matiere['idMat']));
               $quotientExams = 0;
               $notesExams = 0;
               foreach ($listeDesExamens as &$examen) {
                  $examen['note'] = self::model('Participe')->first(array('idExam' => $examen['idExam'], 'numEtudiant' => $numEtudiant), 'note');
                  $examen['coefExam'] = self::model('TypeExam')->first(array('idType' => $examen['idType']), 'coef');
                  $notesExams += $examen['note'] * $examen['coefExam'];
                  $quotientExams += $examen['coefExam'];
               }
               if ($quotientExams !== 0) {
                  $moyennesMatieres[] = ($notesExams / $quotientExams) * $matiere['coefMat'];
                  $quotientMatieres += $matiere['coefMat'];
               }
            }
            $coefModule = self::model('Matiere')->avg('coefMat', array('idMod' => $module['idMod']));
            $module['moyenne'] = $quotientMatieres !== 0 ? str_replace('.', ',', round((array_sum($moyennesMatieres) / $quotientMatieres), 2)) : null;
            if ($quotientMatieres !== 0) {
               $moyennesModules[] = (array_sum($moyennesMatieres) / $quotientMatieres) * $coefModule;
               $quotientModules += $coefModule;
            }
         }
         $this->addVar('moyenneGenerale', $quotientModules !== 0 ? str_replace('.', ',', round(array_sum($moyennesModules) / $quotientModules, 2)) : null);
         $this->addVar('listeDesModules', $listeDesModules);
      }
   }

   /**
    * Résultats d'un étudiant de la promotion
    */
   public function buddy() {
      $idPromo = self::model('Eleve')->first(array('idUtil' => User::id()), 'idPromo');
      if (HTTPRequest::getExists('idUtil') && self::model('Eleve')->exists(array('idUtil' => HTTPRequest::get('idUtil'), 'idPromo' => $idPromo))) {
         if ((int) HTTPRequest::get('idUtil') === User::id()) {
            //Si l'id de l'élève est l'id de l'utilisateur actuel, on redirige vers la section perso
            if (HTTPRequest::getExists('module')) {
               if (HTTPRequest::getExists('matiere')) {
                  HTTPResponse::redirect('/étudiant/perso/' . HTTPRequest::get('module') . '/' . HTTPRequest::get('matiere'));
               } else {
                  HTTPResponse::redirect('/étudiant/perso/' . HTTPRequest::get('module'));
               }
            } else {
               HTTPResponse::redirect('/étudiant/perso');
            }
         } else {
            $this->addVar('idEtudiant', HTTPRequest::get('idUtil'));
            $etudiant = array_merge(self::model('Eleve')->first(array('idUtil' => HTTPRequest::get('idUtil'))), self::model('Utilisateur')->first(array('idUtil' => HTTPRequest::get('idUtil'))));
            $etudiant['nom'] = htmlspecialchars(stripslashes($etudiant['nom']));
            $etudiant['prenom'] = htmlspecialchars(stripslashes($etudiant['prenom']));
            $etudiant['login'] = htmlspecialchars(stripslashes($etudiant['login']));
            $numEtudiant = $etudiant['numEtudiant'];
            if (HTTPRequest::getExists('module')) {
               if (self::model('Module')->exists(array('libelle' => HTTPRequest::get('module'), 'idPromo' => $idPromo))) {
                  $idModule = self::model('Module')->first(array('libelle' => HTTPRequest::get('module'), 'idPromo' => $idPromo), 'idMod');
                  $this->addVar('module', htmlspecialchars(stripslashes(HTTPRequest::get('module'))));
                  if (HTTPRequest::getExists('matiere')) {
                     if (self::model('Matiere')->exists(array('libelle' => HTTPRequest::get('matiere'), 'idMod' => $idModule))) {
                        /**
                         * Consulter une matière
                         */
                        $idMatiere = self::model('Matiere')->first(array('libelle' => HTTPRequest::get('matiere'), 'idMod' => $idModule), 'idMat');
                        $this->addVar('matiere', htmlspecialchars(stripslashes(HTTPRequest::get('matiere'))));
                        $this->setSubAction('voirMatiere');
                        $this->setWindowTitle(HTTPRequest::get('matiere'));

                        //Liste des examens
                        $listeDesExamens = self::model('Examen')->find(array('idMat' => $idMatiere));
                        foreach ($listeDesExamens as &$examen) {
                           $examen['libelle'] = htmlspecialchars(stripslashes($examen['libelle']));
                           $examen['date'] = $examen['date']->format('d/m/Y');
                           $noteEtudiant = self::model('Participe')->first(array('idExam' => $examen['idExam'], 'numEtudiant' => $numEtudiant), 'note');
                           $examen['note'] = !empty($noteEtudiant) ? str_replace('.', ',', round($noteEtudiant, 2)) : null;
                           $notesPromo = self::model('Participe')->field('note', array('idExam' => $examen['idExam'], 'note !=' => null));
                           $examen['moyennePromo'] = !empty($notesPromo) ? str_replace('.', ',', round(array_sum($notesPromo) / count($notesPromo), 2)) : null;
                        }
                        $this->addVar('listeDesExamens', $listeDesExamens);
                     } else {
                        User::addPopup('Cette matière n\'existe pas.', Popup::ERROR);
                        HTTPResponse::redirect('/étudiant/perso/' . HTTPRequest::get('module'));
                     }
                  } else {
                     /**
                      * Consulter un module
                      */
                     $this->setSubAction('voirModule');
                     $this->setWindowTitle(HTTPRequest::get('module'));
                     $listeDesMatieres = self::model('Matiere')->find(array('idMod' => $idModule));
                     $quotientModule = 0;
                     $moyennesModule = 0;
                     foreach ($listeDesMatieres as &$matiere) {
                        $matiere['libelle'] = htmlspecialchars(stripslashes($matiere['libelle']));
                        //Calcul de la moyenne de la matière
                        $listeDesExamens = self::model('Examen')->find(array('idExam' => self::model('Participe')->field('idExam', array('numEtudiant' => $numEtudiant, 'note !=' => null)), 'idMat' => $matiere['idMat']));
                        $quotientExams = 0;
                        $notesPonderes = 0;
                        foreach ($listeDesExamens as &$examen) {
                           $examen['note'] = self::model('Participe')->first(array('idExam' => $examen['idExam'], 'numEtudiant' => $numEtudiant), 'note');
                           $examen['coefExam'] = self::model('TypeExam')->first(array('idType' => $examen['idType']), 'coef');
                           $notesPonderes += $examen['note'] * $examen['coefExam'];
                           $quotientExams += $examen['coefExam'];
                        }
                        if ($notesPonderes !== 0) {
                           $matiere['moyenne'] = str_replace('.', ',', round($notesPonderes / $quotientExams, 2));
                           $moyennesModule += ($notesPonderes / $quotientExams) * $matiere['coefMat'];
                           $quotientModule += $matiere['coefMat'];
                        } else {
                           $matiere['moyenne'] = null;
                        }
                     }
                     $this->addVar('moyenneModule', $quotientModule !== 0 ? str_replace('.', ',', round($moyennesModule / $quotientModule, 2)) : null);
                     $this->addVar('listeDesMatieres', $listeDesMatieres);
                  }
               } else {
                  User::addPopup('Ce module n\'existe pas.', Popup::ERROR);
                  HTTPResponse::redirect('/étudiant/perso');
               }
            } else {
               /**
                * Résultats généraux et liste des modules
                */
               $this->setWindowTitle('Consulter les résultats de ' . $etudiant['prenom'] . ' ' . $etudiant['nom'] . ' (' . $etudiant['login'] . ')');
               $this->addVar('etudiant', $etudiant);
               $listeDesModules = self::model('Module')->find(array('idPromo' => $idPromo));
               $moyennesModules = array();
               $quotientModules = 0;
               foreach ($listeDesModules as &$module) {
                  $module['libelle'] = htmlspecialchars(stripslashes($module['libelle']));
                  //Calcul de la moyenne du module
                  $listeDesMatieres = self::model('Matiere')->find(array('idMod' => $module['idMod']));
                  $moyennesMatieres = array();
                  $quotientMatieres = 0;
                  foreach ($listeDesMatieres as &$matiere) {
                     $listeDesExamens = self::model('Examen')->find(array('idExam' => self::model('Participe')->field('idExam', array('numEtudiant' => $numEtudiant, 'note !=' => null)), 'idMat' => $matiere['idMat']));
                     $quotientExams = 0;
                     $notesExams = 0;
                     foreach ($listeDesExamens as &$examen) {
                        $examen['note'] = self::model('Participe')->first(array('idExam' => $examen['idExam'], 'numEtudiant' => $numEtudiant), 'note');
                        $examen['coefExam'] = self::model('TypeExam')->first(array('idType' => $examen['idType']), 'coef');
                        $notesExams += $examen['note'] * $examen['coefExam'];
                        $quotientExams += $examen['coefExam'];
                     }
                     if ($quotientExams !== 0) {
                        $moyennesMatieres[] = ($notesExams / $quotientExams) * $matiere['coefMat'];
                        $quotientMatieres += $matiere['coefMat'];
                     }
                  }
                  $coefModule = self::model('Matiere')->avg('coefMat', array('idMod' => $module['idMod']));
                  $module['moyenne'] = $quotientMatieres !== 0 ? str_replace('.', ',', round((array_sum($moyennesMatieres) / $quotientMatieres), 2)) : null;
                  if ($quotientMatieres !== 0) {
                     $moyennesModules[] = (array_sum($moyennesMatieres) / $quotientMatieres) * $coefModule;
                     $quotientModules += $coefModule;
                  }
               }
               $this->addVar('moyenneGenerale', $quotientModules !== 0 ? str_replace('.', ',', round(array_sum($moyennesModules) / $quotientModules, 2)) : null);
               $this->addVar('listeDesModules', $listeDesModules);
            }
         }
      } else {
         User::addPopup('Cet étudiant n\'existe pas, ou n\'appartient pas à votre promotion.', Popup::ERROR);
         HTTPResponse::redirect('/étudiant/');
      }
   }

   /**
    * Résultats de la promotion
    */
   public function promo() {
      $idPromo = self::model('Eleve')->first(array('idUtil' => User::id()), 'idPromo');
      $numsEtudiants = self::model('Eleve')->field('numEtudiant', array('idPromo' => $idPromo));
      if (HTTPRequest::getExists('action') && HTTPRequest::get('action') === 'liste') {
         /**
          * Liste des étudiants de la promotion
          */
         $this->setSubAction('afficherListe');
         $this->setWindowTitle('Liste des étudiants de votre promotion');
         $idsEtudiantsPromo = self::model('Eleve')->field('idUtil', array('idPromo' => $idPromo, 'idUtil !=' => User::id()));
         $listeDesEtudiants = self::model('Utilisateur')->find(array('idUtil' => $idsEtudiantsPromo));
         foreach ($listeDesEtudiants as &$etudiant) {
            $etudiant['numEtudiant'] = self::model('Eleve')->first(array('idUtil' => $etudiant['idUtil']), 'numEtudiant');
            $etudiant['login'] = htmlspecialchars(stripslashes($etudiant['login']));
            $etudiant['nom'] = htmlspecialchars(stripslashes($etudiant['nom']));
            $etudiant['prenom'] = htmlspecialchars(stripslashes($etudiant['prenom']));
         }
         $this->addVar('listeDesEtudiants', $listeDesEtudiants);
      } else if (HTTPRequest::getExists('module')) {
         if (self::model('Module')->exists(array('libelle' => HTTPRequest::get('module'), 'idPromo' => $idPromo))) {
            $idModule = self::model('Module')->first(array('libelle' => HTTPRequest::get('module'), 'idPromo' => $idPromo), 'idMod');
            $this->addVar('module', htmlspecialchars(stripslashes(HTTPRequest::get('module'))));
            if (HTTPRequest::getExists('matiere')) {
               if (self::model('Matiere')->exists(array('libelle' => HTTPRequest::get('matiere'), 'idMod' => $idModule))) {
                  /**
                   * Consulter une matière
                   */
                  $idMatiere = self::model('Matiere')->first(array('libelle' => HTTPRequest::get('matiere'), 'idMod' => $idModule), 'idMat');
                  $this->addVar('matiere', htmlspecialchars(stripslashes(HTTPRequest::get('matiere'))));
                  $this->setSubAction('voirMatiere');
                  $this->setWindowTitle(HTTPRequest::get('matiere'));

                  //Liste des examens
                  $listeDesExamens = self::model('Examen')->find(array('idMat' => $idMatiere));
                  foreach ($listeDesExamens as &$examen) {
                     $examen['libelle'] = htmlspecialchars(stripslashes($examen['libelle']));
                     $examen['date'] = $examen['date']->format('d/m/Y');
                     $notesPromo = self::model('Participe')->field('note', array('idExam' => $examen['idExam'], 'note !=' => null));
                     $examen['moyennePromo'] = !empty($notesPromo) ? str_replace('.', ',', round(array_sum($notesPromo) / count($notesPromo), 2)) : null;
                  }
                  $this->addVar('listeDesExamens', $listeDesExamens);
               } else {
                  User::addPopup('Cette matière n\'existe pas.', Popup::ERROR);
                  HTTPResponse::redirect('/étudiant/perso/' . HTTPRequest::get('module'));
               }
            } else {
               /**
                * Consulter un module
                */
               $this->setSubAction('voirModule');
               $this->setWindowTitle(HTTPRequest::get('module'));
               $listeDesMatieres = self::model('Matiere')->find(array('idMod' => $idModule));
               $quotientModule = 0;
               $moyennesModule = 0;
               foreach ($listeDesMatieres as &$matiere) {
                  $matiere['libelle'] = htmlspecialchars(stripslashes($matiere['libelle']));
                  //Calcul de la moyenne de la matière
                  $listeDesExamens = self::model('Examen')->find(array('idExam' => self::model('Participe')->field('idExam', array('numEtudiant' => $numsEtudiants)), 'idMat' => $matiere['idMat']));
                  $quotientExams = 0;
                  $notesPonderes = 0;
                  foreach ($listeDesExamens as &$examen) {
                     $examen['note'] = self::model('Participe')->field('note', array('idExam' => $examen['idExam'], 'numEtudiant' => $numsEtudiants, 'note !=' => null));
                     $examen['coefExam'] = self::model('TypeExam')->first(array('idType' => $examen['idType']), 'coef');
                     $notesPonderes += array_sum($examen['note']) * $examen['coefExam'];
                     $quotientExams += count($examen['note']) * $examen['coefExam'];
                  }
                  if ($notesPonderes !== 0) {
                     $matiere['moyenne'] = str_replace('.', ',', round($notesPonderes / $quotientExams, 2));
                     $moyennesModule += ($notesPonderes / $quotientExams) * $matiere['coefMat'];
                     $quotientModule += $matiere['coefMat'];
                  } else {
                     $matiere['moyenne'] = null;
                  }
               }
               $this->addVar('moyenneModule', $quotientModule !== 0 ? str_replace('.', ',', round($moyennesModule / $quotientModule, 2)) : null);
               $this->addVar('listeDesMatieres', $listeDesMatieres);
            }
         } else {
            User::addPopup('Ce module n\'existe pas.', Popup::ERROR);
            HTTPResponse::redirect('/étudiant/perso');
         }
      } else {
         /**
          * Résultats généraux et liste des modules
          */
         $this->setWindowTitle('Résultats de votre promotion');
         $listeDesModules = self::model('Module')->find(array('idPromo' => $idPromo));
         $moyennesModules = array();
         $quotientModules = 0;
         foreach ($listeDesModules as &$module) {
            $module['libelle'] = htmlspecialchars(stripslashes($module['libelle']));
            //Calcul de la moyenne du module
            $listeDesMatieres = self::model('Matiere')->find(array('idMod' => $module['idMod']));
            $moyennesMatieres = array();
            $quotientMatieres = 0;
            foreach ($listeDesMatieres as &$matiere) {
               $listeDesExamens = self::model('Examen')->find(array('idExam' => self::model('Participe')->field('idExam', array('numEtudiant' => $numsEtudiants, 'note !=' => null)), 'idMat' => $matiere['idMat']));
               $quotientExams = 0;
               $notesExams = 0;
               foreach ($listeDesExamens as &$examen) {
                  $examen['note'] = self::model('Participe')->field('note', array('idExam' => $examen['idExam'], 'numEtudiant' => $numsEtudiants));
                  $examen['coefExam'] = self::model('TypeExam')->first(array('idType' => $examen['idType']), 'coef');
                  $notesExams += array_sum($examen['note']) * $examen['coefExam'];
                  $quotientExams += count($examen['note']) * $examen['coefExam'];
               }
               if ($quotientExams !== 0) {
                  $moyennesMatieres[] = ($notesExams / $quotientExams) * $matiere['coefMat'];
                  $quotientMatieres += $matiere['coefMat'];
               }
            }
            $coefModule = self::model('Matiere')->avg('coefMat', array('idMod' => $module['idMod']));
            $module['moyenne'] = $quotientMatieres !== 0 ? str_replace('.', ',', round((array_sum($moyennesMatieres) / $quotientMatieres), 2)) : null;
            if ($quotientMatieres !== 0) {
               $moyennesModules[] = (array_sum($moyennesMatieres) / $quotientMatieres) * $coefModule;
               $quotientModules += $coefModule;
            }
         }
         $this->addVar('moyenneGenerale', $quotientModules !== 0 ? str_replace('.', ',', round(array_sum($moyennesModules) / $quotientModules, 2)) : null);
         $this->addVar('listeDesModules', $listeDesModules);
      }
   }

}