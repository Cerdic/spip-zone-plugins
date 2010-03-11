<?php

// action/foni_font_preview.php

// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

/**********************************************
 * Copyright (c) 2010 Christian Paulus - http://www.quesaco.org
 * Dual licensed under the MIT and GPL licenses.
 **********************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/foni_api');

/*
 * Appel via Ajax pour visualiser la fonte de caracteres
 * dans la page de configuration, en espace prive'
 * @todo: traiter les $lang
 * */
function action_foni_font_preview () {

	$autorise = foni_autoriser_modifier();
	
	if($autorise && ($fontname = _request('fontname')))
	{
		include_spip('public/utils');
		include_spip('public/assembler');
		include_spip('inc/foni_api');
		
		$available_fonts = foni_fonts_collecter();

		list($font_eot, $font_ttf, $font_path) = explode('|', $available_fonts[$fontname]);

		$contexte = array(
				'fontname' => $fontname
				, 'font_eot' => $font_eot
				, 'font_ttf' => $font_ttf
				, 'font_path' => $font_path
				, 'foni_include' => 'oui'
				);
		
	
		$skel = 'modeles/foni_font_exemple';
		
		if($fond = find_in_path($skel.'.html'))
		{
			$result = recuperer_fond($skel, $contexte);	
		}
		else
		{
			foni_log('preview '.($result = 'squelette not found! ('.$skel.')'));
		}

		echo($result);
	}

	exit(0);
}
