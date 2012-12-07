<?php

/**
 * PandaResponse
 * 
 * @author Stanislas Michalak <stanislas.michalak@gmail.com>
 * @package Panda
 * 
 */
class PandaResponse {

   private static $_page = null;

   private function __construct() {
      
   }
   
   public static function addHeader($header) {
      header($header);
   }

   public static function redirect($location) {
      header('Location: ' . $location);
      exit;
   }

   public static function redirect404($app) {
      PandaApplication::load('Panda.core.Page');
      self::setPage(new Page($app));
      self::page()->setError();
      self::page()->setTemplate(APP_DIR . strtolower($app->name()) . '/template/404.php');
      self::addHeader('HTTP/1.0 404 Not Found');

      self::sendRenderedPage();
   }

   public static function redirect403($app) {
      PandaApplication::load('Panda.core.Page');
      self::setPage(new Page($app));
      self::page()->setError();
      self::page()->setTemplate(APP_DIR . strtolower($app->name()) . '/template/403.php');
      self::addHeader('HTTP/1.0 403 Forbidden');

      self::sendRenderedPage();
   }

   public static function sendRenderedPage() {
      exit(self::page()->build());
   }

   public static function page() {
      return self::$_page;
   }

   public static function setPage(Page $page) {
      self::$_page = $page;
   }

   public static function setSession($key, $value, $append = false) {
      panda_array_set($key, $_SESSION, $value, false, $append);
   }

   public static function setCookie($key, $value = '', $expire = 0, $path = null, $domain = null, $secure = false, $httpOnly = true) {
      if (is_array($value) || is_object($value)) {
         $value = serialize($value);
      }
      setcookie($key, $value, $expire, $path, $domain, $secure, $httpOnly);
   }

   public static function unsetSession($key) {
      panda_array_unset($key, $_SESSION);
   }

   public static function unsetCookie($key) {
      $key = explode('.', $key);
      if (isset($_COOKIE[$key[0]])) {
         if(count(key) > 1) {
            if (($unserializedCookie = @unserialize($_COOKIE[$key[0]])) !== false) {
               $parentKey = $key[0];
               unset($key[0]);
               panda_array_unset($key, $unserializedCookie[$parentKey]);
               $_COOKIE[$key[0]] = $unserializedCookie;
            } else {
               throw new ErrorException(__('Unable to delete this cookie key: the cookie content is not an array'));
            }
         } else {
            setcookie($key, $_COOKIE[$key[0]], 1);
         }
      }
   }

}