<?php

#

if (!defined("_ECRIRE_INC_VERSION")) return;

# creer le lanceur dans tmp/pre_spip.inc
function creer_fastcache() {

	$cfg = @unserialize($GLOBALS['meta']['fastcache']);
	$debug = var_export($cfg['debug'] === 'on', true);
	$pnghack = var_export($cfg['pnghack'] === 'on', true);

	if (!$periode = intval($cfg['periode']))
		$periode = 180;
	$periode = var_export($periode, true);

	$stats = var_export($GLOBALS['meta']['activer_statistiques'] === 'oui', true);

	if (!$cache = $cfg['cache'])
		$cache = _DIR_CACHE;
	$cache = var_export($cache, true);

	$meta = var_export(_FILE_META, true);
	$prefix = var_export($GLOBALS['cookie_prefix'], true);

	$contenu = '<'.'?php' .
<<<CONFIG

// Voir la configuration du plugin dans CFG
@define ('_FC_DEBUG', $debug);
@define ('_FC_PERIODE', $periode);
@define ('_FC_STATS_SPIP', $stats);
@define ('_FC_DIR_CACHE', $cache);
@define ('_FC_META', $meta);
@define ('_FC_IE_PNGHACK', $pnghack);
@define ('_FC_COOKIE_PREFIX', $prefix);

CONFIG

	. "include '" . _DIR_PLUGIN_FASTCACHE . "fastcache.php';\n\n?"
	. ">\n";

	ecrire_fichier(_FC_LANCEUR, $contenu);
}

?>
