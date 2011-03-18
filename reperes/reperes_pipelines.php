<?php
/**
 * Plugin Reperes pour Spip 2.0
 * Licence GPL (c) 2009 - Ateliers CYM
 */


//
// Ajout de la feuille de style et du script javascript
//
function reperes_insert_head_css($flux){
	include_spip('public/spip_bonux_balises');
	$flux .= '<!-- insertion de la css reperes --><link rel="stylesheet" type="text/css" href="'. produire_css_fond('reperes.css') .'" media="all" />';
	return $flux;
}

function reperes_insert_head($flux){
	// pour plus tard : transformer avec produire_js_fond()
	$jsFile = generer_url_public('reperes.js');
	include_spip('inc/session');
	$visiteur = session_get('statut');
	if ($visiteur=='0minirezo'){
		$flux .= "<!-- insertion du js reperes --><script src='$jsFile' type='text/javascript'></script>";
	}
	return $flux;
	
}

function reperes_jqueryui_forcer($scripts){
	$scripts[] = "jquery.ui.draggable";
	return $scripts;
}


?>
