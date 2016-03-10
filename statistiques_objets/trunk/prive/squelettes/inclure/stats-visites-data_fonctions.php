<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

// on récupère les fonctions du squelette surchargé (plugin de la dist)
include_spip(_DIR_PLUGIN_STATS.'/prive/squelettes/inclure/stats-visites-data_fonctions');

// une fonction en plus
function stats_total_objet($objet='', $serveur = '') {
	$row = sql_fetsel("SUM(visites) AS total_absolu", "spip_visites", '', '', '', '', '', $serveur);

	return $row ? $row['total_absolu'] : 0;
}
