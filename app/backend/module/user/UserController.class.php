<?php

/**
 * User controller
 * 
 * @author Stanislas Michalak <stanislas.michalak@gmail.com>
 * 
 */
class UserController extends PandaController {
   public function connexion() {
      if (PandaRequest::postExists('login') && PandaRequest::postExists('password')) {
         $utilisateur = $this->model('Utilisateur');
         if ($utilisateur->exists(array('login' => PandaRequest::post('login')))) {
            $passwordToCheck = __hash(PandaRequest::post('password'), Config::read('salt.user.prefix'), Config::read('salt.user.suffix'));
            if ($utilisateur->exists(array('login' => PandaRequest::post('login'), 'pass' => $passwordToCheck))) {
               $idUtil = $utilisateur->first(array('login' => PandaRequest::post('login')), 'idUtil');
               $this->app()->user()->login($idUtil, PandaRequest::post('login'));
               User::addPopup('Connexion réussie !', Popup::SUCCESS);
            } else {
               User::addPopup('Mot de passe erroné.', Popup::ERROR);
            }
         } else {
            User::addPopup('Ce login n\'appartient à aucun utilisateur.', Popup::ERROR);
         }
      }
      $this->setWindowTitle('Connexion');
   }
   
   public function deconnexion() {
      if (!User::isOnline()) {
         User::addPopup('Vous êtes déjà déconnecté.', Popup::ERROR);
      } else {
         $this->app()->user()->logout();
         User::addPopup('Déconnexion réussie !', Popup::SUCCESS);
      }
      PandaResponse::redirect('/');
   }
}