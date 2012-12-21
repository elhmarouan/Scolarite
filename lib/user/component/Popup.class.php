<?php

/**
 * Popup
 * 
 * @author Stanislas Michalak <stanislas.michalak@gmail.com>
 * @package Panda.user.component
 * 
 */
class Popup {

   const ERROR = 1;
   const INFORMATION = 2;
   const SUCCESS = 3;
   const WARNING = 4;
   
   private $_popups = array();
   
   private function _getSession() {
      if (PandaRequest::sessionExists('user.popups')) {
         if (PandaRequest::session('user.popups') !== null && is_array(PandaRequest::session('user.popups'))) {
            $this->_popups = PandaRequest::session('user.popups');
         }
         PandaResponse::unsetSession('user.popups');
      }
   }
   
   public function send($message, $type = self::INFORMATION, $contentIsHtml = false) {
      if(is_string($message) && is_int($type) && is_bool($contentIsHtml)) {
         $message = $contentIsHtml ? $message : htmlspecialchars($message);
         $oldPopupList = PandaRequest::session('user.popups');
         $oldPopupList[] = array('message' => $message, 'type' => $type);
         PandaResponse::setSession('user.popups', $oldPopupList, true);
      }
   }
   
   public function popupList() {
      $this->_getSession();
      return $this->_popups;
   }

}