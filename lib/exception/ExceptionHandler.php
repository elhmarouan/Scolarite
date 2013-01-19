<?php

/**
 * Exception handler
 * 
 * Catch and process not-catched exceptions
 * 
 * @author Stanislas Michalak <stanislas.michalak@gmail.com>
 * @package Panda
 * 
 */

function exception_handler(Exception $exception) {
   //TODO! Display a HTTP 500 error and log the exception, if the application is on production state
   echo get_class($exception) . ': ' . $exception->getMessage() . '<br />';
   echo 'Where: ' . $exception->getFile() . ':' . $exception->getLine();
}

set_exception_handler('exception_handler');