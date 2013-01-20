<?php

/**
 * Config
 * 
 * @author Stanislas Michalak <stanislas.michalak@gmail.com>
 * @package Panda.core
 * 
 */
class Config {

   private static $_appName;
   private static $_format = 'json';
   private static $_configKeys = array();
   private static $_defaultConfigKeys = array();
   private static $_appConfigKeys = array();

   const DEFAULT_CONFIG_DIR = 0;
   const APP_CONFIG_DIR = 1;
   const UNKNOWN_KEY = 0;
   const INVALID_FILE = 1;

   private function __construct() {
      
   }

   public static function read($key) {
      try {
         $keyValue = panda_array_get($key, self::_configKeys());
      } catch (ErrorException $e) {
         throw new ErrorException(__('The "%s" configuration key doesn\'t exists.', $key), self::UNKNOWN_KEY);
      } catch (InvalidArgumentException $e) {
         throw new InvalidArgumentException(__('Invalid configuration key: not-null string needed.'));
      }
      return $keyValue;
   }

   public static function write($key, $value, $configDir = self::DEFAULT_CONFIG_DIR) {
      if ($configDir === self::DEFAULT_CONFIG_DIR) {
         try {
            if (self::format() === 'json') {
               self::_exportToJson(panda_array_set($key, self::_defaultConfigKeys(), $value, true), CONFIG_DIR . 'config.json');
            } else if (self::format() === 'xml') {
               self::_exportToXml(panda_array_set($key, self::_defaultConfigKeys(), $value, true), CONFIG_DIR . 'config.xml');
            } else {
               self::_exportToIni(panda_array_set($key, self::_defaultConfigKeys(), $value, true), CONFIG_DIR . 'config.ini');
            }
         } catch (InvalidArgumentException $e) {
            throw new InvalidArgumentException(__('Invalid configuration key: not-empty string needed.'));
         }
      } else if ($configDir === self::APP_CONFIG_DIR) {
         try {
            if (self::format() === 'json') {
               self::_exportToJson(panda_array_set(self::_appConfigKeys(), $value, true), APP_DIR . self::_appName() . '/config/config.json');
            } else if (self::format() === 'xml') {
               self::_exportToXml(panda_array_set(self::_appConfigKeys(), $value, true), APP_DIR . self::_appName() . '/config/config.xml');
            } else {
               self::_exportToIni(panda_array_set(self::_appConfigKeys(), $value, true), APP_DIR . self::_appName() . '/config/config.ini');
            }
         } catch (InvalidArgumentException $e) {
            throw new InvalidArgumentException(__('Invalid configuration key: not-empty string needed.'));
         }
      } else {
         throw new InvalidArgumentException(__('Invalid configuration dir: self::DEFAULT_CONFIG_DIR or self::APP_CONFIG_DIR needed.'));
      }
   }

   public static function delete($key, $configDir = self::DEFAULT_CONFIG_DIR) {
      
   }

   public static function appName() {
      return self::$_appName;
   }

   private static function _format() {
      return self::$_format;
   }

   private static function _defaultConfigKeys() {
      if (empty(self::$_defaultConfigKeys)) {
         if (self::_format() === 'json') {
            self::$_defaultConfigKeys = self::_extractFromJson(CONFIG_DIR . 'config.json');
         } else if (self::_format() === 'xml') {
            self::$_defaultConfigKeys = self::_extractFromXml(CONFIG_DIR . 'config.xml');
         } else {
            self::$_defaultConfigKeys = self::_extractFromIni(CONFIG_DIR . 'config.ini');
         }
      }
      return self::$_defaultConfigKeys;
   }

   private static function _appConfigKeys() {
      if (empty(self::$_appConfigKeys)) {
         if (self::_format() === 'json') {
            self::$_appConfigKeys = self::_extractFromJson(APP_DIR . self::appName() . '/config/config.json');
         } else if (self::_format() === 'xml') {
            self::$_appConfigKeys = self::_extractFromXml(APP_DIR . self::appName() . '/config/config.xml');
         } else {
            self::$_appConfigKeys = self::_extractFromIni(APP_DIR . self::appName() . '/config/config.ini');
         }
      }
      return self::$_appConfigKeys;
   }

   private static function _configKeys() {
      if (empty(self::$_appName)) {
         throw new ErrorException(__('Please configure the application name first.'));
      }
      self::$_configKeys = array_replace_recursive(self::_defaultConfigKeys(), self::_appConfigKeys());
      return self::$_configKeys;
   }

   public static function setAppName($appName) {
      if (is_string($appName) && !empty($appName) && is_file(APP_DIR . strtolower($appName) . '/' . $appName . 'Application.class.php')) {
         self::$_appName = $appName;
      } else {
         throw new InvalidArgumentException(__('"%s" is an invalid application name.', $appName));
      }
   }

   public static function setFormat($format) {
      if (is_string($format) && in_array($format, array('json', 'xml', 'ini'))) {
         self::$_format = $format;
      } else {
         throw new InvalidArgumentException(__('Invalid config format: the supported formats are "json", "xml", and "ini"'));
      }
   }

   private static function _extractFromJson($file) {
      if (!empty($file) && is_file($file)) {
         $vars = json_decode(file_get_contents($file), true);
         if ($vars !== null) {
            return $vars;
         } else {
            throw new ErrorException(__('Unable to parse the configuration file.'), self::INVALID_FILE);
         }
      } else {
         return array();
      }
   }

   private static function _extractFromXml($file) {
      if (!empty($file) && is_file($file)) {
         //TODO!
      } else {
         throw new InvalidArgumentException(__('Unknown configuration file.'));
      }
   }

   private static function _extractFromIni($file) {
      if (!empty($file) && is_file($file)) {
         //TODO!
      } else {
         throw new InvalidArgumentException(__('Unknown configuration file.'));
      }
   }

   private static function _exportToJson($vars, $file) {
      if (!empty($file) && is_file($file)) {
         $json = self::_jsonReadableEncode($vars);
         if ($json !== false) {
            file_put_contents($file, $json);
            throw new ErrorException(__('An error occured while trying to export the config to json format.'));
         }
      } else {
         throw new InvalidArgumentException(__('Unknown configuration file.'));
      }
   }

   private static function _jsonReadableEncode($var) {
      if (phpversion() >= 5.4) {
         return json_encode($var, JSON_PRETTY_PRINT);
      } else {
         //JSON_PRETTY_PRINT emulation
         $result = '';
         $level = 0;
         $prevChar = '';
         $inQuotes = false;
         $endsLineLevel = null;
         $json = json_encode($var);
         $jsonLength = strlen($json);

         for ($i = 0; $i < $jsonLength; ++$i) {
            $char = $json[$i];
            $newLineLevel = null;
            $post = '';
            if ($endsLineLevel !== null) {
               $newLineLevel = $endsLineLevel;
               $endsLineLevel = null;
            }
            if ($char === '"' && $prevChar != '\\') {
               $inQuotes = !$inQuotes;
            } else if (!$inQuotes) {
               switch ($char) {
                  case '}': case ']':
                     --$level;
                     $endsLineLevel = null;
                     $newLineLevel = $level;
                     break;

                  case '{': case '[':
                     ++$level;
                  case ',':
                     $endsLineLevel = $level;
                     break;

                  case ':':
                     $post = " ";
                     break;

                  case " ": case "\t": case "\n": case "\r":
                     $char = "";
                     $endsLineLevel = $newLineLevel;
                     $newLineLevel = null;
                     break;
               }
            }
            if ($newLineLevel !== null) {
               $result .= "\n" . str_repeat("\t", $newLineLevel);
            }
            $result .= $char . $post;
            $prevChar = $char;
         }

         return $result;
      }
   }

   private static function _exportToXml($vars, $file) {
      //TODO!
   }

   private static function _exportToIni($vars, $file) {
      //TODO!
   }

}