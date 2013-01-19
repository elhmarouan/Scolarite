<?php

/**
 * Breadcrumb
 * 
 * @author Stanislas Michalak <stanislas.michalak@gmail.com>
 * @package Panda.page.helper
 * 
 */
class Breadcrumb {
   
   private $_items = array();
   
   public function __construct() {
      $this->add(__('Home'), Config::read('roots.default'));
   }
   
   public function add($label, $link = null) {
      if (is_string($label) && !empty($label)) {
         if ($link === null || filter_var($link, FILTER_VALIDATE_URL)) {
            $this->_items[] = array('label' => $label, 'url' => $link);
            return $this;
         } else {
            throw new InvalidArgumentException(__('Unable to add "%s" item to the breadcrumb: invalid link.', $label));
         }
      } else {
         throw new InvalidArgumentException(__('Unable to add "%s" item to the breadcrumb: invalid label.', (string) $label));
      }
   }
   
   public function build() {
      $breadcrumb = '';
      if (!empty($this->_items)) {
         foreach ($this->_items as $item) {
            if ($item['url'] !== null) {
               $breadcrumb .= '<a href="'.$item['url'].'">' . htmlspecialchars(stripslashes($item['label'])) . '</a> &gt; ';
            } else {
               $breadcrumb .= htmlspecialchars(stripslashes($item['label'])) . ' &gt; ';
            }
         }
      }
      return rtrim($breadcrumb, ' >');
   }
}