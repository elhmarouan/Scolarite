<?php

/**
 * Panda controller
 * 
 * @author Stanislas Michalak <stanislas.michalak@gmail.com>
 * @package Panda.core
 * 
 */
class PandaController {

   protected $_app = null;
   protected $_module = '';
   protected $_action = '';
   protected $_view = '';
   protected $_page = null;
   protected $_models = array();

   public function __construct(PandaApplication $app, $module, $action) {
      $this->setApp($app);
      $this->setModule($module);
      $this->setAction($action);
      PandaApplication::load('Panda.core.Page');
      $this->setPage(new Page($app));
      $this->setView($action);
   }

   /**
    * Convenient shortcut to set the window title (the string between the <title></title> tags) 
    * @param string $windowTitle
    * @throws InvalidArgumentException
    */
   public function setWindowTitle($windowTitle) {
      if (!is_string($windowTitle) || empty($windowTitle)) {
         throw new InvalidArgumentException(__('The window title must be a not-empty string.'));
      }
      $this->page()->addVar('windowTitle', $windowTitle);
   }

   public function setSubAction($subAction) {
      if (!is_string($subAction) || empty($subAction)) {
         throw new InvalidArgumentException(__('The sub-action must be a not-empty string.'));
      }
      if (array_key_exists($subAction, $this->page()->vars())) {
         throw new InvalidArgumentException(__('Unable to use this sub-action name: "%s" is already used.', $subAction));
      }
      $this->page()->addVar($subAction);
   }

   /**
    * Execute an action from the current controller
    * @return void
    * @throws RuntimeException
    */
   public function exec() {
      if (!is_callable(array($this, $this->_action))) {
         throw new RuntimeException(__('"%s" action isn\'t defined for this module.', $this->_action));
      }
      return $this->{$this->_action}();
   }

   public function page() {
      return $this->_page;
   }

   public function setApp(PandaApplication $app) {
      $this->_app = $app;
   }

   public function setModule($module) {
      if (is_string($module) && !empty($module)) {
         $this->_module = $module;
      } else {
         throw new InvalidArgumentException(__('Invalid module: the module must be a not-empty string.'));
      }
   }

   public function setAction($action) {
      if (is_string($action) && !empty($action)) {
         if (!in_array($action, array('exec', 'page'))) {
            $this->_action = $action;
         } else {
            throw new InvalidArgumentException(__('Invalid action: the action name can\'t be "exec" or "page".'));
         }
      } else {
         throw new InvalidArgumentException(__('Invalid action: the action must be a not-empty string.'));
      }
   }

   public function setView($view) {
      if (is_string($view) && !empty($view)) {
         $this->_view = $view;
      } else {
         throw new InvalidArgumentException(__('Invalid view: the view must be a not-empty string.'));
      }
      $this->page()->setTemplate(APP_DIR . strtolower($this->_app->name()) . '/module/' . strtolower($this->_module) . '/view/' . strtolower($this->_view) . '.php');
   }

   public function setPage(Page $page) {
      $this->_page = $page;
   }

   public function loadModels($models) {
      $models = (func_num_args() === 1) && is_string($models) ? explode(',', $models) : func_get_args();
      foreach ($models as $modelName) {
         if (!is_string($modelName) || empty($modelName)) {
            throw new InvalidArgumentException(__('Invalid model "%s": not-empty string needed', (string) $modelName));
         }
         $modelName = ucfirst(trim($modelName));
         if (is_file(MODEL_DIR . $modelName . '.class.php')) {
            if (!isset($this->_models[$modelName])) {
               PandaApplication::load('Model.' . $modelName);
               $modelClass = $modelName . 'Model';
               $this->_models[$modelName] = new $modelClass;
            }
         } else {
            throw new InvalidArgumentException(__('Unknown model "%s"', $modelName));
         }
      }
   }
   
   public function model($modelName) {
      if(isset($this->_models[$modelName]) && $this->_models[$modelName] instanceof Model) {
         return $this->_models[$modelName];
      } else {
         throw new InvalidArgumentException(__('Unknown or not-loaded model "%s"', $modelName));
      }
   }

}
