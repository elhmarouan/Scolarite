<?php

function __($label) {
   $nbArgs = func_num_args();
   if ($nbArgs == 1) {
      return _($label);
   } else {
      $args = array_slice(func_get_args(), 1);
      return vsprintf($label, $args);
   }
}

function getBrowserLang() {
   if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))
      return parseBrowserLang($_SERVER['HTTP_ACCEPT_LANGUAGE']);
   else
      return NULL;
}

function parseBrowserLang($httpAccept) {
   if (isset($httpAccept) && strlen($httpAccept) > 1) {
      //Split possible languages into array
      $x = explode(',', $httpAccept);
      foreach ($x as $val) {
         //Check for q-value and create associative array. No q-value means 1 by rule
         if (preg_match('#(.*);q=([0-1]{0,1}\.\d{0,4})#i', $val, $matches))
            $lang[$matches[1]] = (float) $matches[2];
         else
            $lang[$val] = 1.0;
      }

      //Return default language (based on the highest q-value)
      $qval = 0.0;
      foreach ($lang as $key => $value) {
         if ($value > $qval) {
            $qval = (float) $value;
            $deflang = $key;
         }
      }
   }
   return strtolower($deflang);
}

/**
 * Gets the plural of an english noun
 * 
 * @author Akelos Media http://www.akelos.com/
 * 
 * @param string $word
 * @return string|boolean
 */
function pluralize($word) {
   $plural = array(
       '/(quiz)$/i' => '1zes',
       '/^(ox)$/i' => '1en',
       '/([m|l])ouse$/i' => '1ice',
       '/(matr|vert|ind)ix|ex$/i' => '1ices',
       '/(x|ch|ss|sh)$/i' => '1es',
       '/([^aeiouy]|qu)ies$/i' => '1y',
       '/([^aeiouy]|qu)y$/i' => '1ies',
       '/(hive)$/i' => '1s',
       '/(?:([^f])fe|([lr])f)$/i' => '12ves',
       '/sis$/i' => 'ses',
       '/([ti])um$/i' => '1a',
       '/(buffal|tomat)o$/i' => '1oes',
       '/(bu)s$/i' => '1ses',
       '/(alias|status)/i' => '1es',
       '/(octop|vir)us$/i' => '1i',
       '/(ax|test)is$/i' => '1es',
       '/s$/i' => 's',
       '/$/' => 's');

   $uncountable = array('equipment', 'information', 'rice', 'money', 'species', 'series', 'fish', 'sheep');

   $irregular = array(
       'person' => 'people',
       'man' => 'men',
       'child' => 'children',
       'sex' => 'sexes',
       'move' => 'moves');

   $lowercasedWord = strtolower($word);

   foreach ($uncountable as $_uncountable) {
      if (substr($lowercasedWord, (-1 * strlen($_uncountable))) == $_uncountable) {
         return $word;
      }
   }

   foreach ($irregular as $_plural => $_singular) {
      if (preg_match('/(' . $_plural . ')$/i', $word, $arr)) {
         return preg_replace('/(' . $_plural . ')$/i', substr($arr[0], 0, 1) . substr($_singular, 1), $word);
      }
   }

   foreach ($plural as $rule => $replacement) {
      if (preg_match($rule, $word)) {
         return preg_replace($rule, $replacement, $word);
      }
   }
   return false;
}