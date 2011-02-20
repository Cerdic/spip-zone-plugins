<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function manuelsite_header_prive($flux) {
	$flux .= '<link rel="stylesheet" href="'.url_absolue(generer_url_public('manuelsite.css')).'" type="text/css" media="all" />' . "\n";
	return $flux;
}

function manuelsite_insert_head($flux){
	return $flux;
}

function manuelsite_body_prive(&$flux){

	$conf_manuelsite = lire_config('manuelsite');
	if($conf_manuelsite["id_article"]) {
		$flux .= recuperer_fond('prive/manuelsite',array('id_article'=>$conf_manuelsite["id_article"])); 
	}
   return $flux;
}

?>