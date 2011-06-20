<?php

function links_insert_head_css($flux) {
	//Recuperation de la configuration
	$links = sql_fetsel('valeur', 'spip_meta', 'nom = "links"');
	$links = unserialize($links['valeur']);
	//Styles
	if($links['style'] == 'on'){
		$flux .= '<link rel="stylesheet" href="'.find_in_path('css/links.css').'" type="text/css" media="all" />';
	}
	return $flux;
}

function links_insert_head($flux) {
	//Recuperation de la configuration
	$links = sql_fetsel('valeur', 'spip_meta', 'nom = "links"');
	$links = unserialize($links['valeur']);

	//Ouverture d'une nouvelle fenetre
	if($links['window'] == 'on'){
		//Ouverture dune nouvelel fenetre sur les liens externes
		if($links['external'] == 'on'){
			$flux .= '<script type="text/javascript">var links_site = \''.$GLOBALS['meta']['adresse_site'].'\';</script>';
		}
		//Ouverture d'une nouvelle fenetre sur les documents (extensions à préciser)
		if(($links['download'] == 'on')&&($links['doc_list'])){
			$flux .= '<script type="text/javascript">var links_doc = \''.$links['doc_list'].'\';</script>';
		}
		$flux .= '<script src="'.find_in_path('links.js').'" type="text/javascript"></script>';
	}
	return $flux;
}

?>