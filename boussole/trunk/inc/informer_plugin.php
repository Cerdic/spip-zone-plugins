<?php
/**
 * Ce fichier contient la fonction surchargeable de récupération des informations d'un plugin.
 *
 * @package SPIP\BOUSSOLE\Outils\Plugins
 */

if (!defined("_ECRIRE_INC_VERSION")) return;


/**
 * Récupération des informations d'un plugin connu par son préfixe.
 *
 * Cette fonction reloade systématiquement le cache des plugins afin d'être sur
 * de lire les informations à jour.
 *
 * @todo
 * 		Elle pourrait être avantageusement remplacé par le filtre SPIP info_plugin() si
 * 		celui-ci pouvait forcer la relecture du XML (ajout d'un argument reload suffirait).
 *
 * @param $prefixe
 * 		Préfixe du plugin en minuscules.
 * @return array
 * 		Tableau de toutes les informations du plugin ou tableau vide en cas d'erreur.
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
