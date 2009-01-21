<?php



function supprimer_enveloppe_mailto_insert_head($flux) {

	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	define('_DIR_PLUGIN_SUPPRIMER_ENVELOPPE_MAILTO',(_DIR_PLUGINS.end($p)));


	$flux .= '<link rel="stylesheet" href="'._DIR_PLUGIN_SUPPRIMER_ENVELOPPE_MAILTO.'supprimer_enveloppe_mailto.css" type="text/css" media="all" />';
	
	return $flux;
}

?>