<?php
 
//
// ajout feuille de style
//
function socialtags_insert_head($flux){
  $flux .= '<link rel="stylesheet" type="text/css" href="'.find_in_path('socialtags.css').'" media="all" />';

  $jsFile = generer_url_public('socialtags.js');
  $flux .= "<script src='$jsFile' type='text/javascript'></script>";

  return $flux;
}


// La liste est stockee en format RSS
function socialtags_liste() {
	include_spip('inc/syndic');
	lire_fichier(find_in_path('socialtags.xml'), $rss);
	return analyser_backend($rss);
}

?>