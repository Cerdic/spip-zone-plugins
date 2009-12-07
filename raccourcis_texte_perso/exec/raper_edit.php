<?php

// exec/raper_edit.php

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

/*
 * Formulaire d'édition des raccourcis
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/traduire');
include_spip('inc/raper_api_globales');
include_spip('inc/raper_api_prive');
include_spip('inc/raper_api_edit');

function exec_raper_edit_dist () {
	
	global $couleur_foncee, $spip_lang;

	$flag_editable = autoriser_raccourcis_gerer();
	
	if($flag_editable) {

		$prefs_modifiees = false;
		
		// retour de formulaire ?
		// modifier le raccourci demandé
		if (
			(_request('valider')) 
			&& ($id_raccourci = _request('id_raccourci'))
			&& ($value = _request('value'))
		) {
			// corriger le tag si multi présent
			$value = raper_multi_swap_entities ($value, false);
			raper_raccourci_modifier($id_raccourci, $value);
			$prefs_modifiees = true;
		}
		// supprimer un raccourci perso
		else if($ii = _request('drop')) {
			raper_raccourci_modifier($ii, null);
			$prefs_modifiees = true;
		}
		
		
		$raper_lang = raper_lang();
		
		$prefs = raper_lire_preferences($prefs_modifiees);

		$raccourcis_spip = raper_raccourcis_spip($raper_lang, $prefs);

		// surcharger par les prefs du raper
		$raccourcis_list = raper_raccourcis_fusionner($raccourcis_spip, $prefs);
		ksort($raccourcis_list);
		
		// retour de formulaire. Préparer le mini-edit
		if($id_edit = _request('edit')) {
			$formulaire_edit = raper_edit_form_mini_edit ($id_edit, $raccourcis_list, $prefs, $raper_lang);
		}
		
		$nb_spip = count($raccourcis_spip);
		$nb_raper = count($prefs['raccourcis']);
		$msg_info = raper_msg_info($nb_raper, $nb_spip);
	}

////////////////////////////////////
// PAGE CONTENU
////////////////////////////////////

	$titre_page = _T('raper:edition_des_raccourcis');
	$rubrique = _RAPER_PREFIX;
	$sous_rubrique = "raccourcis_edit";

	$commencer_page = charger_fonction('commencer_page', 'inc');
	echo($commencer_page(_T('raper:raper') . " - " . $titre_page, $rubrique, $sous_rubrique));

	if(!$flag_editable) {
		die (raper_terminer_page_non_autorisee() . fin_page());
	}
	
	$page_result = ""
		. "<br /><br /><br />\n"
		. raper_gros_titre($titre_page, '', true)
		. barre_onglets($rubrique, $sous_rubrique)
		. debut_gauche($rubrique, true)
		. raper_boite_info(_T('raper:raccourcis_perso'), $msg_info)
		. pipeline('affiche_gauche', array('args'=>array('exec'=>$sous_rubrique),'data'=>''))
		. creer_colonne_droite($rubrique, true)
		. raper_boite_raccourcis($rubrique)
		. pipeline('affiche_droite', array('args'=>array('exec'=>$sous_rubrique),'data'=>''))
		. debut_droite($rubrique, true)
		;

	$page_result .= ""
		// tableau des raccourcis
		. "<table class='raper-table'>\n"
		// entete
		. "<tr style='background-color: $couleur_foncee; color:white;'>\n"
			. "<th class='verdana2 strong'>"._T('module_raccourci')."</th>\n"
			. "<th class='verdana2 strong'>"._T('module_texte_affiche')."</th>\n"
			. "<th class='verdana2 strong actions'>".trim(_T('info_action', array('action' => '')), ": ")."</th>\n"
		. "</tr>\n"
		// corps
		// menu multilingue si besoin
		. "<tr><td colspan='3'>".raper_edit_menu_langues($raper_lang)."</td></tr>\n"
		;
		$ii = 0;
		foreach ($raccourcis_list as $raccourci => $value) {
			
			// alterner les couleurs des lignes
			$bgcolor = ((($ii++) % 2) ? 'w' : 'e');
			
			// si dans raper, proposer de supprimer (retour à l'original)
			$lien_drop = (($value['raper']) ? raper_edit_creer_lien('drop', $raccourci) : "");
			
			// si raccourci d'un local_* au skel, le signaler
			$sig_skel = (($value['skel']) ? "skel" : "");
			
			// edition du bloc demandé ? afficher le mini formulaire
			if ($id_edit == $raccourci) {
				$lien_edit = "";
				$value = $formulaire_edit;
			}
			// sinon, afficher le crayon
			else {
				$lien_edit = raper_edit_creer_lien('edit', $raccourci);
				$value = 
					($value['raper'])
					? raper_extraire_multi($value['value'], $raper_lang)
					: $value['value']
					;
			}

			$page_result .= ""
				. "<tr id='_r_" . $raccourci . "' class='bg-$bgcolor $sig_skel'>\n"
					. "<td class='verdana2 strong'>&lt;:$raccourci:&gt;</td>\n"
					. "<td class='arial2 value'>".$value."</td>"
					. "<td class='arial2 actions'>".$lien_edit." ".$lien_drop."</td>"
				. "</tr>\n"
				;
			if(!(($ii) % 10)) {
				$page_result .= "<tr><td colspan='3'>".raper_edit_menu_langues($raper_lang)."</td></tr>\n";
			}
		}
	$page_result .= ""
		. "</table>"
		. "<br />\n"
		;
	
	echo($page_result);

	echo pipeline('affiche_milieu',array('args'=>array('exec'=>$sous_rubrique),'data'=>''))
		, raper_html_signature()
		, fin_gauche(), fin_page();
	
} // end exec_raper_edit_dist()

/*
 * Menu langue en une seule ligne
 * @return string
 * @param $raper_lang string langue sélectionnée courante pour ne pas mettre un lien dessus
 */
function raper_edit_menu_langues ($raper_lang) {
	static $id_menu, $langues_array, $url, $nom_langue, $micro;

	// si multilingue, propose le menu eponyme
	if(raper_site_langues_compter() > 1) {
		if($id_menu === null) {
			$id_menu = 0;
			$alt = _T("raper:selectionnez_langue");
			$langues_array = explode(',', raper_langues_selection());
			sort($langues_array);
			$traduire = charger_fonction('traduire', 'inc');
			$url = generer_url_ecrire("raper_edit");
			foreach($langues_array as $langue) {
				$nom_langue[$langue] = "title='".ucwords(traduire_nom_langue($langue))."'";
			}
			$micro = "<img src='"._DIR_IMG_PACK."langues-12.gif' alt=\"".$alt."\" title=\"".$alt."\" />\n";
		}
		$ancre = "raper-menu-multi_$id_menu";
		$menu_langues = "\n"
			.	"<div id='$ancre' class='verdana2 raper-menu-multi'>\n"
			;
		foreach($langues_array as $langue) {
			$href = parametre_url($url, 'raper_lang', $langue);
			$class = "class='".(($langue == $GLOBALS['spip_lang']) ? "lang-sel" : "")."'";
			$menu_langues .= ""
				.	(
					($langue != $raper_lang)
						? "<a href='$href#$ancre' ".$nom_langue['langue']." lang='$langue'>[$langue]</a>&nbsp;\n"
						: "<span ".$nom_langue['langue']." lang='$langue'>[$langue]</span>&nbsp;\n"
					)
				;
		}
		$id_menu++;
		$menu_langues .= ""
			.	$micro
			.	"</div>\n"
			;
	}
	else $menu_langues = "";
	
	return($menu_langues);
}



