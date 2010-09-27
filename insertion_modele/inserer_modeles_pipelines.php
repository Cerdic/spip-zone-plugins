<?php
function inserer_modeles_porte_plume_barre_pre_charger($barres){
	$barre = &$barres['edition'];
	
	$barre->ajouterApres('notes', array(
		"id"          => 'barre_img_dft',
		"name"        => _T('inserer_modeles:barre_img_dft'),
		"className"   => 'outil_barre_img_dft',
		"replaceWidth"   => 'function(markitup) {zone_selection = markitup.textarea;}',
		"display"     => true
	));
	
	return $barres;
}

function inserer_modeles_porte_plume_lien_classe_vers_icone($flux){
	return array_merge($flux, array(
		'outil_barre_img_dft' => 'barre-is.png',
	));
}

function inserer_modeles_header_prive($flux) {
	$flux .= '<script type="text/javascript" src="' . _DIR_PLUGIN_INSERER_MODELES . 'javascript/inserer_modeles.js"></script>' . "\n";
	$flux.= '<link rel="stylesheet" type="text/css" href="' . _DIR_PLUGIN_INSERER_MODELES . 'css/inserer_modeles_prive.css" />' . "\n";
	return $flux;
}

function inserer_modeles_insert_head($flux) {
	$flux.= '<link rel="stylesheet" type="text/css" href="' . _DIR_PLUGIN_INSERER_MODELES . 'css/inserer_modeles_public.css" media="all" />' . "\n";
	return $flux;
}

?>