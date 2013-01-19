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
   
   public function perso() {
      $this->setWindowTitle('Consulter vos résultats');
      $numEtudiant = self::model('Eleve')->first(array('idUtil' => User::id()), 'numEtudiant');
      $listeDesModules = self::model('Module')->find(array('idPromo' => self::model('Eleve')->first(array('idUtil' => User::id()), 'idPromo')));
      $moyennesModules = array();
      $quotientModules = 0;
      foreach ($listeDesModules as &$module) {
         $module['libelle'] = htmlspecialchars(stripslashes($module['libelle']));
         //Calcul de la moyenne du module
         $listeDesExamens = self::model('Examen')->find(array('idExam' => self::model('Participe')->field('idExam', array('numEtudiant' => $numEtudiant)), 'idMat' => self::model('Matiere')->field('idMat', array('idMod' => $module['idMod']))));
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