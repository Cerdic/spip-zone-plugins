<?php

// exec/foni_pipelines.php


// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

/**********************************************
 * Copyright (c) 2010 Christian Paulus - http://www.quesaco.org
 * Dual licensed under the MIT and GPL licenses.
 **********************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/foni_api');


/**
 * pipeline header_prive
 *
 * Ajoute le css et javascript necessaires dans le <head>
 * pour la page de configuratin du plugin en espace prive'
 * 
 * @param unknown_type $flux
 * @return unknown_type
 */
function foni_header_prive ($flux) {

	$result = '';
	
	$prefs = foni_lire_preferences();

	$flux .= foni_header_sig();
	
	if(_request('exec') == 'foni_config') {
		
		$flux .= '<style type="text/css"><!--' . PHP_EOL
			. import_script(_DIR_PLUGIN_FONI . 'foni_prive.css') . PHP_EOL
			. '--></style>' . PHP_EOL
			. '<script type="text/javascript">' . PHP_EOL
			. '//<![CDATA[' . PHP_EOL
			. import_script(_DIR_PLUGIN_FONI . 'javascript/foni_prive.js') . PHP_EOL
			. '//]]>' . PHP_EOL
			. '</script>' . PHP_EOL
			;
	}
	
	return ($flux);
}
/**
 * pipeline insert_head
 * 
 * Insere le code necessaire dans le <head> en espace public
 * 
 * @param unknown_type $flux
 * @return unknown_type $flux
 */
function foni_insert_head ($flux) {

	$result = '';
	

	$flux .= foni_header_sig();

	$prefs = foni_lire_preferences();

	$available_fonts = foni_fonts_collecter();
	
	$skel = 'inc-inserer_fontes';

	foreach($prefs['fontes'] as $fontname => $family)
	{
		list($font_eot, $font_ttf, $font_path) = explode('|', $available_fonts[$fontname]);
		
		$contexte = array(
			'fontname' => $fontname
			, 'family' => $family
			, 'font_eot' => $font_eot
			, 'font_ttf' => $font_ttf
			, 'font_path' => $font_path
			, 'foni_include' => $prefs['include']
		);
		$skel = 'inc-head_font_face';
		
		if($fond = find_in_path($skel.'.html'))
		{
			$result = recuperer_fond($skel, $contexte);	
		}
		else
		{
			$result = '<!-- squelette not found! -->' . PHP_EOL;
		}
		$flux .= $result;
	}
	return ($flux);
}

/*
 * Onglet de configuration du plugin
 * Apparait en espace prive' pour le bouton Configuration
 *
 * @return unknown_type $flux
 * */
function foni_ajouter_onglets ($flux) {

	global $connect_statut
		, $connect_toutes_rubriques
		;

	if(foni_autoriser_modifier()
		&& ($flux['args'] == 'configuration')
	) {
		$flux['data'][_FONI_PREFIX] = new Bouton( 
			_FONI_IMAGES_DIR.'fontes_importer-24.png'
			, _T('foni:foni')
			, generer_url_ecrire('foni_config')
			)
			;
	}
	return ($flux);
}
