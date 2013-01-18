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
   echo $exception->getLine() . ': ' . $exception->getMessage();
}

set_exception_handler('exception_handler');