<?php

/**
 * Interrogation de l'API SVP pour les plugins de SPIP
 *
 * @plugin     Info Sites
 * @copyright  2014-2016
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP\Info_Sites\PluginsSpip
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function inc_plugins_spip_dist($plugin = array(), $branche_spip = null) {
	if (is_array($plugin) and count($plugin) > 0 and !is_null($branche_spip)) {
		include_spip('inc/utils');
		$prefixe = strtolower($plugin['prefixe']);
		$content_json = recuperer_page("http://plugins.spip.net/http.api/svp/plugins/" . $prefixe);
		$json_to_array = charger_fonction('json_to_array', 'inc');
		$svp_plugin = $json_to_array($content_json);
		if (isset($svp_plugin['erreur']) and $svp_plugin['erreur']['status'] !== 404) {
			$t = explode('.', $branche_spip);
			$branche_spip = $t[0] . '.' . $t[1];

		}
		echo "<pre>";
		var_dump($svp_plugin);
		echo "</pre>";
		# TODO : Créer la fonction qui va chercher les infos d'un plugin SPIP.
		return $svp_plugin;
	}
	return false;
}
