<?php
// fichier d'options SPIP principal du plugin xray
// 		xray/xray_options.php

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/xray_options');
include_spip('inc/xray_options_default');

if (!isset($_GET['exec']) or ($_GET['exec']!='xray')) 
	return;


global $Memoization;
$cfg = @unserialize($GLOBALS['meta']['memoization']);
if ($Memoization and ($Memoization->methode == 'apc')
and $cfg and ($cfg['methode']=='apc')) {
	include_once ('xray_apc.php');
	exit;
}
else {
	echo "Erreur : le plugin XRay nécessite d'activer le plugin memoization avec APC";
	exit;
};

