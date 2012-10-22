<?php

function feuillederoute_insert_head_css($flux){
	$flux .= '<link type="text/css" rel="stylesheet" href="' . _DIR_PLUGIN_FEUILLEDEROUTE  .'css/feuillederoute.css" />';
	return $flux;
}

function feuillederoute_insert_head($flux){
	$flux .= '<script type="text/javascript" src="' . _DIR_PLUGIN_FEUILLEDEROUTE  .'javascript/feuillederoute.js"></script>';
	return $flux;
}

// le bouton pour afficher/masquer la feuille de route
function feuillederoute_formulaire_admin($flux) {

			$btn = recuperer_fond('prive/bouton/feuillederoute');
			$flux['data'] = preg_replace('%(<!--extra-->)%is', $btn, $flux['data']);
	
	return $flux;
}

?>