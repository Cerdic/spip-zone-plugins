<?php

// Insertion dans le porte-plume

function pp_chgt_lg_porte_plume_barre_pre_charger($barres) {
	$barre = &$barres['edition'];
	$barre->ajouterApres('grpCaracteres', array(
				"id" => "sepChgtLang",
				"separator" => "---------------",
				"display"   => true,
	));
	
	
	
	$barre->ajouterApres('sepChgtLang', array(
		"id"          => 'changement_langue',
		"name"        => _T('pp_chgt_lg:outil_changement_langue'),
		"className"   => 'outil_changement_langue',
		"openWith"    => "<multi>[[!["._T('pp_chgt_lg:code_langue')."]!]]",
		"closeWith"   => "</multi>",
		"display"     => true
	 ));
	
	return $barres;
}

// Icônes pour le porte-plume

function pp_chgt_lg_porte_plume_lien_classe_vers_icone($flux) {
	$icones = array();
	$icones['outil_changement_langue'] = 'changement_langue.png';
	return array_merge($flux, $icones);
}

// insert le css pour les styles supplementaires de la BTE dans le <head> du document (#INSERT_HEAD)
function pp_chgt_lg_insert_head_css($flux) {
	static $done = false;
	if (!$done) {
		$done = true;
		$flux .= "\n".'<link rel="stylesheet" href="'.find_in_path('css/pp_chgt_lg.css').'" type="text/css" media="all" />';
	}
	return $flux;
}

// insert le css pour les styles supplementaires de la BTE dans le <head> du document (#INSERT_HEAD)
function pp_chgt_lg_insert_head($flux) {
	$flux = pp_chgt_lg_insert_head_css($flux);
	return $flux;
}
function pp_chgt_lg_header_prive($texte) {
	$texte.= '<link rel="stylesheet" type="text/css" href="' . _DIR_PLUGIN_PP_CHGT_LG . 'css/pp_chgt_lg_prive.css" />' . "\n";
	return $texte;
}


?>