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
class Acl extends UserComponent {
   private $_rights = array();
   private $_groups = array();
   
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
         $this->_groups = $this->user()->model()->getGroupsOf($this->user()->id());
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
         $userRights = $this->user()->model()->getRightsOf($this->user()->id());

         $rights = array_merge($groupRights, $userRights);
         $this->_rights = empty($rights) ? array() : $this->_model()->getRightsData($rights);
      }
      return $this->_rights;
   }

}