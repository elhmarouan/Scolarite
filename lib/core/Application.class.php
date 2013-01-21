<?php

/**
 * Panda application
 * 
 * @author Stanislas Michalak <stanislas.michalak@gmail.com>
 * @package Panda.core
 * 
 */
abstract class Application {

   protected $_name;
   protected $_user;

   public function __construct($appName) {
      self::load('Panda.core.Config');
      self::load('Panda.http.HTTPRequest');
      self::load('Panda.http.HTTPResponse');
      self::load('Panda.error.ErrorHandler');
      $this->setName($appName);
      Config::setAppName($appName);
      self::load('Panda.user.User');
      $this->_user = new User;
   }

   /**
    * Get, if it exists, the controller matching with the current url
    * @return Controller
    */
   public function getController() {
      self::load('Panda.router.Router');
      $router = new Router;
      $router->loadKnownRoutes($this->name());

      //Try to find a matching route
      try {
         $matchedRoute = $router->getRoute(HTTPRequest::requestURI());
      } catch (RuntimeException $error) {
         if ($error->getCode() === Router::NO_ROUTE_FOUND) {
            HTTPResponse::redirect404($this);
         }
      }

      $_GET = array_merge($_GET, $matchedRoute->vars());
      $controllerClass = $matchedRoute->module() . 'Controller';
      //and load the matching controller
      self::load('App.' . strtolower($this->name()) . '.module.' . strtolower($matchedRoute->module()) . '.' . $matchedRoute->module() . 'Controller');
      return new $controllerClass($this, $matchedRoute->module(), $matchedRoute->action());
   }

   public static function load($className) {
      $className = explode('.', $className);
      if ($className[0] === 'Panda') {
         unset($className[0]);
         if (is_file(LIB_DIR . implode('/', $className) . '.class.php')) {
            require_once LIB_DIR . implode('/', $className) . '.class.php';
         } else if (is_file(LIB_DIR . implode('/', $className) . '.php')) {
            require_once LIB_DIR . implode('/', $className) . '.php';
         } else {
            throw new InvalidArgumentException(__('Unable to load "%s" lib component.', implode('.', $className)));
         }
      } else if ($className[0] === 'App') {
         unset($className[0]);
         if (is_file(APP_DIR . implode('/', $className) . '.class.php')) {
            require_once APP_DIR . implode('/', $className) . '.class.php';
         } else {
            throw new InvalidArgumentException(__('Unable to load "%s" app component.', implode('.', $className)));
         }
      } else if ($className[0] === 'Model') {
         unset($className[0]);
         if (is_file(MODEL_DIR . $className[1] . '.class.php')) {
            require_once MODEL_DIR . $className[1] . '.class.php';
         } else {
            throw new InvalidArgumentException(__('Unable to load "%s" model.', $className[1]));
         }
      } else if ($className[0] === 'Plugin') {
         //TODO!
      } else {
         throw new InvalidArgumentException(__('Unknown class domain: expected "Panda", "App", "Model" or "Plugin"; "%s" given.', $className[0]));
      }
   }

   /**
    * Return the application name.
    * @return string
    */
   public function name() {
      return $this->_name;
   }

   /**
    * Return the application current user.
    * @see Panda.user.User
    * @return User
    */
   public function user() {
      return $this->_user;
   }

   /**
    * Set the application name, in an object context.
    * @param string $name
    * @throws InvalidArgumentException
    */
   public function setName($name) {
      if (is_string($name) && !empty($name)) {
         $this->_name = $name;
      } else {
         throw new InvalidArgumentException(__('Invalid application name: not-empty string needed.'));
      }
   }

   /**
    * Run the application and get a rendered page
    * from the matching controller. 
    */
   public function run() {
      $accessFilterResult = $this->accessFilter();
      if ($accessFilterResult instanceof Controller) {
         //Use the custom given controller
         $controller = $accessFilterResult;
      } else if ($accessFilterResult === true) {
         $controller = $this->getController();
      } else {
         /* If the access isn't granted and the proccess is still
          * running, then generate a "403 Forbidden" HTTP error. */
         HTTPResponse::redirect403($this);
      }
      $controller->exec();

      HTTPResponse::setPage($controller->page());
      HTTPResponse::sendRenderedPage();
   }

   /**
    * Use this method to allow the application access
    * to a group of users only. By default, this method
    * is empty and allow the access to everyone.
    * 
    * It can return either a boolean (true if the access is granted,
    * false else) or an instance of Controller if you want to
    * use a custom controller (if the login is required, you can return
    * the user/member controller, and specify the login action for instance).
    * 
    * This method control the access to every controller
    * in the application. Please use a controller specific
    * method if you want to control the access more particulary.
    * 
    * @see Controller::accessFilter
    * @return bool|Controller
    */
   public function accessFilter() {
      return true;
   }

}