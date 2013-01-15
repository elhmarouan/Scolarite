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
      $this->setWindowTitle('Accueil élève');
   }

   public function perso() {
      $this->setWindowTitle('Informations personnelles');
   }

   public function promotion() {
      if (HTTPRequest::getExists('promo')) {
         $this->setWindowTitle('Consultation de la promotion ' . HTTPRequest::get('promo'));
         $this->setSubAction('manageClass');
         $this->addVar('promo', stripslashes(htmlspecialchars(HTTPRequest::get('promo'))));
      } else {
         $this->setWindowTitle('Consultaion des promotions');
      }
   }

   public function matieres() {
      if (HTTPRequest::getExists('promo') && HTTPRequest::getExists('module') && HTTPRequest::getExists('matiere')) {
         $this->setWindowTitle('Gestion de ' . HTTPRequest::get('matiere'));
         $this->addVar('matiere', HTTPRequest::get('matiere'));
      }
   }

   public function notes() {
         
                          
      $numUtil = self::model('Participe')->find(array('numEtudiant' => self::model('Utilisateur')->first(array('idUtil' => User::id()), 'numEtudiant')));
      debug($numUtil);
      
      $idUtilisateur = User::id();
      $donneesUtil = self::model('Utilisateur')->first(array('idUtil' => $idUtilisateur));      
      

      $notesPromo = self::model('Examen')->first(array('idExam', 'idMat' => self::model('Promo')->first(array('idPromo' => Promo::id(), 'idExam' ))));
      debug($notesPromo);
      
      $notesUtil = self::model('Examen')->first(array('idExam', 'idMat' => self::model('Utilisateur')->first(array('idUtil' => User::id(), 'idExam' ))));
      debug($notesUtil);
      
   }

   public function moyennes() {

   }

}