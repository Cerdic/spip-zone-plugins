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

	$result = foni_header_sig();

	$prefs = foni_lire_preferences();

	$available_fonts = foni_fonts_collecter();
	
	$skel = _FONI_SKEL_HEAD_STD;

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
		// recherche le woff
		$font_woff = $font_path.'/'.substr($font_ttf, 0, strlen($font_ttf)-4).'.woff';
		
		if(file_exists($font_woff)) {
			$contexte['font_woff'] = $font_woff;
			$skel = _FONI_SKEL_HEAD_XTD;
		}
		
		if($fond = find_in_path($skel.'.html'))
		{
			$result .= recuperer_fond($skel, $contexte);
		}
		else
		{
			$result .= '<!-- squelette '.$fond.' not found! -->' . PHP_EOL;
		}
		
		// verifier le bon import (experimental)
		// @see: http://sameropensource.blogspot.com/2009/08/improved-solution-for-embedding-fonts.html
		// @see: http://code.google.com/p/jquery-fontavailable/downloads/list
		//
		// En chantier ici. Le pb: Firefox/Mac oublie parfois d'appliquer
		// la fonte. Il l'a charge, mais ne l'applique pas au css demande'.
		// @todo: tester sans le woff ? (ffx 3.6 charge le woff ET le ttf)
		if($f = find_in_path('javascript/jquery.fontavailable-1.1.min.js'))
		{
			$result .= '
<script type="text/javascript"><!--
' . file_get_contents($f)
. '// --></script>
<script type="text/javascript"><!--
	if(window.jQuery)jQuery(document).ready(function(){
		if ($.browser.mozilla) {
			var loaded, reviens, maxt = 5;
			boucle = function() {
				loaded = $.fontAvailable("' . $family . '");
				/* console.log("Fonte ' . $family . ' loaded: " + loaded); /* */
				maxt--;
				if(loaded || maxt<=0) {
					console.log("finish");
					clearInterval(reviens);
				}
				else {
					console.log("continue");
				}
			}
			if(!reviens) {
				/* console.log("setinterval call"); /* */
				reviens = setInterval(function(){ boucle(); }, 1000);
			}
		}

		/* console.log("Fonte ' . $family . ' loaded: " + $.fontAvailable("' . $family . '")); /* */
		$("#tete-baseline").click(function () {  
			/* console.log("Fonte ' . $family . ' loaded: " + $.fontAvailable("' . $family . '")); /* */
	    });
	});
// --></script>
'			;
			
		}
		
		$flux .= $result;
	}
	return($flux);
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
