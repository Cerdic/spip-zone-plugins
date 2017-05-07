<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * La fonction à exécuter par le cron
 * On vérifie que la date de dernière modification du site soit supérieure
 * à la dernière sauvegarde
 * @param unknown_type $last
 */
function genie_saveauto_dist($last) {

	$tables = array();
	$options = array('auteur' => 'cron', 'manuel' => false);

	// On recherche la configuration des tables
	include_spip('inc/config');
	$tout_exporter = (lire_config('saveauto/tout_saveauto') == 'oui');
	if (!$tout_exporter)
		$tables = lire_config('saveauto/tables_saveauto');

	$saveauto = charger_fonction('saveauto','inc');
	$saveauto($tables, $options);

	return 1;
}

