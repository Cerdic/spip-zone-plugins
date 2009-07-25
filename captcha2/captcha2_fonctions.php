<?php

// calcule le ratio de caractere ASCII d'une phrase
// si < 0.4, texte asiatique, russe ...
function captcha2_ratio_ascii($str) {
  if (strlen($str)==0) return 0;
  $str = strip_tags($str); // on supprime les tags (ex. code) qui faussent le ratio
  // On supprime les caracteres accentues de type &#1234;
  $pattern = '/&#(\d+);/i';
  $replacement = '';
  $string = preg_replace($pattern, $replacement, $str);
  $c = 0;
  foreach (count_chars($string, 1) as $i => $val) {
       if ($i<127)  $c += $val; 
  }
  $l = strlen($str);  
  return $c/$l;
}


?>
