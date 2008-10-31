<?php
/** 
 * Fichier de retrocompatibilité pour php4
 */

/* 
 * Calcule l'intersection de deux tableaux
 * Emule la fonction pour php <5.1
 * 
 * @author Rod Byrnes
 * @see http://fr.php.net/manual/fr/function.array-intersect-key.php#74956
 */

if (!function_exists('array_intersect_key'))
{
  function array_intersect_key($isec, $keys)
  {
    $argc = func_num_args();
    if ($argc > 2)
    {
      for ($i = 1; !empty($isec) && $i < $argc; $i++)
      {
        $arr = func_get_arg($i);
        foreach (array_keys($isec) as $key)
        {
          if (!isset($arr[$key]))
          {
            unset($isec[$key]);
          }
        }
      }
      return $isec;
    }
    else
    {
      $res = array();
      foreach (array_keys($isec) as $key)
      {
        if (isset($keys[$key]))
        {
          $res[$key] = $isec[$key];
        }
      }
      return $res;
    }
  }
}
?>
