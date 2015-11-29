<?php

$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_PAGERANK',(_DIR_PLUGINS.end($p))."/");

function afficher_pagerank($url, $racine=false) {
	include_spip("inc/calculer_pagerank");
	return round(trim(pb_getpagerank($url, $racine)));
}
?>
