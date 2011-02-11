<?php
/**
 * Plugin DayFill - Gestionnaire de temps pour Spip 2.0
 * Licence GPL (c) 2010 - Ateliers CYM
 *
 */

function dayfill_insert_head($flux){

	$t = lire_config('dayfill/theme');
	spip_log('la config est '.$t, 'dayfill');	

	// à remplacer par une css active dont les paramètres sont définis avec CFG
	$flux .= '<!-- insertion de la css '.$t.'--><link rel="stylesheet" type="text/css" href="'.find_in_path('themes/'.$t.'/habillage.css').'" media="all" />';

	$jsFile = generer_url_public('scripts/dayfill.js');
	$flux .= "<!-- insertion du js dayfill --><script src='$jsFile' type='text/javascript'></script>";

	return $flux;
}

?>