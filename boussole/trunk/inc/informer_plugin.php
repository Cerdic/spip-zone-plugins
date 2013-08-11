<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


/**
 * Récupération des informations d'un plugin connu par son préfixe.
 * Cette fonction reloade systématiquement le cache des plugins afin d'être sur
 * de lire les informations à jour
 *
 * @package	BOUSSOLE\Outils
 *
 * @param $prefixe
 *
 * @return array
 */
function inc_informer_plugin_dist($prefixe) {

	include_spip('inc/plugin');
	$prefixe = strtoupper($prefixe);
	$plugins_actifs = liste_plugin_actifs();

	if (!is_dir($plugins_actifs[$prefixe]['dir_type']))
		$dir_plugins = constant($plugins_actifs[$prefixe]['dir_type']);
	else
		$dir_plugins = $plugins_actifs[$prefixe]['dir_type'];

	$informer = charger_fonction('get_infos','plugins');
	$infos = $informer($plugins_actifs[$prefixe]['dir'], true, $dir_plugins);

	return (is_array($infos) ? $infos : array());
}

?>
