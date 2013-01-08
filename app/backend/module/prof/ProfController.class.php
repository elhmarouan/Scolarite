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
      if (HTTPRequest::get('promo')) {
         //Si la promotion existe
         if (self::model('Promo')->exists(array('libelle' => HTTPRequest::get('promo')))) {
            $this->setWindowTitle('Gestion de la promotion ' . HTTPRequest::get('promo'));
            $this->setSubAction('managePromo');
            $this->addVar('promo', htmlspecialchars(stripslashes(HTTPRequest::get('promo'))));
         } else {
            $this->app()->user()->addPopup('Désolé, la promo « ' . HTTPRequest::get('promo') . ' » n\'existe pas.', Popup::ERROR);
            HTTPResponse::redirect('/prof/');
         }
      } else {
         $this->app()->user()->addPopup('Veuillez sélectionner une promotion avant.');
         HTTPResponse::redirect('/prof/');
      }
   }

   public function module() {
      if (HTTPRequest::get('promo')) {
         //Si la promotion existe
         if (self::model('Promo')->exists(array('libelle' => HTTPRequest::get('promo')))) {
            $this->addVar('promo', HTTPRequest::get('promo'));
            if (HTTPRequest::getExists('module')) {
               //Si le module existe
               if (self::model('Module')->exists(array('libelle' => HTTPRequest::get('module')))) {
                  $this->setWindowTitle('Matières du module ' . HTTPRequest::get('module'));
                  $this->setSubAction('manageMatieres');
                  $this->addVar('module', HTTPRequest::get('module'));
               } else {
                  
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
            $this->app()->user()->addPopup('Désolé, la promo « ' . HTTPRequest::get('promo') . ' » n\'existe pas.', Popup::ERROR);
            HTTPResponse::redirect('/prof/');
         }
      } else {
         $this->app()->user()->addPopup('Veuillez sélectionner une promotion avant.');
         HTTPResponse::redirect('/prof/');
      }
   }

   public function matiere() {
      if (HTTPRequest::get('promo')) {
         //Si la promotion existe
         if (self::model('Promo')->exists(array('libelle' => HTTPRequest::get('promo')))) {
            $this->addVar('promo', HTTPRequest::get('promo'));
            if (HTTPRequest::getExists('module')) {
               //Si le module existe 
               if (self::model('Module')->exists(array('libelle' => HTTPRequest::get('module')))) {
                  $this->addVar('module', HTTPRequest::get('module'));
                  if (HTTPRequest::get('matiere')) {
                     //Si la matiere existe
                     $this->setWindowTitle('Examens de la matière' . HTTPRequest::get('matiere'));
                     
                  }
               }
            }            
         }
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
            $this->app()->user()->addPopup('Désolé, la promo « ' . HTTPRequest::get('promo') . ' » n\'existe pas.', Popup::ERROR);
            HTTPResponse::redirect('/prof/');
         }
      } else {
         $this->app()->user()->addPopup('Veuillez sélectionner une promotion avant.');
         HTTPResponse::redirect('/prof/');
      }
   }
}
