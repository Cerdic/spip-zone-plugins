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

function onglets_texte_porte_plume_barre_pre_charger($barres){
	$barre_edition = &$barres['edition'];

	$module_barre = "barre_outils";
	if (intval($GLOBALS['spip_version_branche'])>2)
	$module_barre = "barreoutils";
					
	$barre_edition->ajouterApres('barre_cadre', array(
		"id"          => 'onglets_texte',
		"name"        => 'Insérer un système d\'onglets',
		"className"   => "outil_onglets",
		"replaceWith" => "\n<onglet|debut|titre=Titre du premier onglet>\ncontenu du premier onglet\n\n<onglet|titre=Titre du deuxième onglet>\ncontenu du deuxième onglet\n\n<onglet|titre=Titre du troisième onglet>\ncontenu du troisième onglet\n\n<onglet|fin>\n",
		"display"     => true,
	));
	
	return $barres;
}

function onglets_texte_porte_plume_lien_classe_vers_icone($flux){
	return array_merge($flux, array(
		'outil_onglets' => array('onglets.png','0'),
	));
}