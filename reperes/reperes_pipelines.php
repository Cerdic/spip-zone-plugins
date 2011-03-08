<?php
/**
 * Plugin Reperes pour Spip 2.0
 * Licence GPL (c) 2009 - Ateliers CYM
 */


//
// Ajout de la feuille de style et du script javascript
//
function reperes_insert_head($flux){
  $flux .= '<!-- insertion de la css reperes --><link rel="stylesheet" type="text/css" href="'.find_in_path('reperes.css').'" media="all" />';

  $jsFile = generer_url_public('reperes.js');
  $flux .= "<!-- insertion du js reperes --><script src='$jsFile' type='text/javascript'></script>";

  return $flux;
}


?>