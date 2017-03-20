<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function w3css_insert_head_css($flux){

	# Definir les chemins, stocker le css généré dans /local/w3.css
	$dir =  $GLOBALS['path'] . _NOM_TEMPORAIRES_ACCESSIBLES;
	$fsdir = $_SERVER['DOCUMENT_ROOT'] .'/'. $dir;
	$filename = "w3.css";
	
	# On utilise le compilateur scss uniquement en var_mode recalcul ou css ( et pas calcul )
	if ( isset($GLOBALS['visiteur_session']['webmestre'])
		 && ( _request('var_mode') === "recalcul" || _request('var_mode') === "css") ) {
		$data = scss_css(recuperer_fond('css/w3.css'));
		ecrire_fichier($fsdir . $filename, $data);
		$flux .= "<link rel='stylesheet' href='" . $dir . $filename . "' type='text/css' media='all' />\n";
	}
	else {
		$flux .= "<link rel='stylesheet' href='" . $dir . $filename . "' type='text/css' media='all' />\n";
	}

    return $flux;
}
