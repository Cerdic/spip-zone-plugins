<?php

#

if (!defined("_ECRIRE_INC_VERSION")) return;

# creer le lanceur dans tmp/fcconfig_domaine.inc
function creer_fastcache() {

	$cfg = @unserialize($GLOBALS['meta']['fastcache']);
	$debug = var_export($cfg['debug'] === 'on', true);
	$pnghack = var_export($cfg['pnghack'] === 'on', true);
	$toutes = var_export($cfg['toutes'] === 'on', true);

	if (!$periode = intval($cfg['periode']))
		$periode = 180;
	$periode = var_export($periode, true);

	$stats = var_export($GLOBALS['meta']['activer_statistiques'] === 'oui', true);
	$dir_plugin_fastcache = var_export(_DIR_PLUGIN_FASTCACHE, true);
	$dir_tmp = var_export(_DIR_TMP, true);

	$meta = var_export(_FILE_META, true);
	$prefix = var_export($GLOBALS['cookie_prefix'], true);

	$memoization = var_export(find_in_path('inc/memoization.php'), true);

	$contenu = '<'.'?php' .
<<<CONFIG

// Voir la configuration du plugin dans CFG
@define ('_FC_DEBUG', $debug);
@define ('_FC_PERIODE', $periode);
@define ('_FC_STATS_SPIP', $stats);
@define ('_DIR_PLUGIN_FASTCACHE', $dir_plugin_fastcache);
@define ('_DIR_TMP', $dir_tmp);
@define ('_FC_META', $meta);
@define ('_FC_IE_PNGHACK', $pnghack);
@define ('_FC_TOUTES', $toutes);
@define ('_FC_COOKIE_PREFIX', $prefix);
@define ('_FC_MEMOIZATION', $memoization);

CONFIG

	. "include '" . _DIR_PLUGIN_FASTCACHE . "fastcache.php';\n\n?"
	. ">\n";

	ecrire_fichier(_FC_LANCEUR, $contenu);
}

?>
