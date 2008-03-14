<?php 

	// exec/lido_configuration.php
	
	
	// page de configuration espace privé

	// $LastChangedRevision$
	// $LastChangedBy$
	// $LastChangedDate$

	/*****************************************************
	Copyright (C) 2008 Christian PAULUS
	cpaulus@quesaco.org - http://www.quesaco.org/
	/*****************************************************
	
	This file is part of LiDo.
	
	LiDo is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.
	
	LiDo is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with LiDo; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	
	/*****************************************************
	
	Ce fichier est un des composants de LiDo. 
	
	LiDo est un programme libre, vous pouvez le redistribuer et/ou le modifier 
	selon les termes de la Licence Publique Generale GNU publiée par 
	la Free Software Foundation (version 2 ou bien toute autre version ultérieure 
	choisie par vous).
	
	LiDo est distribué car potentiellement utile, mais SANS AUCUNE GARANTIE,
	ni explicite ni implicite, y compris les garanties de commercialisation ou
	d'adaptation dans un but spécifique. Reportez-vous à la Licence Publique Générale GNU 
	pour plus de détails. 
	
	Vous devez avoir reçu une copie de la Licence Publique Generale GNU 
	en meme temps que ce programme ; si ce n'est pas le cas, ecrivez à la  
	Free Software Foundation, Inc., 
	59 Temple Place, Suite 330, Boston, MA 02111-1307, États-Unis.
	
	*****************************************************/
	
if (!defined("_ECRIRE_INC_VERSION")) return;


function exec_lido_configuration () {

	global $connect_statut
		, $connect_toutes_rubriques
		, $spip_lang_left
		, $spip_lang_right
		;

	include_spip('inc/presentation');
	include_spip('inc/meta');
	include_spip('inc/urls');
	include_spip('inc/utils');
	include_spip('inc/acces');
	include_spip('inc/plugin_globales_lib');
	include_spip('inc/lido_api_presentation');
	
	if (!(($connect_statut == '0minirezo') && $connect_toutes_rubriques)) {
		echo _T('avis_non_acces_page');
		echo fin_gauche(), fin_page();
		exit;
	}
	
	$lido_values_array = unserialize(_LIDO_DEFAULT_VALUES_ARRAY);
	
	////////////////////////////////////
	// initialise les variables postées par le formulaire
	$config = array();
	foreach(array_merge(
		array(
			'btn_valider_configure'
		)
		, array_keys($lido_values_array)
		) as $key) {
		$$key = _request($key);
	}
	$lido_email_moderateur = substr($lido_email_moderateur, 0, 64);
	$lido_email_tag = substr($lido_email_tag, 0, 64);
	
	$rubrique = "configuration";
	
	$message_gauche = $message_erreur = ""; 
	
	////////////////////////////////////
	// valider la configuration
	if($btn_valider_configure) {
		$config = array();
		// compléter les checkbox manquantes
		foreach($lido_values_array as $key => $value) {
			if(($value == 'oui') && !isset($$key)) {
				// si radio non coché, valeur = 'non'
				$$key = 'non';
			}
		}
		// initialiser la config par défaut si besoin (installation non configurée)
		foreach($lido_values_array as $key => $value) {
			$config[$key] = (isset($$key) && !empty($$key)) ? $$key	: $lido_values_array[$key];
		}
		// ecrire la config
		__plugin_ecrire_key_in_serialized_meta('config', $config, _LIDO_META_PREFERENCES);
		__ecrire_metas();
		
	}
	
	// recharger. mettre à jour les variables locales
	$config = __plugin_lire_key_in_serialized_meta('config', _LIDO_META_PREFERENCES);
	foreach($config as $key=>$value) {
		$$key = $value;
	}

	// les valeurs obligatoires par defaut
	foreach($lido_values_array as $key => $value) {
		if(!$$key || empty($$key)) $$key = $value;
	}
	
	// la liste des secteurs
	$sql_select = "SELECT id_rubrique,titre FROM spip_rubriques WHERE id_parent=0";
	if($sql_result = spip_query($sql_select)) {
		$liste_rubriques = array();
		while ($row = spip_fetch_array($sql_result)) {
			$liste_rubriques[] = $row;
		}
	}
	$rubriques_presentes = (isset($liste_rubriques) && count($liste_rubriques));
	
	if(!$rubriques_presentes) {
		$message_erreur .= _T(_LIDO_LANG."pas_de_rubriques");
	}
	
	// la liste des auteurs
	$sql_select = "SELECT statut,id_auteur,nom,email FROM spip_auteurs WHERE statut='0minirezo' OR statut='1comite' ORDER BY statut,nom";
	if($sql_result = spip_query($sql_select)) {
		$liste_auteurs = array('0minirezo' => array(), '1comite' => array());
		while ($row = spip_fetch_array($sql_result)) {
			$liste_auteurs[$row['statut']][] = $row;
		}
	}
	
	$breves_activees = (isset($GLOBALS['meta']['activer_breves']) && ($GLOBALS['meta']['activer_breves'] == "oui"));
	
	if(!$breves_activees) {
		if(!empty($message_erreur)) {
			$message_erreur .= "<br />";
		}
		$message_erreur .= _T(_LIDO_LANG."breves_non_activees");
	}
	
	if(!empty($message_gauche)) { $message_gauche = lido_ligne_paragraphe($message_gauche); }
	if(!empty($message_erreur)) { $message_erreur = lido_ligne_paragraphe($message_erreur, 'font-weight:bold;'); }

	////////////////////////////////////
	// fin traitements

	$commencer_page = charger_fonction('commencer_page', 'inc');


////////////////////////////////////
// PAGE CONTENU
////////////////////////////////////
	
	$page_result = ""
		. $commencer_page(_T(_LIDO_LANG."configuration_livre_dor"), _LIDO_PREFIX)
		. "<div style='height:3em;'></div>\n"
		. gros_titre(_T(_LIDO_LANG."configuration_livre_dor"), "", false)
		. barre_onglets($rubrique, _LIDO_PREFIX)
		. debut_gauche($rubrique, true)
		. __plugin_boite_meta_info(_LIDO_PREFIX, true)
		. $message_gauche
		. creer_colonne_droite($rubrique, true)
		. debut_droite($rubrique, true)
		;

	////////////////////////////////////
	// introduction
	$page_result .= ""
		. debut_cadre_trait_couleur(_DIR_PLUGIN_LIDO_IMG_PACK."administration-24.png", true, '', _T(_LIDO_LANG."configuration_livre_dor"))
		. debut_cadre_trait_couleur('', true, '', '')
		. lido_form_description('configuration_livre_dor_desc')
		. $message_erreur
		. fin_cadre_trait_couleur(true)
		. lido_form_debut_form('form_configuration')
		;
	//
	// préciser article ou breve
	if(!$breves_activees) {
		$lido_table_destination = "articles";
		$page_result .= ""
			. "<input type='hidden' name='lido_table_destination' value='articles' />\n"
			;
	} else {
		$page_result .= ""
			. debut_cadre_relief('', true, '', _T(_LIDO_LANG."configurer_article_breve"))
			. lido_form_description('configurer_article_breve_desc_')
			. "<ul class='liste_choix'>\n"
			. "<li>".lido_form_radio_button ('lido_table_destination', 'd_article', $lido_table_destination, 'articles')."</li>\n"
			. "<li>".lido_form_radio_button ('lido_table_destination', 'de_breve', $lido_table_destination, 'breves')."</li>\n"
			. "</ul>\n"
			//
			. fin_cadre_relief(true)
			;
	}
	//
	// secteur/rubrique de destination
	$page_result .= ""
		. debut_cadre_relief('', true, '', _T(_LIDO_LANG."rubrique_destination"))
		;
	if($rubriques_presentes) {
		$page_result .= ""
			. lido_form_description('selectionnez_la_rubrique_')
			. debut_cadre_couleur('', true)
			. "<div id='boite_chercher_rubrique'>\n"
			. lido_chercher_rubrique($lido_id_rubrique, rtrim($lido_table_destination, 's'), 'lido_id_rubrique')
			. "</div>\n"
			. fin_cadre_couleur(true)
			;
	} else {
		$page_result .= lido_ligne_paragraphe(_T(_LIDO_LANG."pas_de_rubriques"));
	}
	$page_result .= ""
		. fin_cadre_relief(true)
		;
	//
	// valider auto ?
	$page_result .= ""
		. debut_cadre_relief('', true, '', _T(_LIDO_LANG.'mode_publication'))
		. "<ul style='list-style: none;margin:0;padding:0;'>\n"
		. "<li>".lido_form_radio_button ('lido_valider_auto', 'bouton_radio_publication_immediate', $lido_valider_auto, 'oui', false)."</li>\n"
		. "<li>".lido_form_radio_button ('lido_valider_auto', 'bouton_radio_moderation_priori', $lido_valider_auto, 'non', false)."</li>\n"
		. "</ul>\n"
		//
		. fin_cadre_relief(true)
		;
	//
	// prévenir modérateur par mail ?
	if(empty($lido_email_moderateur)) {
		$lido_email_moderateur = $GLOBALS['meta']['email_webmaster'];
	}
	$page_result .= ""
		. debut_cadre_relief('', true, '', _T(_LIDO_LANG.'prevenir_moderateur'))
		. lido_form_description('prevenir_moderateur_desc')
		. "<ul style='list-style: none;margin:0;padding:0;'>\n"
		. "<li>".lido_form_radio_button ('lido_prevenir_moderateur', 'ne_pas_prevenir', $lido_prevenir_moderateur, 'non')."</li>\n"
		. "<li>".lido_form_radio_button ('lido_prevenir_moderateur', 'prevenir', $lido_prevenir_moderateur, 'oui')
			. "<span id='lido_bloc_email_moderateur' style='display:".(($lido_prevenir_moderateur == 'oui') ? 'block' : 'none')."'>"
			. lido_form_input_text('lido_email_moderateur', 'indiquez_email_',  $lido_email_moderateur)
			. lido_form_input_text('lido_email_tag', 'indiquez_tag_',  $lido_email_tag)
			. "</span>\n"
		."</li>\n"
		. "</ul>\n"
		//
		. fin_cadre_relief(true)
		;
	//
	// attribuer les articles/breves à un auteur
	$selected = ((!$lido_id_auteur) ? " selected='selected'" : "" );
	$page_result .= ""
		. "<div id='bloc_attribuer_auteur' style='display:".(($lido_table_destination == 'articles') ? 'block' : 'none')."'>\n"
		. debut_cadre_relief('', true, '', _T(_LIDO_LANG."attribuer_auteur"))
		. lido_form_description('attribuer_auteur_desc')
		. debut_cadre_couleur('', true)
		. "<select name='lido_id_auteur' style='font-size: 90%; width: 99%; max-height: 24px;' class='verdana1' size='1'>\n"
		. "<option value='0'".$selected.">&lt;"._T(_LIDO_LANG."auteur_aucun")."&gt;</option>\n"
		;
	$optgroup = array('0minirezo' => _T('info_administrateurs'), '1comite' => _T('info_redacteurs'));
	foreach($liste_auteurs as $statut => $auteur) {
		$page_result .= "<optgroup label='".$optgroup[$statut]."'>\n";
		foreach($auteur as $key => $value) {
			$selected = (($value['id_auteur'] == $lido_id_auteur) ? " selected='selected'" : "" );
			$page_result .= "<option value='".$value['id_auteur']."'".$selected.">".$value['nom']." (".$value['email'].")</option>\n";
		}
		$page_result .= "</optgroup>\n";
	}
	$page_result .= ""
		. "</select>\n"
		. fin_cadre_couleur(true)
		. fin_cadre_relief(true)
		. "</div>\n"
		;
	
	// url pour ajax
	$page_result .= ""
		. "<input type='hidden' id='lido_url_action' value='".generer_action_auteur('lido_chercher_rubrique', '')."' />\n"
		;
	//
	// bouton valider
	$page_result .= ""
		. "<div style='text-align:$spip_lang_left;margin:0.75em 0 0;'>\n"
		. "<div style='text-align:$spip_lang_right;margin:0;'>\n"
		. "<input type='reset' name='btn_valider_annule' value='"._T('bouton_annuler')."' class='fondo' />\n"
		. "<input type='submit' name='btn_valider_configure' value='"._T('bouton_valider')."' class='fondo' />\n"
		. "</div>\n"
		. "</div>\n"
		;
	//
	// fin du formulaire
	$page_result .= ""
		. lido_form_fin_form()
		. fin_cadre_trait_couleur(true)
		;
	
	echo($page_result);
	echo __plugin_html_signature(_LIDO_PREFIX, true, true, true), fin_gauche(), fin_page();
	return(true);
}

/***********************************************/

/**/
function lido_ligne_paragraphe ($texte, $style = "") {
	$style = empty($style) ? $style : " style='$style'";
	return("<p class='verdana2'".$style.">".$texte."</p>\n");
}

/**/
function lido_form_input_text ($nom_champ, $label_champ, $value) {
	$page_result = ""
		. "<label for='$nom_champ' class='verdana2' style='display:block;margin-top:0.5em;'>"._T(_LIDO_LANG.$label_champ)."</label>\n"
		. "<input type='text' name='$nom_champ' id='$nom_champ' size='40' class='forml' style='margin-top:0.5em;' value=\"".$value."\" />\n"
		;
	return($page_result);
}

/**/
function lido_form_debut_form ($nom_form, $ancre = '') {
	global $spip_lang_left;
	if(empty($ancre)) {
		$ancre = $nom_form;
	}
	$page_result = ""
		. "<div style='text-align: $spip_lang_left;' class='verdana2'>\n"
		. "<form name='$nom_form' id='$nom_form' method='post' action='".$_SERVER['REQUEST_URI']."#$ancre'>\n"
		;
	return($page_result);
}

/**/
function lido_form_fin_form () {
	$page_result = ""
		. "</form>\n"
		. "</div>\n"
		;
	return($page_result);
}

/**/
function lido_form_description ($texte) {
	if(!$texte) return ('');
	return( ""
		. "<div class='verdana2' style='text-align: $spip_lang_left;font-style: italic;margin-bottom:0.5em' >\n"
		. _T(_LIDO_LANG.$texte)
		. "</div>\n"
	);
}

/**/
function lido_form_radio_button ($nom_radio, $label_radio, $current_value, $value, $lido_lang = true) {
	static $id = 1;
	$title = ($lido_lang) ? _T(_LIDO_LANG.$label_radio) : _T($label_radio);
	$page_result = ""
		. "<div class='verdana2'>\n"
		. "<input type='radio' name='$nom_radio' value='$value' id='".$nom_radio."_$id'"
			. (($current_value == $value) ? " checked='checked'" : "")
			. " title = \"$title\" />\n"
		. "<label for='".$nom_radio."_$id'>$title</label>\n"
		. "</div>\n"
		;
	$id++;
	return($page_result);
}

//
?>