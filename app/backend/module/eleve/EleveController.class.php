<?php

/**
 * Éleve controller
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
                     $notesPromo = self::model('Participe')->field('note', array('idExam' => $examen['idExam']));
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
               foreach ($listeDesMatieres as &$matiere) {
                  $matiere['libelle'] = htmlspecialchars(stripslashes($matiere['libelle']));
               }
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
            $listeDesExamens = self::model('Examen')->find(array('idExam' => self::model('Participe')->field('idExam', array('numEtudiant' => $numEtudiant, 'note !=' => null)), 'idMat' => self::model('Matiere')->field('idMat', array('idMod' => $module['idMod']))));
            $quotientExams = 0;
            $notesPonderes = 0;
            foreach ($listeDesExamens as &$examen) {
               $examen['note'] = self::model('Participe')->first(array('idExam' => $examen['idExam'], 'numEtudiant' => $numEtudiant), 'note');
               $examen['coefExam'] = self::model('TypeExam')->first(array('idType' => $examen['idType']), 'coef');
               $examen['coefMat'] = self::model('Matiere')->first(array('idMat' => $examen['idMat']), 'coefMat');
               $notesPonderes += $examen['note'] * $examen['coefExam'] * $examen['coefMat'];
               $quotientExams += $examen['coefExam'] * $examen['coefMat'];
            }
            if ($notesPonderes !== 0) {
               $coefsMatieres = self::model('Matiere')->field('coefMat', array('idMod' => $module['idMod']));
               $coefModule = array_sum($coefsMatieres) / count($coefsMatieres);
               $moyennesModules[] = ($notesPonderes / $quotientExams) * $coefModule;
               $quotientModules += $coefModule;
               $module['moyenne'] = str_replace('.', ',', round($notesPonderes / $quotientExams, 2));
            } else {
               $module['moyenne'] = null;
            }
         }
         $this->addVar('moyenneGenerale', $quotientModules !== 0 ? str_replace('.', ',', round(array_sum($moyennesModules) / $quotientModules, 2)) : null);
         $this->addVar('listeDesModules', $listeDesModules);
      }
   }

   /**
    * Résultats de la promotion
    */
   public function promo() {
      $idPromo = self::model('Eleve')->first(array('idUtil' => User::id()), 'idPromo');
      $numsEtudiants = self::model('Eleve')->field('numEtudiant', array('idPromo' => $idPromo));
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
         $listeDesExamens = self::model('Examen')->find(array('idExam' => self::model('Participe')->field('idExam', array('numEtudiant' => $numsEtudiants, 'note !=' => null)), 'idMat' => self::model('Matiere')->field('idMat', array('idMod' => $module['idMod']))));
         $quotientExams = 0;
         $notesPonderes = 0;
         foreach ($listeDesExamens as &$examen) {
            $examen['note'] = self::model('Participe')->field('note', array('idExam' => $examen['idExam'], 'numEtudiant' => $numsEtudiants));
            $examen['coefExam'] = self::model('TypeExam')->first(array('idType' => $examen['idType']), 'coef');
            $examen['coefMat'] = self::model('Matiere')->first(array('idMat' => $examen['idMat']), 'coefMat');
            $notesPonderes += array_sum($examen['note']) * $examen['coefExam'] * $examen['coefMat'];
            $quotientExams += count($examen['note']) * $examen['coefExam'] * $examen['coefMat'];
         }
         if ($notesPonderes !== 0) {
            $coefsMatieres = self::model('Matiere')->field('coefMat', array('idMod' => $module['idMod']));
            $coefModule = array_sum($coefsMatieres) / count($coefsMatieres);
            $moyennesModules[] = ($notesPonderes / $quotientExams) * $coefModule;
            $quotientModules += $coefModule;
            $module['moyenne'] = str_replace('.', ',', round($notesPonderes / $quotientExams, 2));
         } else {
            $module['moyenne'] = null;
         }
      }
      $this->addVar('moyenneGenerale', $quotientModules !== 0 ? str_replace('.', ',', round(array_sum($moyennesModules) / $quotientModules, 2)) : null);
      $this->addVar('listeDesModules', $listeDesModules);
   }

}