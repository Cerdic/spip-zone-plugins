<?php

// exec/foni_config.php


// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

/**********************************************
 * Copyright (c) 2010 Christian Paulus - http://www.quesaco.org
 * Dual licensed under the MIT and GPL licenses.
 **********************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/foni_api');

function exec_foni_config () {

	global $spip_lang_right;
		
	$autorise = foni_autoriser_modifier();

	$old_prefs = $prefs = foni_lire_preferences();

	if($autorise && _request('fontes_valider')) {
	// retour de formulaire...
	
		if(	($foni_fontes_sel = _request('foni_fontes_sel'))
			&& ($foni_fontes_family = _request('foni_fontes_family')))
		{
			$fontes = array();
			foreach($foni_fontes_sel as $fname)
			{
				$family = $foni_fontes_family[$fname];
				if($fname && $family) {
					$fontes[$fname] = $family;
				}
			}
			if(count($fontes))
			{
				$prefs['fontes'] = $fontes;
			}
		}
		
		$prefs['include'] =
			(_request('foni_include') == 'oui')
			? 'oui'
			: 'non'
			;
		
		if(serialize($old_prefs) != serialize($prefs))
		{
			$prefs = foni_ecrire_preferences($prefs);
		}
	}
	
	
////////////////////////////////////
// PAGE CONTENU
////////////////////////////////////

	$titre_page = _T('icone_configuration_site');
	// Permet entre autres d'ajouter les classes a' la page : <body class='$rubrique $sous_rubrique'>
	$rubrique = 'configuration';
	$sous_rubrique = _FONI_PREFIX;

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo($commencer_page(_T('foni:foni') . " - " . $titre_page, $rubrique, $sous_rubrique));
	
	if(!$autorise)
	{
		die ('<p>' . _T('avis_non_acces_page') . '</p>' . PHP_EOL . fin_page());
	}

	$page_result = ""
		. '<br /><br /><br />' . PHP_EOL
		. gros_titre(_T('titre_page_config_contenu'), '', false)
		. barre_onglets($rubrique, $sous_rubrique)
		. debut_gauche($rubrique, true)
		. foni_fontes_selector($prefs)
		. pipeline('affiche_gauche', array('args'=>array('exec'=>'foni_configure'),'data'=>''))
		. creer_colonne_droite($rubrique, true) 
		. pipeline('affiche_droite', array('args'=>array('exec'=>'foni_configure'),'data'=>''))
		. debut_droite($rubrique, true)
		;

	$page_result .= PHP_EOL
		. debut_cadre_trait_couleur('', true, '', _T('foni:visualisation_fonte'))
		. '<div id="foni_font_preview">'
		. '<p>' . _T('foni:visualisation_fonte_txt') . '</p>'
		. '</div>' . PHP_EOL
		;
		
	$page_result .= ''
		. fin_cadre_trait_couleur(true)
		;
	
	// Fin de la page
	echo($page_result);

	echo pipeline('affiche_milieu',array('args'=>array('exec'=>$sous_rubrique),'data'=>''))
		, fin_gauche(), fin_page();

}


/*
 * @return string l'objet select pour le formulaire
 * */
function foni_fontes_selector ($prefs) {
	
	// demande la liste des fontes diponibles sur le site
	$available_fonts = foni_fonts_collecter();

	$result = '';
	$ii = 0;
	
	foreach($available_fonts as $key => $val)
	{
		$id = 'foni_fontes_sel'.$ii;
		
		$checked = (isset($prefs['fontes'][$key]) ? ' checked="checked"' : '');
		
		$family = ($checked ? $prefs['fontes'][$key] : $key);
		
		$result .= PHP_EOL
			. '<tr><td class="face">'
			. '<input type="checkbox" name="foni_fontes_sel[]" id="'.$id.'" value="' . $key	. '" ' . $checked . ' />'
			. '<label class="face" for="'.$id.'">'
			. $key
			. '</label>'
			. '</td><td class="family">'
				. '<input type="text" name="foni_fontes_family[' . $key . ']" type="text" size="10" maxlength="10" class="forml" '
					. ' value="' . $family .'" />'
				. '<label class="family">Family</label>'
			. '</td></tr>'
			. PHP_EOL
			;
		$ii++;
	}

	$result = PHP_EOL
		. '<form id="foni_form_font" action="' . generer_url_ecrire('foni_config') . '" method="post">' . PHP_EOL
		. '<fieldset>'
		. '<legend class="help">' . _T('foni:fontes_dispo_sel') . '<span>[?]</span></legend>'
		. '<p class="help" style="display:none">' . _T('foni:fontes_dispo_sel_legend') . '</p>'
		. '<table id="foni_fontes_sel">' . PHP_EOL
		. '<tr class="fondo"><th class="face">face</th><th>family</th></tr>'
		. $result
		. '</table>' . PHP_EOL
		. '</fieldset>'
		//
		. '<fieldset>'
		. '<legend class="help">' . _T('foni:methode') . '<span>[?]</span></legend>'
		. '<p class="help" style="display:none">' . _T('foni:methode_legend') . '</p>'
		. '<input type="checkbox" name="foni_include" id="foni_include" value="oui" '
				. (($prefs['include'] == 'oui') ? ' checked="checked"' : '')
				. ' />'
		. '<label for="foni_include">'
			. _T('foni:include_font')
			. '</label>'
		. '</fieldset>'
		//
		. '<div style="margin-top:1em;text-align:right">' . PHP_EOL
		. '<input type="submit" name="fontes_valider" value="' . _T('bouton_valider') . '" class="fondo" />'
		. '</div>' . PHP_EOL
		. '</form>' . PHP_EOL
		;
	
	$result = PHP_EOL
		. debut_cadre_relief(_FONI_IMAGES_DIR.'fontes_importer-24.png', true, '', _T('foni:selection_fonte'))
		. '<div class="verdana1">' . PHP_EOL
		. '<div>' . PHP_EOL
		. $result
		. '</div>'
		. '</div>'
		. fin_cadre_relief(true)
		;

	return($result);
}

