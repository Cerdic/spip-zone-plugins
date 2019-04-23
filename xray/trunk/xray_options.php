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

function autoriser_xray_dist($faire, $type='', $id=0, $qui = NULL, $opt = NULL) {
	return autoriser('webmestre')
		or (defined ('ID_AUTEUR_AUTORISER_XRAY') 
			and isset($GLOBALS['visiteur_session']['id_auteur'])
			and ($GLOBALS['visiteur_session']['id_auteur']==intval(ID_AUTEUR_AUTORISER_XRAY)));
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

defined ('_CACHE_KEY') or define('_CACHE_KEY', '');
if (_CACHE_KEY) 
	die ("XRay ne fonctionne pas encore avec des caches cryptés. Ajoutez &nbsp; <code> define('_CACHE_KEY', ''); </code> &nbsp; dans votre mes_options.php");


global $Memoization;
$cfg = @unserialize($GLOBALS['meta']['memoization']);
$err = '';
if (!$Memoization or !$cfg )
	$err = "Pour XRay, activez memoization par apc ou apcu";
elseif (($Memoization->methode != 'apc') and ($Memoization->methode != 'apcu'))
	$err = "Le plugin XRay nécessite d'activer le plugin memoization avec APC ou APCu";
else {
	$methode = $Memoization->methode;
	$fexists = $methode.'_exists';
	if (!function_exists($fexists))
		$err = "Memoization est activée avec $methode, mais il manque la fonction $fexists";
}
	
if ($err) {
	if (isset($_GET['exec']) and ($_GET['exec']=='xray')) {
		echo "<h1>$err</h1>";
		exit;
	}
	spip_log($err, 'xray');
	return;
}

// détecter les vidages de caches yc car saturation de l'espace dispo
$fstore = $methode.'_store';
if (!$fexists($methode.'_key_test_flush')) {
  spip_log ("xray détecte un vidage du cache $methode");
  $fstore($methode.'_key_test_flush', date(DATE_RFC2822).': recréation du cache APC ou APCu (aprés vidage total ?)');
}

include_once ('xray_apc.php');

exit;
