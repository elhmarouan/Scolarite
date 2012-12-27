<?php

/**
 * Access Control List
 * 
 * An adaptive and powerful user's rights manager.
 * 
 * @package Panda framework
 * @author Stanislas Michalak <stanislas.michalak@gmail.com>
 * 
 */
class Acl {
   private $_rights = array();
   private $_groups = array();
   private $_model;
   
   private function _model() {
      if ($this->_model === null) {
         try {
            $modelName = Config::read('user.acl.model');
         } catch (Exception $e) {
            if ($e->getCode() === Config::UNKNOWN_KEY) {
               throw new RuntimeException(__('Unable to use the Acl component: please set the user.model key in the configuration file.'));
            }
         }
         PandaApplication::load('Model.' . $modelName);
         $modelName .= 'Model';
         $this->_model = new $modelName;
      }
      return $this->_model;
   }
   
   public function isAllowedTo($rightKey) {
      $rights = $this->_getUserRights();
      if (!empty($rights)) {
         foreach ($rights as $right) {
            if ($right['key'] === $rightKey) {
               return true;
            }
         }
      }
      return false;
   }
   
   public function isMemberOf($groupKey) {
      $groups = $this->_getUserGroups();
      if (!empty($groups)) {
         foreach ($groups as $group) {
            if ($group['key'] === $groupKey) {
               return true;
            }
         }
      }
      return false;
   }
   
   private function _getUserGroups() {
      if (empty($this->_groups)) {
         $this->_groups = $this->_model()->getGroupsOf(PandaRequest::session('user.id'));
      }
      return $this->_groups;
   }

   private function _getUserRights() {
      if (empty($this->_rights)) {
         $groupRights = array();

         //Get group rights
         $groups = $this->getUserGroups();

         if (count($groups) > 0) {
            foreach ($groups as $group) {
               foreach ($group['rights'] as $right) {
                  if (!in_array($right, $groupRights)) {
                     $groupRights[] = $right;
                  }
               }
            }
         }

         //Get user rights
         $userRights = $this->_model()->getRightsOf(PandaRequest::session('user.id'));

         $rights = array_merge($groupRights, $userRights);
         $this->_rights = empty($rights) ? array() : $this->_model()->getRightsData($rights);
      }
      return $this->_rights;
   }

}