<?php
// fichier d'options SPIP principal du plugin xray
// 		xray/xray_options.php
//
if (!defined('_ECRIRE_INC_VERSION')) return;

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/xray_options');
include_spip('inc/xray_options_default');

if (!isset($_GET['exec']) or ($_GET['exec']!='xray')) 
	return;

// détecter (?) les vidages de caches yc car saturation de l'espace dispo
if (!apc_exists('apc_key_test_flush')) {
  spip_log ('xray says : le cache APC a été vidé', 'APC_cache_flush');
  apc_store('apc_key_test_flush', 'apc_test_value_flush');
}

function xray_marqueur_invisible($t) {
	recuperer_fond('inclure/xray_marqueur_visible', array('what'=>'session','texte'=>$t));
	return '';
}

global $Memoization;
$cfg = @unserialize($GLOBALS['meta']['memoization']);
if ($Memoization and 
	(($Memoization->methode == 'apc') or ($Memoization->methode == 'apcu'))
	and $cfg and (($cfg['methode']=='apc') or ($cfg['methode']=='apcu'))) {
	include_once ('xray_apc.php');
	exit;
}
else {
	echo "Erreur : le plugin XRay nécessite d'activer le plugin memoization avec APC ou APCu";
	exit;
};

