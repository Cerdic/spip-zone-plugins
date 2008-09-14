<?php 

	// exec/amocles_stopwords.php
	
	// $LastChangedRevision$
	// $LastChangedBy$
	// $LastChangedDate$

	/*****************************************************
	Copyright (C) 2007-2008 Christian PAULUS
	cpaulus@quesaco.org - http://www.quesaco.org/
	/*****************************************************
	
	This file is part of Amocles.
	
	Amocles is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.
	
	Amocles is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with Amocles; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	
	/*****************************************************
	
	Ce fichier est un des composants de Amocles. 
	
	Amocles est un programme libre, vous pouvez le redistribuer et/ou le modifier 
	selon les termes de la Licence Publique Generale GNU publiee par 
	la Free Software Foundation (version 2 ou bien toute autre version ulterieure 
	choisie par vous).
	
	Amocles est distribue car potentiellement utile, mais SANS AUCUNE GARANTIE,
	ni explicite ni implicite, y compris les garanties de commercialisation ou
	d'adaptation dans un but specifique. Reportez-vous a la Licence Publique Generale GNU 
	pour plus de details. 
	
	Vous devez avoir recu une copie de la Licence Publique Generale GNU 
	en meme temps que ce programme ; si ce n'est pas le cas, ecrivez a la  
	Free Software Foundation, Inc., 
	59 Temple Place, Suite 330, Boston, MA 02111-1307, etats-Unis.
	
	*****************************************************/
	
if (!defined("_ECRIRE_INC_VERSION")) return;


function exec_amocles_stopwords () {

  global $connect_statut
	  , $connect_toutes_rubriques
	  , $connect_id_auteur
	  , $connect_id_rubrique
	  , $spip_lang_left
	  , $spip_lang_right
	  ;

	include_spip('inc/presentation');
//	include_spip('inc/meta');
	include_spip('inc/urls');
	include_spip('inc/utils');
//	include_spip('inc/acces');
	include_spip('inc/amocles_api_globales');
	include_spip('inc/amocles_api_prive');
	
	$flag_autorise = (($connect_statut == '0minirezo') && $connect_toutes_rubriques);
	$titre_page = _T(_AMOCLES_LANG."administration_mots_cles");
	$rubrique = "configuration";
	$sous_rubrique = _AMOCLES_PREFIX;
	$message_gauche = $message_erreur = "";
	
	if ($flag_autorise)
	{
		$stop_words = amocles_get_preference ('stop_words');
		if(!is_array($stop_words)) { $stop_words = array(); }
			
		////////////////////////////////////
		// initialise les variables postees par le formulaire
		foreach(array(
			'btn_valider_stopwords', 'input_stopwords'
			) as $key) {
			$$key = _request($key);
		}
		
		$langues_array = split(',', $GLOBALS['meta']['langues_utilisees']);

		if((count($langues_array) > 0) && $btn_valider_stopwords) 
		{
			foreach($langues_array as $lang)
			{
				if(isset($input_stopwords[$lang]))
				{
					$les_mots = array();
					$ii = explode("\n", $input_stopwords[$lang]);
					$stop_words[$lang] = array();
					
					// # pour les remarques dans l'import
					// tt ligne ou suite de ligne commencant par # est ignorée
					$ii = preg_replace("/#.*$/", "", $ii);
					
					foreach($ii as $mot)
					{
						$mot = trim($mot);
						if(strlen($mot) >= 3) {
							$les_mots[] = $mot;
						}
					}
					sort($les_mots);
					$stop_words[$lang] = array_unique($les_mots);
				}
			}
			amocles_set_preference ('stop_words', $stop_words);
		}
		
		////////////////////////////////////
		// versions traduites pour légender
		$nom_langue = array();
		foreach($langues_array as $lang)
		{
			$nom_langue[$lang] = ucwords(traduire_nom_langue($lang));
		}
			
		if(!empty($message_gauche)) {
			$message_gauche = "<div class='verdana2 message-gauche'>$message_gauche</div>\n";
		}
	
		if(!empty($message_erreur)) {
			$message_erreur = "<br />".amocles_boite_alerte($message_erreur);
		}
		
		// fin traitements
	} // fin if ($flag_autorise)
	
	$commencer_page = charger_fonction('commencer_page', 'inc');

////////////////////////////////////
// PAGE CONTENU
////////////////////////////////////
		
	$page_result = ""
		. $commencer_page($titre_page, $rubrique, $sous_rubrique)
		. "<br /><br /><br />\n"
		. gros_titre(_T(_AMOCLES_LANG."administration_mots_cles"), "", false)
		. barre_onglets($rubrique, _AMOCLES_PREFIX)
		. debut_gauche($rubrique, true)
		. amocles_boite_plugin_info()
		. $message_gauche
		;
	
	if (!$flag_autorise) {
		echo _T('avis_non_acces_page');
		echo fin_gauche(), fin_page();
		exit;
	}
	
	////////////////////////////////////
	// Boite des raccourcis 
	$page_result .= ""
		. creer_colonne_droite('',true)
		. amocles_bloc_raccourcis(
			_T('titre_cadre_raccourcis')
			, array(
				icone_horizontale(
					_T('icone_creation_groupe_mots')
					, generer_url_ecrire("mots_type", "new=oui")
					, _DIR_PLUGIN_AMOCLES_IMG_PACK."groupe-mots-24.png", "creer.gif", false
					)
				, icone_horizontale(
					_T(_AMOCLES_LANG."deleguer_admin")
					, generer_url_ecrire("amocles_configuration", "#delegation")
					, _DIR_PLUGIN_AMOCLES_IMG_PACK."redacteurs-admin-24.png"
					, "", false
					)
				, icone_horizontale(
					_T(_AMOCLES_LANG."importer_mots_cles")
					, generer_url_ecrire("amocles_configuration", "#importer")
					, _DIR_PLUGIN_AMOCLES_IMG_PACK."groupe-mots-24.png"
					, _DIR_PLUGIN_AMOCLES_IMG_PACK."fleches-jaune-gauche-24.png", false
					)
				, icone_horizontale(
					_T(_AMOCLES_LANG."exporter_mots_cles")
					, generer_url_ecrire("amocles_configuration", "#exporter")
					, _DIR_PLUGIN_AMOCLES_IMG_PACK."groupe-mots-24.png"
					, _DIR_PLUGIN_AMOCLES_IMG_PACK."fleches-jaune-droite-24.png", false
					)
				)
			)
			;

	$page_result .= ""
		. debut_droite($rubrique, true)
		;

	////////////////////////////////////
	// Boite edition des mots vides 
	$page_result .= ""
		. debut_cadre_trait_couleur(_DIR_PLUGIN_AMOCLES_IMG_PACK."stopwords-24.png", true, "", _T(_AMOCLES_LANG.'editer_stopwords'))
		. "<div style='text-align: $spip_lang_left;font-style: italic;' class='verdana2'>\n"
		. _T(_AMOCLES_LANG."info_stopwords")
		. "</div>\n"
		. "<div style='text-align: $spip_lang_left;' class='verdana2'>\n"
		. "<form id='form_stopwords' method='post' action=''>\n"
		. "<fieldset>\n"
		. "<legend>"._T(_AMOCLES_LANG."vos_mots_vides_")."</legend>\n"
		;
	
	$page_result .= "\n"
		.	"<div id='amocles-stopwords-menu' class='verdana2 amocles-lang-menu'>\n"
		;
	
	////////////////////////////////////
	// menu des langues 
	foreach($langues_array as $lang) {
		$class = "class='amocles-select-lang".(($lang == $GLOBALS['spip_lang']) ? " lang-sel" : "")."'";
		$page_result .= ""
			. "<a href='#form_stopwords' $class title='".$nom_langue[$lang]."' lang='$lang'>[$lang]</a>&nbsp;\n"
			;
	}

	$alt = _T(_AMOCLES_LANG."selectionnez_langue_stopwords");

	$page_result .= ""
		.	"<img src='"._DIR_IMG_PACK."langues-12.gif' alt=\"".$alt."\" title=\"".$alt."\" />\n"
		.	"</div>\n"
		;

	////////////////////////////////////
	// Les boites d'edition 
	foreach($langues_array as $lang)
	{
		$class = (($lang == $GLOBALS['spip_lang']) ? "block" : "none");
		$style = "display:".$class;
		$page_result .= ""
			. "<label title=\"$titre\" style='display:block;'>\n"
			. "<textarea name='input_stopwords[$lang]' class='forml $class' cols='50' rows='20' lang='$lang'"
				. " style='" . $style . "'" 
				. " title='" . _T(_AMOCLES_LANG."mots_vides_pour_", array('nom_lang' => $nom_langue[$lang], 'lang' => $lang))
				. "'>"
			. ((isset($stop_words[$lang]) && is_array($stop_words[$lang])) ? implode("\n", $stop_words[$lang]) : "")
			. "</textarea>\n"
			. "</label>\n"
			; 
	}
	$page_result .= ""
		. "</fieldset>\n"
		;
	
	////////////////////////////////////
	// Et le bouton valider
	$page_result .= ""
		. "<div style='text-align:$spip_lang_right;margin-top:0.5em;'>\n"
		. "<input type='submit' name='btn_valider_stopwords' value='"._T('bouton_valider')."' class='fondo' />\n"
		. "</div>\n"
		. "</form>\n"
		. "</div>\n"
		. fin_cadre_trait_couleur(true)
		;
	
	echo($page_result);
	echo amocles_html_signature(_AMOCLES_PREFIX), fin_gauche(), fin_page();

}

?>