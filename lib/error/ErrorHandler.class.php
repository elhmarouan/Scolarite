<?php

/**
 * Error and exception handler
 * 
 * Catch and process errors and exceptions
 * 
 * @author Stanislas Michalak <stanislas.michalak@gmail.com>
 * @package Panda
 * 
 */

class ErrorHandler {

   public static function handleError($number, $message, $file, $line) {
      $error = array('type' => $number, 'message' => $message, 'file' => $file, 'line' => $line);
      echo '<pre>';
      print_r($error);
      echo '</pre>';
   }

   public static function handleException($exception) {
      echo '<pre>';
      print_r($exception);
      echo '</pre>';
   }

   public static function handleShutdown() {
      $error = error_get_last();
      if ($error) {
         echo '<pre>';
         print_r($error);
         echo '</pre>';
      } else {
         return true;
      }
   }

}

//set_error_handler(array('ErrorHandler', 'handleError'));
set_exception_handler(array('ErrorHandler', 'handleException'));
//register_shutdown_function(array('ErrorHandler', 'handleShutdown'));