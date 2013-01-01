<?php

/**
 * Panda controller
 * 
 * @author Stanislas Michalak <stanislas.michalak@gmail.com>
 * @package Panda.core
 * 
 */
class Controller {

   protected $_app = null;
   protected $_module = '';
   protected $_action = '';
   protected $_view = '';
   protected $_page = null;
   protected static $_models = array();

   public function __construct(Application $app, $module, $action) {
      $this->setApp($app);
      $this->setModule($module);
      $this->setAction($action);
      Application::load('Panda.page.Page');
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

   /**
    * Convenient shortcut to set the current sub-action in the view 
    * @param string $subAction
    * @throws InvalidArgumentException
    */
   public function setSubAction($subAction) {
      if (!is_string($subAction) || empty($subAction)) {
         throw new InvalidArgumentException(__('The sub-action must be a not-empty string.'));
      }
      if (array_key_exists($subAction, $this->page()->vars())) {
         throw new InvalidArgumentException(__('Unable to use this sub-action name: "%s" is already used.', $subAction));
      }
      $this->page()->addVar($subAction);
   }
   
   public function addVar($varName, $var) {
      $this->page()->addVar($varName, $var);
   }

   /**
    * Executes an action from the current controller
    * @return void
    * @throws RuntimeException
    */
   public function exec() {
      if ($this->accessFilter()) {
         if (!is_callable(array($this, $this->_action))) {
            throw new RuntimeException(__('"%s" action isn\'t defined for this module.', $this->_action));
         }
         return $this->{$this->_action}();
      } else {
         HTTPResponse::redirect403($this->app());
      }
   }

   public function app() {
      return $this->_app;
   }
   
   public function page() {
      return $this->_page;
   }

   public function setApp(Application $app) {
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
         if (!in_array($action, array('exec', 'app', 'page'))) {
            $this->_action = $action;
         } else {
            throw new InvalidArgumentException(__('Invalid action: the action name can\'t be "exec", "app" or "page".'));
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

   protected static function _loadModel($model) {
      if (!is_string($model) || empty($model)) {
         throw new InvalidArgumentException(__('Invalid model "%s": not-empty string needed', (string) $model));
      }
      $model = ucfirst(trim($model));
      if (is_file(MODEL_DIR . $model . '.class.php')) {
         if (!isset(self::$_models[$model])) {
            Application::load('Model.' . $model);
            $modelClass = $model . 'Model';
            self::$_models[$model] = new $modelClass;
         }
      } else {
         throw new InvalidArgumentException(__('Unknown model "%s"', $model));
      }
   }

   public static function model($modelName) {
      if (!isset(self::$_models[$modelName])) {
         self::_loadModel($modelName);
      }
      return self::$_models[$modelName];
   }
   
   /**
    * Use this method to allow the controller access
    * to a group of users only. By default, this method
    * is empty and allow the access to everyone.
    * 
    * Unlike the Application accessFilter method,
    * it can only return a boolean : true if the access
    * is granted, false else. The return value is optional
    * if you handle the cases by yourself (for instance, you
    * can redirect an user to a specific page, if the access isn't
    * granted, instead of displaying a HTTP 403 error).
    * 
    * @see Application::accessFilter
    * @return bool|void
    */
   public function accessFilter() {
      return true;
   }

}