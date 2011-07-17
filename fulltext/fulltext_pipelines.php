<?php
/**
 * Plugin FullText/Gestion des documents
 */

function fulltext_taches_generales_cron($taches_generales) {
	$fulltext = sql_fetsel('valeur', 'spip_meta', 'nom = "fulltext"');
	$fulltext = unserialize($fulltext['valeur']);
	$taches_generales['fulltext_index_document'] = $fulltext['intervalle_cron'] ? $fulltext['intervalle_cron'] : @define('_FULLTEXT_INTERVALLE_CRON',600); // toutes les 10 minutes
	return $taches_generales;
}

?>