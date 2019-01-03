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

// détecter les vidages de caches yc car saturation de l'espace dispo
if (!apc_exists('apc_key_test_flush')) {
  spip_log ('xray détecte un vidage du cache APC');
  apc_store('apc_key_test_flush', date(DATE_RFC2822).': recréation du cache APC (aprés vidage total ?)');
}

//
// Le filtre xray_marqueur_invisible met ce qu'il reçoit dans un cache APC 'xray_marqueur_visible'
// et renvoie une chaine vide pour le html, si bien que ce qui est caché... reste invisible
//
function xray_marqueur_invisible($t) {
	// souriez :
	recuperer_fond('inclure/xray_marqueur_visible', array('what'=>'session','texte'=>$t));	
	// circulez :
	return '';
}

!defined ('_CACHE_KEY') or define('_CACHE_KEY', '');
if (_CACHE_KEY) 
	die ("XRay ne fonctionne pas avec des caches cryptés. Ajoutez &nbsp; <code> define('_CACHE_KEY', ''); </code> &nbsp; dans votre mes_options.php");


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

