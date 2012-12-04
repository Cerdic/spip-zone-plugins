<?php
/*
 * Plugin COG-GPS
 * (c) 2009-2010 Guillaume Wauquier
 * Distribue sous licence GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

function coggps_affiche_gauche($flux){

	if ($flux['args']['exec'] == 'cog'){
		$flux['data'] .=recuperer_fond('prive/squelettes/inclure/raccourcis_gps');

	}
	return $flux;
}



?>
