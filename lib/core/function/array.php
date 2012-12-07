<?php

define(INVALID_ARRAY_KEY, 1);
define(UNKNOWN_ARRAY_KEY, 2);

function panda_array_key_exists($key, array $array) {
   if (!is_string($key) || empty($key)) {
      throw new InvalidArgumentException(__('Unable to get the value: invalid index.'), INVALID_ARRAY_KEY);
   }
   $key = explode('.', $key);
   if (isset($array[$key[0]])) {
      $currentKey = $array[$key[0]];
      for ($i = 1; $i < count($key); ++$i) {
         if (isset($currentKey[$key[$i]])) {
            $currentKey = $currentKey[$key[$i]];
         } else {
            return false;
         }
      }
      return true;
   } else {
      return false;
   }
}

function panda_array_get($key, array $array) {
   if (!is_string($key) || empty($key)) {
      throw new InvalidArgumentException(__('Unable to get the value: invalid index.'), INVALID_ARRAY_KEY);
   }
   $key = explode('.', $key);
   $output = $array[$key[0]];
   for ($i = 1; $i < count($key); ++$i) {
      if (isset($output[$key[$i]])) {
         $output = $output[$key[$i]];
      } else {
         throw new ErrorException(__('The "%s" index doesn\'t exists.', implode('.', $key)), UNKNOWN_ARRAY_KEY);
      }
   }
   return $output;
}

function panda_array_set($key, array &$array, $value, $return = false, $append = false) {
   if (!is_string($key) || empty($key)) {
      throw new InvalidArgumentException(__('Unable to set the value: invalid index.', INVALID_ARRAY_KEY));
   }
   $key = explode('.', $key);
   if (count($key) > 1) {
      $key = array_reverse($key);
   }
   if ($append && panda_array_key_exists(implode($key), $array)) {
      $oldKeyValue = panda_array_get(implode('.', $key), $array);
      $newKey = array($key[0] => array($oldKeyValue, $value));
   } else {
      $newKey = array($key[0] => $value);
   }
   for ($i = 1; $i < count($key); ++$i) {
      $newKey = array($key[$i] => $newKey);
   }

   $array = array_replace_recursive($array, $newKey);
   if ($return) {
      return $array;
   }
}

function panda_array_unset($key, array &$array) {
   if (!is_string($key) || empty($key)) {
      throw new InvalidArgumentException(__('Unable to delete the value: invalid index.', INVALID_ARRAY_KEY));
   }

   $key = explode('.', $key);
   if (isset($array[$key[0]])) {
      if (count($key) === 1) {
         unset($array[$key[0]]);
      } else {
         $parentKey = $key[0];
         unset($key[0]);
         panda_array_unset(implode('.', $key), $array[$parentKey]);
      }
   } else {
      throw new InvalidArgumentException(__('Unable to delete the value: unknown index.', UNKNOWN_ARRAY_KEY));
   }
}