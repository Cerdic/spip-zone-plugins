<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

// on récupère les fonctions du squelette surchargé (plugin de la dist)
include_spip(_DIR_PLUGIN_STATS.'/prive/squelettes/inclure/stats-visites-data_fonctions');

// une fonction en plus
function stats_total_objet($objet='', $serveur = '') {

	// total pour un type d'objet
	if ($objet) {
		if ($objet == 'article'){
			$table_visites = 'spip_visites_articles';
			$where = '';
		} else {
			$table_visites = 'spip_visites_objets';
			$where = 'objet='.sql_quote($objet);
		}
		$row = sql_fetsel("SUM(visites) AS total_absolu", $table_visites, $where, '', '', '', '', $serveur);
	}
	// ou total global
	else {
		$row = sql_fetsel("SUM(visites) AS total_absolu", "spip_visites", '', '', '', '', '', $serveur);
	}

	return $row ? $row['total_absolu'] : 0;
}
