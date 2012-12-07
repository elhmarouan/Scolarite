<?php

/**
 * Page
 * 
 * @author Stanislas Michalak <stanislas.michalak@gmail.com>
 * @package Panda.core
 * 
 */
class Page {

   private $_appName;
   private $_error;
   private $_template;
   private $_vars = array();

   public function __construct($app) {
      $this->setAppName($app->name());
   }

   public function addVar() {
      $numArg = func_num_args();
      $args = func_get_args();
      if ($numArg === 1) {
         if (is_array($args[0]) && !empty($args[0])) {
            foreach ($args[0] as $varName => $value) {
               if (is_string($varName) && !empty($varName) && !is_numeric($varName)) {
                  $this->_vars[$varName] = $value;
               } else {
                  throw new InvalidArgumentException(__('Unable to add the var to the view: invalid var "%s".', $varName));
               }
            }
         } else if (is_string($args[0]) && !empty($args[0]) && !is_numeric($args[0])) {
            $this->_vars[$args[0]] = true;
         } else {
            throw new InvalidArgumentException(__('Unable to add the var to the view: the parameter must be an array or a not-empty string.'));
         }
      } else if ($numArg === 2) {
         if (is_string($args[0]) && !empty($args[0]) && !is_numeric($args[0])) {
            $this->_vars[$args[0]] = $args[1];
         } else {
            throw new InvalidArgumentException(__('Unable to add the var to the view: the first parameter must be a not-empty string.'));
         }
      } else {
         throw new InvalidArgumentException(__('Unable to add the var to the view: too much arguments.'));
      }
   }

   public function build() {
      extract($this->vars());
      $windowTitle = isset($windowTitle) ? $windowTitle : 'Undefined';
      try {
         $root = Config::read('root.default');
      } catch(ErrorException $e) {
         if($e->getCode() === Config::UNKNOWN_KEY) {
            $root = '/';
         } else {
            throw $e;
         }
      }
      ob_start();
      require $this->template();
      $content = ob_get_clean();
      if(phpversion() >= 5.4) { 
         ob_start('ob_gzhandler');
      } else {
         ob_start('zlib.output_compression');
      }
      if (!$this->error()) {
         require APP_DIR . $this->appName() . '/template/layout.php';
      } else {
         echo $content;
      }

      return ob_get_clean();
   }

   public function appName() {
      return $this->_appName;
   }

   public function error() {
      return $this->_error;
   }

   public function template() {
      return $this->_template;
   }

   public function vars() {
      return $this->_vars;
   }

   public function setAppName($appName) {
      if (is_string($appName) && !empty($appName)) {
         $this->_appName = strtolower($appName);
      } else {
         throw new InvalidArgumentException(__('"%s" application doesn\'t exists.', $appName));
      }
   }

   public function setTemplate($template) {
      if (!is_string($template) || empty($template)) {
         throw new InvalidArgumentException(__('Invalid view: must be a not-empty string.'));
      }
      if (!file_exists($template)) {
         throw new RuntimeException(__('The specified view doesn\'t exists.'));
      }

      $this->_template = $template;
   }

   public function setError() {
      $this->_error = true;
   }

   public function unsetError() {
      $this->_error = false;
   }

}