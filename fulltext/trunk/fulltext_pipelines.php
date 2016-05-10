<?php
/**
 * Plugin FullText/Gestion des documents
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function fulltext_taches_generales_cron($taches_generales) {
	$fulltext = @unserialize($GLOBALS['meta']['fulltext']);
	if ($fulltext['intervalle_cron']) {
		$taches_generales['fulltext_index_document'] = $fulltext['intervalle_cron'];
	} else {
		@define('_FULLTEXT_INTERVALLE_CRON', 600);
		// toutes les 10 minutes
		$taches_generales['fulltext_index_document'] = _FULLTEXT_INTERVALLE_CRON;
	}
	return $taches_generales;
}
