<?php

/**
 * Interrogation de l'API SVP pour les plugins de SPIP
 *
 * @plugin     Info Sites
 * @copyright  2014-2019
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP\Info_Sites\PluginsSpip
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function inc_plugins_spip_dist($plugin = array(), $branche_spip = null) {
	$argc = func_num_args();
	$params = func_get_args();
	if (is_array($plugin) and count($plugin) > 0 and !is_null($branche_spip)) {
		include_spip('inc/utils');
		include_spip('inc/distant');
		if (is_null($branche_spip)) {
			trigger_error("inc_plugins_spip_dist() expects at least 2 parameters, {$argc} given", E_USER_WARNING);
			return false;
		}
		$prefixe = strtolower($plugin['prefixe']);
		$content_json = recuperer_page("https://plugins.spip.net/http.api/svp/plugins/" . $prefixe);
		// Utilisation de la fonction PHP pour pouvoir avoir un tableau et non un objet
		$svp_plugin = json_decode($content_json, true);
		if (isset($svp_plugin['erreur']) and $svp_plugin['erreur']['status'] !== 404) {
			// On recrée la branche de SPIP du plugin
			$t = explode('.', $branche_spip);
			$branche_spip = $t[0] . '.' . $t[1];
			if ($plugin['version'] == $svp_plugin['donnees']['plugin']['vmax']) {
				// On est sur la dernière version du plugin
				return false;
			} elseif (isset($svp_plugin['donnees']['paquets'])) {
				// On va parcourir les données des paquets pour trouver la version qui nous intéresse
				foreach ($svp_plugin['donnees']['paquets'] as $paquet_nom => $paquet_info) {
					if (in_array($branche_spip, $paquet_info['branches_spip'])) {
						// On est dans la bonne branche
						include_spip('plugins/installer');
						$version_compare = spip_version_compare($plugin['version'], $paquet_info['version']);
						switch ($version_compare) {
							case -1:
								// On est sur une version inférieure
								$plugin['maj'] = $paquet_info['version'];
								return $plugin;
							default:
								return false;
						}
					}
				}
			} else {
				return false;
			}
		}
		return false;
	}
	return false;
}
