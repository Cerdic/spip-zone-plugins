<?php
/*
 * Plugin xxx
 * (c) 2009 xxx
 * Distribue sous licence GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_once _DIR_RESTREINT.'inc/config.php';
if (!defined('_DIR_PLUGIN_CFG')){
	if (!function_exists('lire_config')){
		function lire_config($cfg='', $def=null, $unserialize=true) {
			include_spip('configurer/pipelines');
			return spip_bonux_lire_config($cfg, $def, $unserialize);
		}
	}
// charger celui de CFG si ce n'est pas le cas encore !
} elseif (!function_exists('lire_config')) {
	include_spip('inc/cfg_config');
}

?>
