<?php
require_once 'core/const/dir.php';
require_once 'core/function/debug.php';
require_once 'core/function/lang.php';
require_once 'core/function/lack.php';
require_once 'core/function/array.php';
require_once 'core/function/hash.php';

function autoload($className) {
    if(preg_match('#Application$#', $className) ) {
       if($className === 'Application') {
          require_once CORE_DIR . 'Application.class.php';
       } else {
          require_once APP_DIR . strtolower(rtrim($className, 'Application')) . '/' . $className . '.class.php';
       }
    } else if(preg_match('#Controller$#', $className)) {
       if($className === 'Controller') {
          require_once CORE_DIR . 'Controller.class.php';
       }
    } else if(preg_match('#Model$#', $className)) {
       if($className === 'Model') {
          require_once LIB_DIR . 'model/Model.class.php';
       }
    }
}

spl_autoload_register('autoload');