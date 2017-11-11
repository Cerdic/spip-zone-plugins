<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

global $Memoization;
$cfg = @unserialize($GLOBALS['meta']['memoization']);

if (isset($_GET['exec']) and ($_GET['exec']=='xray')) {
	if ($Memoization and ($Memoization->methode == 'apc')
	and $cfg and ($cfg['methode']=='apc')) {
		include_once ('xray_apc.php');
		exit;
	}
	else {
		echo "Erreur : le plugin XRay nécessite d'activer le plugin memoization avec APC";
		exit;
	};
}
