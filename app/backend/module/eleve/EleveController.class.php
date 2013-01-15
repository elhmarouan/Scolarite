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
      $this->setWindowTitle('Accueil Etudiant');
   }

   public function etudiant() {
      $this->setWindowTitle('Informations Personnelles');
   }

   public function promotion() {
      $this->setWindowTitle('Informations Promotion');
   }
   
   public function matieres() {
      if (HTTPRequest::getExists('promo') && HTTPRequest::getExists('module') && HTTPRequest::getExists('matiere')) {
         $this->setWindowTitle('Consultation de ' . HTTPRequest::get('matiere'));
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