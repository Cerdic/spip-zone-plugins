<?php

function ticketspublics_insert_head_css($flux){
	$flux .= '<link type="text/css" rel="stylesheet" href="' . _DIR_PLUGIN_TICKETSPUBLICS  .'css/ticketspublics.css" />';
	return $flux;
}

function ticketspublics_insert_head($flux){
	$flux .= '<script type="text/javascript" src="' . _DIR_PLUGIN_TICKETSPUBLICS  .'javascript/ticketspublics.js"></script>';
	return $flux;
}

// le bouton pour afficher/masquer la feuille de route
function ticketspublics_formulaire_admin($flux) {

			$btn = recuperer_fond('prive/bouton/ticketspublics');
			$flux['data'] = preg_replace('%(<!--extra-->)%is', $btn.'$1', $flux['data']);
	
	return $flux;
}

?>