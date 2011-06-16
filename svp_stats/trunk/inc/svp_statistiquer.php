<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

// ----------------------- Traitements des stats ---------------------------------

/**
 * Actualisation des statistiques des plugins presents dans la base.
 * @return boolean
 */

function svp_actualiser_stats() {
	include_spip('inc/distant');

	$page = recuperer_page(_SVP_SOURCE_STATS);
	$infos = json_decode($page);
	if (!$stats = $infos->plugins) {
		// On ne fait que loger l'erreur car celle-ci n'a pas d'incidence sur le comportement
		// de SVP
		spip_log('MODULE STATS - Réponse du serveur incorrecte ou mal formée. Les statistiques ne seront pas mises à jour', 'svp_actions.' . _LOG_ERREUR);
		return false;
	}

	foreach ($stats as $_stat) {
		$prefixe = strtoupper($_stat->nom);
		if ($id_plugin = sql_fetsel('id_plugin', 'spip_plugins', array('prefixe='. sql_quote($prefixe)))) {
			// Si le plugin est bien dans la base on peut lui mettre a jour ses statistiques
			sql_updateq('spip_plugins', 
						array('nbr_sites'=> $_stat->sites, 'popularite'=> floatval(trim($_stat->pourcentage, '%'))),
						'id_plugin=' . sql_quote($id_plugin));
		}
	}
	
	return true;
}

?>
