<?php

/**
 * Router
 * 
 * @author Stanislas Michalak <stanislas.michalak@gmail.com>
 * @package Panda.Router
 * 
 */
class Router {

   protected $_routes = array();

   const NO_ROUTE_FOUND = 1;

   public function __construct() {
      Application::load('Panda.router.Route');
      try {
         $domains = Config::read('roots.list');
      } catch (ErrorException $e) {
         if($e->getCode() === Config::UNKNOWN_KEY) {
            $domains = null;
         } else {
            throw $e;
         }
      }
      if ($domains !== null) {
         foreach ($domains as $domain => $params) {
            if ($domain === HTTPRequest::serverName() && $params['redirect'] !== false) {
               HTTPResponse::redirect('http://' . $params['redirect'] . HTTPRequest::requestURI());
            }
         }
      }
   }

   private function _addRoute(Route $route) {
      if (!in_array($route, $this->_routes)) {
         $this->_routes[] = $route;
      }
   }

   public function getRoute($url) {
      $url = urldecode($url);
      foreach ($this->_routes as $route) {
         if (($varsValues = $route->match($url)) !== false) {
            if ($route->containVars()) {
               $varsNames = $route->varsNames();
               $varsList = array();

               foreach ($varsValues as $key => $match) {
                  if ($key !== 0) {
                     $varsList[$varsNames[$key - 1]] = $match;
                  }
               }

               $route->setVars($varsList);
            }

            return $route;
         }
      }

      throw new RuntimeException(__('No route found for the asked URL'), self::NO_ROUTE_FOUND);
   }

   public function loadKnownRoutes($appName) {
      if (is_file(APP_DIR . strtolower($appName) . '/config/routes.xml')) {
         $appXml = new DOMDocument;
         $appXml->load(APP_DIR . strtolower($appName) . '/config/routes.xml');
         $routes = $appXml->getElementsByTagName('route');
      }
      if (count($routes) > 0) {
         foreach ($routes as $route) {
            $vars = array();
            if ($route->hasAttribute('vars')) {
               $vars = explode(',', $route->getAttribute('vars'));
            }
            $this->_addRoute(new Route($route->getAttribute('url'), $route->getAttribute('module'), $route->getAttribute('action'), $vars));
         }
      }
   }

}