<?php

/**
 * User controller
 * 
 * @author Stanislas Michalak <stanislas.michalak@gmail.com>
 * 
 */
class UserController extends PandaController {
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