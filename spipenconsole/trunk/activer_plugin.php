#!/usr/bin/php
<?php
$activer = $argv;
array_shift($activer);
chdir($activer[count($activer)-1]);
array_pop($activer);

if (!defined('_DIR_RESTREINT_ABS')) define('_DIR_RESTREINT_ABS', '');
include_once _DIR_RESTREINT_ABS.'inc_version.php';

$plugins_actifs = unserialize($GLOBALS['meta']['plugin']);
$activer = array_map('strtolower',$activer);

// enlever les plugins deja actifs (donc y compris les extensions)
foreach($activer as $k=>$prefixe){
	$prefixeup = strtoupper($prefixe);
	if (isset($plugins_actifs[$prefixeup])){
		echo "Plugin $prefixe deja actif\n";
		unset($activer[$k]);
	}
}

// chercher dans les plugins dispo
include_spip('inc/plugin');
$plugins = liste_plugin_files();
$get_infos = charger_fonction('get_infos','plugins');
$dirs_add = array();

foreach($plugins as $dir){
	$infos = $get_infos($dir);
	$prefix = strtolower($infos['prefix']);
	if (in_array($prefix,$activer)){
		$dirs_add[] = $dir;
		echo "\033[32m Activer plugin".$prefix." (repertoire $dir)\33[0m \n";
		$activer = array_diff($activer, array($prefix));
		if (!count($activer))
			break;
	}
}
if (count($activer)){
	echo "\nImpossible de trouver les plugins : ".implode(", ",$activer)."\n";
}
if (count($dirs_add)){
	ecrire_plugin_actifs($dirs_add,false,'ajoute');
	$plugins_actifs = unserialize($GLOBALS['meta']['plugin']);
}
