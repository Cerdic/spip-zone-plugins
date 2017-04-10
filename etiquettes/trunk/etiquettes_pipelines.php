<?php
/**
 * Plugin  : Étiquettes
 * Auteur  : RastaPopoulos
 * Licence : GPL
 *
 * Documentation : https://contrib.spip.net/Plugin-Etiquettes
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function etiquettes_insert_head($flux){

	$etiquettes = find_in_path('javascript/etiquettes.js');
	$css = find_in_path('css/etiquettes.css');	
	
	$flux .= <<<EOS
	<link rel="stylesheet" type="text/css" media="all" href="$css" />
	<script type="text/javascript" src="$etiquettes"></script>	
EOS;
	
	return $flux;
}

?>
