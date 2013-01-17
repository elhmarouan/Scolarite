<?php

/**
 * User controller
 * 
 * @author Stanislas Michalak <stanislas.michalak@gmail.com>
 * 
 */
class UserController extends Controller {
   public function connexion() {
      if (HTTPRequest::postExists('login') && HTTPRequest::postExists('password')) {
         $utilisateur = $this->model('Utilisateur');
         if ($utilisateur->exists(array('login' => HTTPRequest::post('login')))) {
            $passwordToCheck = __hash(HTTPRequest::post('password'), Config::read('salt.user.prefix'), Config::read('salt.user.suffix'));
            if ($utilisateur->exists(array('login' => HTTPRequest::post('login'), 'pass' => $passwordToCheck))) {
               $idUtil = $utilisateur->first(array('login' => HTTPRequest::post('login')), 'idUtil');
               $role = self::model('Role')->first(array('idRole' => $utilisateur->first(array('login' => HTTPRequest::post('login')), 'idRole')), 'libelle');
               $this->app()->user()->login($idUtil, HTTPRequest::post('login'));
               User::addPopup('Connexion réussie !', Popup::SUCCESS);
               switch ($role) {
                  case 'Administrateur' :
                     if (preg_match('#^/admin/#', HTTPRequest::requestURI())) {
                        HTTPResponse::redirect(HTTPRequest::requestURI());
                     } else {
                        HTTPResponse::redirect('/admin/');
                     }
                     break;
                  case 'Professeur' :
                     if (preg_match('#^/prof/#', HTTPRequest::requestURI())) {
                        HTTPResponse::redirect(HTTPRequest::requestURI());
                     } else {
                        HTTPResponse::redirect('/prof/');
                     }
                     break;
                  case 'Élève' :
                     if (preg_match('#^/eleve/#', HTTPRequest::requestURI())) {
                        HTTPResponse::redirect(HTTPRequest::requestURI());
                     } else {
                        HTTPResponse::redirect('/eleve/');
                     }
                     break;
               }
               
            } else {
               User::addPopup('Mot de passe erroné.', Popup::ERROR);
            }
         } else {
            User::addPopup('Ce login n\'appartient à aucun utilisateur.', Popup::ERROR);
         }
      }
      $this->setWindowTitle('Connexion');
   }
}