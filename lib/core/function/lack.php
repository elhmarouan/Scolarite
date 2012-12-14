<?php

if (!function_exists('array_replace_recursive')) {

   /**
    * Replaces elements from passed arrays into the first
    * array recursively. (replacement for PHP 5 < 5.3.0)
    * 
    * @param array $base
    * @param array $replacements
    * @return array
    */
   function array_replace_recursive($base, $replacements) {
      foreach (array_slice(func_get_args(), 1) as $replacements) {
         $brefStack = array(&$base);
         $headStack = array($replacements);

         do {
            end($brefStack);

            $bref = &$brefStack[key($brefStack)];
            $head = array_pop($headStack);

            unset($brefStack[key($brefStack)]);

            foreach (array_keys($head) as $key) {
               if (isset($key, $bref) && is_array($bref[$key]) && is_array($head[$key])) {
                  $brefStack[] = &$bref[$key];
                  $headStack[] = $head[$key];
               } else {
                  $bref[$key] = $head[$key];
               }
            }
         } while (count($headStack));
      }

      return $base;
   }

}