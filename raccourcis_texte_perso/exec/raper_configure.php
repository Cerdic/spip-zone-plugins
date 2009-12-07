<?php

// exec/raper_configure.php

	/*****************************************************
	Copyright (C) 2009 Christian PAULUS
	cpaulus@quesaco.org - http://www.quesaco.org/
	/*****************************************************
	
	This file is part of RaPer.
	
	RaPer is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.
	
	RaPer is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with RaPer; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	
	/*****************************************************
	
	Ce fichier est un des composants de RaPer. 
	
	RaPer est un programme libre, vous pouvez le redistribuer et/ou le modifier 
	selon les termes de la Licence Publique Generale GNU publie'e par 
	la Free Software Foundation (version 2 ou bien toute autre version ulterieure 
	choisie par vous).
	
	RaPer est distribue' car potentiellement utile, mais SANS AUCUNE GARANTIE,
	ni explicite ni implicite, y compris les garanties de commercialisation ou
	d'adaptation dans un but spe'cifique. Reportez-vous a' la Licence Publique Ge'ne'rale GNU 
	pour plus de details. 
	
	Vous devez avoir reçu une copie de la Licence Publique Generale GNU 
	en meme temps que ce programme ; si ce n'est pas le cas, ecrivez a' la  
	Free Software Foundation, Inc., 
	59 Temple Place, Suite 330, Boston, MA 02111-1307, Etats-Unis.
	
	*****************************************************/
	
// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/raper_api_globales');
include_spip('inc/raper_api_journal');

function exec_raper_configure_dist () {

	global $connect_statut
		, $connect_toutes_rubriques
		, $connect_id_auteur
		, $couleur_foncee
		, $spip_lang_right
		;
		
	$flag_editable = (($connect_statut == "0minirezo") && ($connect_toutes_rubriques));

	if($flag_editable) {
		
		$prefs = raper_lire_preferences($prefs_modifiees);
		$editer_quoi = array('editer_tout', 'editer_public', 'editer_ecrire', 'editer_spip' ,'editer_local');
	
		if (_request('raper_valider')) {

			// traiter les infos retour du formulaire

			$autoriser_gerer = _request('autoriser_gerer');
			if (autoriser_gerer && (autoriser_gerer != $prefs['autoriser_gerer'])) {
				$prefs['autoriser_gerer'] = $autoriser_gerer;
			}
			
			$prefs['editer_tout'] = ((_request('editer_tout') == 'oui') ? 'oui' : 'non');
			$pas_tout = ($prefs['editer_tout'] == 'non');
			foreach($editer_quoi as $key) {
				if($key == 'editer_tout') continue;
				$prefs[$key] = ($pas_tout && (_request($key) == 'oui') ? 'oui' : 'non');
			}

			// verifier le type langue choisi
			if(!$ii = _request('type_langues')) $ii = _RAPER_TYPE_LANGUES_MULTILINGUE;
			$prefs['type_langues'] = raper_type_langues ($ii);

			raper_ecrire_preferences($prefs);
		}
	}
	
////////////////////////////////////
// PAGE CONTENU
////////////////////////////////////

	$titre_page = _T('icone_configuration_site');
	$rubrique = 'configuration';
	$sous_rubrique = _RAPER_PREFIX;

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo($commencer_page(_T('raper:raper') . " - " . $titre_page, $rubrique, $sous_rubrique));
	
	// la configuration du raper est reservee aux supers-admins 
	if(!$flag_editable) {
		die (raper_terminer_page_non_autorisee() . fin_page());
	}

	$page_result = ""
		. "<br /><br /><br />\n"
		. raper_gros_titre(_T('titre_page_config_contenu'), '', true)
		. barre_onglets($rubrique, $sous_rubrique)
		. debut_gauche($rubrique, true)
		. raper_boite_plugin_info()
		//. raper_boite_info_raper(true)
		. pipeline('affiche_gauche', array('args'=>array('exec'=>'raper_configure'),'data'=>''))
		. creer_colonne_droite($rubrique, true)
		. raper_boite_raccourcis($rubrique, true)
		. pipeline('affiche_droite', array('args'=>array('exec'=>'raper_configure'),'data'=>''))
		. debut_droite($rubrique, true)
		;

	//////////////////////////////////////////////////////

	$page_result .= ""
		. debut_cadre_trait_couleur("administration-24.gif", true, "", _T('raper:parametrer_le_raper'))
		. "<form action='" . generer_url_ecrire("raper_configure") . "' class='raper-form' method='post'>\n"
		// deleguer
		. "<fieldset>\n"
		. "<legend>" . _T('raper:delegation_') . "</legend>\n"
		. "<p class='verdana2'>" . _T('raper:deleguer_description') . "</p>\n"
		. raper_form_checkbox ('autoriser_gerer', _T('raper:deleguer_restreints_')
			, 'restreints', ($prefs['autoriser_gerer'] == 'restreints'))
		. "</fieldset>\n"
		// perimetre
		. "<fieldset>\n"
		. "<legend>" . _T('raper:perimetre_') . "</legend>\n"
		. "<p class='verdana2'>" . _T('raper:perimetre_description') . "</p>\n"
		;
	foreach($editer_quoi as $key) {
		$class = (($key == 'editer_tout') ? 'tout' : 'choix');
		$page_result .= raper_form_checkbox ($key, _T('raper:'.$key), "oui", ($prefs[$key] == 'oui'), $class);
	}
	$page_result .= ""
		. "</fieldset>\n"
		;
		
	// si le site est multilingue, proposer de gerer les langues utilisees
	// ou uniquement celles definies par ?exec=config_multilang
	if(substr_count($GLOBALS['meta']['langues_multilingue'], ',') >= 1) {
		$page_result .= ""
			. "<fieldset>\n"
			. "<legend>" . _T('raper:langues_') . "</legend>\n"
			. "<p class='verdana2'>" . _T('raper:langues_description') . "</p>\n"
			. raper_form_checkbox ('type_langues', _T('raper:gerer_langues_utilisees_')
				, _RAPER_TYPE_LANGUES_UTILISEES, ($prefs['type_langues'] == _RAPER_TYPE_LANGUES_UTILISEES))
			. "</fieldset>\n"
			;
	}
	
	$page_result .= ""		
		// bouton de validation
		. "<div style='text-align:right;'>"
		. "<input type='submit' name='raper_valider' class='fondo' value='"._T('bouton_valider')."' /></div>\n"
		. "</form>\n"
		. fin_cadre_trait_couleur(true)
		;

	// Fin de la page
	echo($page_result);

	echo pipeline('affiche_milieu',array('args'=>array('exec'=>$sous_rubrique),'data'=>''))
		, raper_html_signature()
		, fin_gauche(), fin_page();
	
	
} // exec_raper_config()

/*
 * Bouton radio
 * @return string
 * @param $name string
 * @param $title string
 * @param $value string
 * @param $checked bool
 * @param $class string
 */
function raper_form_radio ($name, $title, $value, $checked, $class = '') {
	$checked = ($checked ? " checked='checked'" : "");
	$class = (!empty($class) ? " class='$class'" : "");
	$result = ""
		. "<label class='verdana2'><input type='radio' name='$name' value='$value'".$checked.$class." />" . $title . "</label>\n"
		;
	return($result);
}

/*
 * Boite a' cocher
 * @return string
 * @param $name string
 * @param $title string
 * @param $value string
 * @param $checked bool
 * @param $class string
 */
function raper_form_checkbox ($name, $title, $value, $checked, $class = '') {
	$checked = ($checked ? " checked='checked'" : "");
	$class = (!empty($class) ? " class='$class'" : "");
	$result = ""
		. "<label class='verdana2'><input type='checkbox' name='$name' value='$value'".$checked.$class." />" . $title . "</label>\n"
		;
	return($result);
}

