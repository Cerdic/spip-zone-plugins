#!/usr/bin/php
<?php
$desactiver = $argv;
array_shift($desactiver);

chdir('../');
if (!is_dir('ecrire/') AND is_dir('../ecrire/')) chdir('../');
chdir('ecrire/');
if (!defined('_DIR_RESTREINT_ABS')) define('_DIR_RESTREINT_ABS', '');
include_once _DIR_RESTREINT_ABS.'inc_version.php';

$plugins = unserialize($GLOBALS['meta']['plugin']);

if (reset($desactiver)=="--all"){
	$desactiver = array_map('strtolower',array_keys($plugins));
}
$dirs_un = array();
foreach($desactiver as $prefixe){
	$prefixeup = strtoupper($prefixe);
	if (!isset($plugins[$prefixeup])){
		echo "Plugin $prefixe introuvable dans les plugins actifs\n";
	}
	else {
		$dir = constant($plugins[$prefixeup]['dir_type']).$plugins[$prefixeup]['dir'];
		echo "\33[31m Desactiver plugin $prefixe (repertoire $dir)\33[0m \n";
		$dirs_un[] = $plugins[$prefixeup]['dir'];
	}
}
if (count($dirs_un)){
	include_spip('inc/plugin');
	ecrire_plugin_actifs($dirs_un,false,'enleve');
	$plugins = unserialize($GLOBALS['meta']['plugin']);
}
