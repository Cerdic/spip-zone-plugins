<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


$p = explode(basename(_DIR_PLUGINS) . "/", str_replace('\\', '/', realpath(dirname(__FILE__))));
if (!defined('_DIR_PLUGIN_ONGLETS_TEXTE')) {
	define('_DIR_PLUGIN_ONGLETS_TEXTE', (_DIR_PLUGINS . end($p)));
}

function onglets_texte_insert_head($flux) {
	$flux .= '<script type="text/javascript" src="' . find_in_path('javascript/mes_onglets.js') . '"></script>';

	return $flux;
}

function onglets_texte_insert_head_css($flux) {
	$flux .= '<link rel="stylesheet" href="' . find_in_path('css/tab.css') . '" type="text/css" media="all" />';

	return $flux;
}