<?php

function balise_GOOGLEPLUS1 ($p) {

$googleplus1_taille = lire_config('googleplus1/googleplus1_taille');
//if ($googleplus1_taille != "standard") {
//$balise = "<g:plusone size=".$googleplus1_taille."></g:plusone>";
  $p->code = "'<g:plusone size=\"$googleplus1_taille\"></g:plusone>'";
//}
  return $p;
}

?> 
