<?php
/**
 * Plugin PreProd pour Spip 2.0
 * Licence GPL (c) 2011 - Ateliers CYM
 */


function preprod_affichage_final($page){
	
	$pos_head = strpos($page, '</head>');
	// si pas de </head>
	if ($pos_head === false) {
		return $page;
	}

	// si admin
	include_spip('inc/autoriser');
	if (autoriser('configurer')) {
		$jsFile = generer_url_public('preprod.js');
		include_spip('public/spip_bonux_balises');

		$head_reperes  = "<!-- insertion du js preprod --><script src='$jsFile' type='text/javascript'></script>";
		$head_reperes .= '<!-- insertion de la css preprod --><link rel="stylesheet" type="text/css" href="' . produire_css_fond('preprod.css') . '" media="all" />';
		$page = substr_replace($page, $head_reperes, $pos_head, 0);

	}

	return $page;
		
}

?>