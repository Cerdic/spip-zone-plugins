<?php

$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_TINYMCE',(_DIR_PLUGINS.end($p)));

// determination du chemin de base par rapport a  la racine du serveur
/*$dir_relatif_array = split('/', $_SERVER['PHP_SELF']);
$i = 0;
while($dir_relatif_array[$i] != 'ecrire') {
	$dir_relatif .= $dir_relatif_array[$i];
	$i++;
}
if($dir_relatif != '') $dir_relatif = '/'.$dir_relatif;
define('_DIR_PLUGIN_TINYMCE', $dir_relatif.'/plugins/tinymce');*/

?>