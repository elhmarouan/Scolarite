<?php

/**
 * Prof controller
 * 
 * @author Soheil Dahmani <dahmanisou@eisti.eu>
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
                  $this->setWindowTitle('Matières du module ' . HTTPRequest::get('module'));
                  $this->setSubAction('manageMatieres');
                  $this->addVar('module', HTTPRequest::get('module'));
                  
                  //Récupération de la liste des matières
                  $listeDesMatieres = self::model('Matiere')->field('libelle', array('idMod' => self::model('Module')->first(array('libelle' => HTTPRequest::get('module'), 'idPromo' => self::model('Promo')->first(array('libelle' => HTTPRequest::get('promo')), 'idPromo')), 'idMod')));
                  foreach ($listeDesMatieres as &$matiere) {
                     $matiere = htmlspecialchars(stripslashes($matiere));
                  }
                  $this->addVar('listeDesMatieres', $listeDesMatieres);
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
                  //Liste des examens
                  $listeDesExamens = self::model('Examen')->find(array('idMat' => $idMatiere));
                  foreach ($listeDesExamens as &$examen) {
                     $examen['libelle'] = htmlspecialchars(stripslashes($examen['libelle']));
                     $examen['date'] = $examen['date']->format('d/m/Y');
                     $examen['type'] = htmlspecialchars(stripslashes(self::model('TypeExam')->first(array('idType' => $examen['idType']), 'libelle')));
                  }
                  $this->addVar('listeDesExamens', $listeDesExamens);
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
                  $this->addVar('matiere', HTTPRequest::get('matiere'));
                  $this->addVar('examen', htmlspecialchars(stripslashes(self::model('Examen')->first(array('idExam' => HTTPRequest::get('idExam')), 'libelle'))));
                  $this->addVar('idExam', HTTPRequest::get('idExam'));
                  $idMatiere = self::model('Matiere')->first(array('libelle' => HTTPRequest::get('matiere'), 'idMod' => $idModule), 'idMat');
                  $listeDesNotes = self::model('Participe')->find(array('idExam' => self::model('Examen')->field('idExam', array('idMat' => $idMatiere))));
                  foreach ($listeDesNotes as &$note) {
                     $etudiant = self::model('Utilisateur')->first(array('idUtil' => self::model('Eleve')->first(array('numEtudiant' => $note['numEtudiant']), 'idUtil')));
                     $note['login'] = htmlspecialchars(stripslashes($etudiant['login']));
                     $note['nom'] = htmlspecialchars(stripslashes($etudiant['nom']));
                     $note['prenom'] = htmlspecialchars(stripslashes($etudiant['prenom']));
                     $note['note'] = !empty($note['note']) ? str_replace('.', ',', $note['note']) : null;
                  }
                  $this->addVar('listeDesNotes', $listeDesNotes);
                  $this->addVar('estResponsable', self::model('Matiere')->exists(array('idProf' => self::model('Prof')->first(array('idUtil' => User::id()), 'idProf'), 'idMat' => $idMatiere)));
                  $this->setWindowTitle('Gestion des Notes' . HTTPRequest::get('examen'));
               } else {
                  User::addPopup('Désolé, cet examen n\'existe pas.', Popup::ERROR);
                  HTTPResponse::redirect('/prof/' . HTTPRequest::get('promo'));
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
      } else {
         User::addPopup('Veuillez sélectionner une promotion avant.');
         HTTPResponse::redirect('/prof/');
      }
   }

}
