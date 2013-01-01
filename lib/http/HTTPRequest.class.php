<?php

/**
 * HTTPRequest
 * 
 * @author Stanislas Michalak <stanislas.michalak@gmail.com>
 * @package Panda
 * 
 */
class HTTPRequest {

   private function __construct() {
      
   }

   public static function getExists() {
      if (func_num_args() > 0) {
         $keys = func_get_args();
         foreach ($keys as $key) {
            if (!panda_array_key_exists($key, $_GET)) {
               return false;
            }
         }
         return true;
      } else {
         throw new InvalidArgumentException(__('Please use at least one argument with the getExists method.'));
      }
   }

   public static function postExists() {
      if (func_num_args() > 0) {
         $keys = func_get_args();
         foreach ($keys as $key) {
            if (!panda_array_key_exists($key, $_POST)) {
               return false;
            }
         }
         return true;
      } else {
         throw new InvalidArgumentException(__('Please use at least one argument with the postExists method.'));
      }
   }

   public static function fileExists($key) {
      return (isset($_FILES[$key]) && $_FILES[$key]['size'] !== 0);
   }

   public static function sessionExists($key) {
      return panda_array_key_exists($key, $_SESSION);
   }

   public static function cookieExists($key) {
      $key = explode('.', $key);
      if (isset($_COOKIE[$key[0]])) {
         if (count($key) === 1) {
            return true;
         } else {
            if (($unserializedCookie = @unserialize($_COOKIE[$key[0]])) !== false) {
               return panda_array_key_exists(implode('.', $key), $unserializedCookie);
            } else {
               throw new ErrorException(__('Unable to get this cookie key: the cookie content is not an array'));
            }
         }
      } else {
         return false;
      }
   }

   public static function get($key) {
      if(self::getExists($key)) {
         return panda_array_get($key, $_GET);
      } else {
         return null;
      }
   }

   public static function post($key) {
      if(self::postExists($key)) {
         return panda_array_get($key, $_POST);
      } else {
         return null;
      }
   }

   public static function file($key) {
      return (isset($_FILES[$key])) ? $_FILES[$key] : null;
   }

   public static function session($key) {
      if(self::sessionExists($key)) {
         return panda_array_get($key, $_SESSION);
      } else {
         return null;
      }
   }

   public static function cookie($key) {
      if (self::cookieExists($key)) {
         $key = explode('.', $key);
         if (count($key) === 1) {
            return $_COOKIE[$key[0]];
         } else {
            if (($unserializedCookie = @unserialize($_COOKIE[$key[0]])) !== false) {
               return panda_array_get(implode('.', $key), $unserializedCookie);
            } else {
               throw new ErrorException(__('Unable to get this cookie key: the cookie content is not an array'));
            }
         }
      } else {
         return null;
      }
   }

   public static function httpsEnabled() {
      return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] === 443;
   }

   public static function serverName() {
      return isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : null;
   }

   public static function requestURI() {
      return isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : null;
   }

}