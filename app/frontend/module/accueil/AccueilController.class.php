<?php

/**
 * Accueil controller
 * 
 * @author Stanislas Michalak <stanislas.michalak@gmail.com>
 * 
 */
class AccueilController extends PandaController {
   public function index() {
      $this->setWindowTitle('Scolarité');
   }
}