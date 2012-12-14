<?php

/**
 * Directories
 */

require_once 'root.php';

define('APP_DIR', ROOT . 'app/');
   define('SANDBOX_DIR', APP_DIR . 'sandbox/');
      define('DOC_DIR', SANDBOX_DIR . 'doc/');
define('LIB_DIR', ROOT . 'lib/');
   define('CONST_DIR', LIB_DIR . 'const/');
   define('CORE_DIR', LIB_DIR . 'core/');
   define('FUNCTION_DIR', LIB_DIR . 'function/');
define('SHARE_DIR', ROOT . 'share/');
   define('CONFIG_DIR', SHARE_DIR . 'config/');
   define('MODEL_DIR', SHARE_DIR . 'model/');
define('WEB_DIR', ROOT . 'www/');
   define('CSS_DIR', WEB_DIR . 'css/');
   define('FEED_DIR', WEB_DIR . 'feed/');
   define('IMG_DIR', WEB_DIR . 'img/');
   define('JS_DIR', WEB_DIR . 'js/');