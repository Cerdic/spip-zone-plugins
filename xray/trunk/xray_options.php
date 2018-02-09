<?php
if (!defined('_ECRIRE_INC_VERSION')) return;
if (!isset($_GET['exec']) or ($_GET['exec']!='xray')) return;

if (!defined('XRAY_PATTERN_STATS_SPECIALES')) {
	define ('XRAY_PATTERN_STATS_SPECIALES', '/\.(js|css)(\s|_|$)/ui');
	define ('XRAY_LABEL_STATS_SPECIALES', 'Javascript et css');
	define ('XRAY_LABEL_STATS_SPECIALES_EXCLUES', 'Sans les javascript et css');
}

if (!defined('XRAY_OBJET_SPECIAL')) {
	define ('XRAY_OBJET_SPECIAL', 'article');
}

if (!defined('XRAY_ID_OBJET_SPECIAL')) {
	define ('XRAY_ID_OBJET_SPECIAL', 14533);
}

define (JOLI_DATE_FORMAT, 'd/m/Y H:i:s');
date_default_timezone_set ('Europe/Paris');

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

