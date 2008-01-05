<?php

function Smoothgallery_insert_head($flux){
/*
$id_google = lire_config('googleanalytics/idGoogle');
if (!$id_google || $id_google == '_' || $id_google == 'UA-xxxxxx') {
		return '';
	}
	else {
*/
if (!defined('_DIR_PLUGIN_SMOOTHGALLERY')){
	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	define('_DIR_PLUGIN_SMOOTHGALLERY',(_DIR_PLUGINS.end($p)));
}

$flux .= '<script src="'._DIR_PLUGIN_SMOOTHGALLERY.'scripts/mootools.v1.11.js" type="text/javascript"></script>
<script src="'._DIR_PLUGIN_SMOOTHGALLERY.'scripts/jd.gallery.js" type="text/javascript"></script>
<link rel="stylesheet" href="'._DIR_PLUGIN_SMOOTHGALLERY.'css/jd.gallery.css" type="text/css" media="screen" />
';
return $flux;
//}
}

?>
