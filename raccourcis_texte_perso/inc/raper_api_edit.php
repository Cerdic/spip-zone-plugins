<?php

// inc/raper_api_edit.php

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

include_spip('inc/filtres');

/*
 * Charger les raccourcis texte, dans toutes les langues utilise'es si besoin
 * @return array les raccourcis pour la langue demande'e
 * @param $lang string (ex. en)
 * @param $prefs array les pre'fe'rences du RaPer
 */
function raper_raccourcis_spip ($lang, $prefs) {
	static $raccourcis_spip;
	global $spip_lang;

	if(!$raccourcis_spip) $raccourcis_spip = array();

	// si les raccourcis pour la langue demande'es sont inconnus,
	// aller les chercher
	if(!$raccourcis_spip[$lang]) {

		$editer_quoi = array(
			'editer_public' => 'public' 
			, 'editer_spip' => 'spip'
			, 'editer_ecrire' => 'ecrire'
			);
		
		if(!$GLOBALS['idx_lang']) {
			$GLOBALS['idx_lang'] = $idx_lang_temp = 'idx_lang_temp';
		}

		$editer_tout = ($prefs['editer_tout'] == 'oui'); 

		$charger = false;
		foreach($editer_quoi as $key => $module) {
			if($editer_tout || ($prefs[$key] == 'oui')) {
				if(!$charger) {
					charger_langue($lang, $module);
					$charger = true;
				}
				else if ($f = chercher_module_lang($module, $lang)) {
					surcharger_langue($f);
				}

			}
		}
		if ($editer_tout || ($prefs['editer_local'] == "oui")) {
//raper_log("charger aussi les locales de skel dans raccourcis_spip");
			// raper_raccourcis_local_skel surcharge les raccourcis
			raper_raccourcis_local_skel();
		}
		
		// placer le tout en static pour la langue
		if(!$raccourcis_spip[$lang] = $GLOBALS[$GLOBALS['idx_lang']]) {
			$raccourcis_spip[$lang] = array();
			raper_log("Erreur raper_raccourcis_spip() sur lang = $lang module = $module");
		}
	
		// si passage temporaire (cas d'edition 'local' seul), nettoyer
		if($idx_lang_temp) unset($GLOBALS['idx_lang']);

	}
	return($raccourcis_spip[$lang]);
}

/*
 * Fusionner les tableaux des raccourcis en annotant ceux du raper 
 * @return array
 * @param $raccourcis_list array
 * @param $prefs array
 */
function raper_raccourcis_fusionner ($raccourcis_spip, $prefs) {	
	
	if(!($raccourcis_raper = $prefs['raccourcis'])) $raccourcis_raper = array();
	
	// demander le tableau des raccourcis locaux au squelette
	$raccourcis_local = raper_raccourcis_local_skel(false);
	if(count($raccourcis_local)) {
		//$raccourcis_raper = array_merge($raccourcis_local, $raccourcis_raper);
//raper_log("au final ". count($raccourcis_local));
	}
	
	$raccourcis_list = array_merge($raccourcis_spip, $raccourcis_raper);
	
	foreach($raccourcis_list as $key => $value) {
		$raper = isset($raccourcis_raper[$key]);
		$skel = isset($raccourcis_local[$key]);
		$raccourcis_list[$key] =
			array(
				'value' => $value
				// ajouter le drapeau pour placer les icones action (entre autre)
				, 'raper' => $raper
				// et le drapeau pour rappel que c'est du local au skel
				, 'skel' => $skel
			);
	}
	return($raccourcis_list);			
}


/*
 * Complement pour la boite info. Message
 * @return string
 * @param $nb_raper int
 * @param $nb_spip int
 */
function raper_msg_info ($nb_raper, $nb_spip) {
	$result = 
		($nb_raper)
		?	(
			($nb_raper > 1)
			? _T('raper:n_raccourcis_perso_sur_i', array('n' => $nb_raper, 'i' => $nb_spip))
			: _T('raper:un_raccourci_perso_sur_i', array('i' => $nb_spip))
			)
		: _T('raper:aucun_raccourci_perso_sur_i', array('i' => $nb_spip))
		;
	return($result);	
}

/*
 * Demander les raccourcis local_* du squelette
 * @return array
 * @param $surcharger bool[optional] true pour surcharger
 */
function raper_raccourcis_local_skel ($surcharger = true) {
	
	$langues_array = explode(",", raper_langues_selection());
	$is_multilingue = (count($langues_array) > 1);
	
	// Chemin standard depuis l'espace public
	$path = defined('_SPIP_PATH') ? _SPIP_PATH : _DIR_RACINE;
	// le dossier standard
	if (@is_dir(_DIR_RACINE.'squelettes')) {
		$path = _DIR_RACINE.'squelettes/:' . $path;
	}
	// Et le(s) dossier(s) des squelettes nommes
	if ($GLOBALS['dossier_squelettes']) {
		foreach (array_reverse(explode(':', $GLOBALS['dossier_squelettes'])) as $d) {
			$path = ($d[0] == '/' ? '' : _DIR_RACINE) . $d . '/:' . $path;
		}
	}
	// nettoyer les / du path
	$path_array = array();
	foreach (explode(':', $path) as $dir) {
		if (($dir = trim($dir)) && strlen($dir) && substr($dir,-1) != '/')
			$dir .= "/";
		$path_array[] = $dir;
	}
	
	if(!$surcharger) {
		// sauvegarder les langues
		$idx_lang_temp = $GLOBALS['idx_lang'].'_temporaire';
		if(isset($GLOBALS['idx_lang'])) $idx_lang_normal = $GLOBALS['idx_lang'];
		$GLOBALS['idx_lang'] = $idx_lang_temp;
	}
	
	$raccourcis_local = array();
	
	foreach($langues_array as $lang) {
		raper_surcharger_langue($lang, 'local', $path_array);
		if (count($GLOBALS[$GLOBALS['idx_lang']])) {
			foreach($GLOBALS[$GLOBALS['idx_lang']] as $key => $value) {
				if(!isset($raccourcis_local[$key]) && $is_multilingue) $raccourcis_local[$key] = array();
				if($is_multilingue) {
					$raccourcis_local[$key][$lang] = $value;
				}
				else $raccourcis_local[$key] = $value;
			}
		}
	}
//raper_log("en local skel ".count($raccourcis_local));
	// restituer les langues
	if(!$surcharger && $idx_lang_normal) $GLOBALS['idx_lang'] = $idx_lang_normal;
	unset ($GLOBALS[$idx_lang_temp]);
	
	return($raccourcis_local);
}

/*
 * Lien a' placer dans les petits boutons du tableau
 * @return string
 * @param $type string
 * @param $id string
 */
function raper_edit_creer_lien ($type, $id) {
	static $titles, $url;
	if(!$titles) {
		$titles = array(
			'edit' => _T('raper:perso_edit')
			, 'drop' => _T('raper:perso_drop')
			);
	}
	if(!$url) {
		$url = self();
	}
	$href = parametre_url($url, $type, $id);
	$lien = ""
		. "<a href='$href#_r_$id' $id_attr class='raper-$type' title=\"".$titles[$type]."\""
		. " onclick=\"javascript:return jQuery.raper_action('raper-$type', '$id')\""
		. ">"
		. "<span class='no-screen $type'>".$titles[$type]."</span></a>\n"
		;
	return($lien);
}

/*
 * Petit formulaire d'edition pour un raccourci
 * @return string
 * @param $id_raccourci string id du raccourci
 * @param $raccourcis_list array tableau des raccourcis actuels 
 * @param $raper_lang string langue choisie pour l'affichage du contenu des raccourcis
 */
function raper_edit_form_mini_edit ($id_raccourci, $raccourcis_list, $prefs, $raper_lang) {

	$nb_langues = substr_count(raper_langues_selection(), ',') + 1;
	
	if(isset($raccourcis_list[$id_raccourci])) {
		// si c'est un raccourci du raper
		// ou une seule langue, prendre la valeur
		if($raccourcis_list[$id_raccourci]['raper']
			|| ($nb_langues == 1)
		) {
			$value = $raccourcis_list[$id_raccourci]['value'];
			$value = raper_multi_swap_entities ($value, true);
		}
		else {
		// sinon, aller le rechercher dans tt les langues utilise'es par le site et cre'er le multi
			$value = "";
			$les_langues = explode(",", raper_langues_selection());
			sort($les_langues);
			foreach($les_langues as $langue) {
				$ii = raper_raccourcis_spip ($langue, $prefs);
				$value .= "[$langue]" . (isset($ii[$id_raccourci]) ? $ii[$id_raccourci] : "<vide>") . "\n";
			}
			// envelopper le tout du tag multi
			$value = "&lt;multi&gt;\n" . $value . "&lt;/multi&gt;";
		}
	}
	else $value = "Erreur sur ce raccourci (bug ou url fausse'e !)";
	
	$nameform = 'miniform';
	$url = parametre_url(generer_url_ecrire('raper_edit'), 'edit', '');
	$url = parametre_url($url, 'id_raccourci', '');
	$ancre = "_r_$id_raccourci";
	$result = "\n"
		. "<form action='" . $url . "#$ancre'"
		. " method='post' name='$nameform'>\n"
		. "<textarea name='value' cols='40' rows='" 
			. ($nb_langues + ((raper_site_langues_compter() > 1) ? 2 : 1)) // pour placer les 2 <multi>
			. "'>\n"
		. $value
		. "</textarea>\n"
		. "<table class='construct'><tr><td>\n"
		. raper_edit_miniform_button('raper-cancel', 'annuler', $id_raccourci, _T('raper:annuler'), $nameform, $raper_lang)
		. "</td><td class='right'>\n"
		. raper_edit_miniform_button('raper-apply', 'valider', $id_raccourci, _T('raper:valider'), $nameform, $raper_lang)
		. "</td></tr></table>\n"
		. "</form>\n"
		;
	return($result);
}

/*
 * Le petit bouton submit pour le formulaire
 * @return 
 * @param $id_button string
 * @param $name string
 * @param $id_raccourci string
 * @param $title string
 * @param $nameform string
 */
function raper_edit_miniform_button ($id_button, $name, $id_raccourci, $title, $nameform, $raper_lang) {
	$result = ""
		. "<div>\n"
		. "<input type='submit' name='$name'"
			. " class='$id_button pointer'"
			. " title=\"".$title."\" value=\"".$title."\""
			. " onclick=\"javascript:return jQuery.raper_action('$id_button', '$id_raccourci')\""
			. " />\n"
		. "<input type='hidden' name='id_raccourci' value='$id_raccourci' />\n"
		. "</div>\n"
		;
	return($result);
}

/*
 * Retourne la version HTML ou TEXTE de "<multi>"
 * nota: pour XHTML-stric, "<multi>" doit être avec entite's HTML
 * @return 
 * @param $value string
 * @param $to_entities bool[optional]
 */
function raper_multi_swap_entities ($value, $to_entities = true) {

	if(!empty($value)) {
		$patterns = array('|&lt;multi&gt;|', '|&lt;/multi&gt;|');
		$replacements = array('<multi>', '</multi>');	
		if ($to_entities && preg_match("|<multi>|", $value)) {
			$patterns = array('|<multi>|', '|</multi>|');
			$replacements = array('&lt;multi&gt;', '&lt;/multi&gt;');
			
		}
		else if (preg_match("|&lt;multi&gt;|", $value)) {
			//$patterns = array('|&lt;multi&gt;|', '|&lt;/multi&gt;|');
			//$replacements = array('<multi>', '</multi>');
		}
		$value = preg_replace($patterns, $replacements, $value);
	}
	else if(raper_site_langues_compter() > 1) {
		if(($langues_array = explode(',', raper_langues_selection())) && (count($langues_array) > 1)) {
			sort($langues_array);
			$value = "";
			foreach($langues_array as $langue) {
				$value .= "[$langue]\n";
			}
			$lt = $to_entities ? "&lt;" : "<";
			$gt = $to_entities ? "&gt;" : ">";
			$value = $lt . "multi" . $gt . "\n" . $value . $lt . "/multi" . $gt . "\n";
		}
	}

	return($value);
}

/*
 * Extraire le multi dans la langue de'sire'e
 * @return string
 * @param $value string
 * @param $raper_lang string
 */
function raper_extraire_multi ($value, $raper_lang) {
	global $spip_lang;
	$tmp_lang = false;
	
	if($spip_lang != $raper_lang) {
		$tmp_lang = $spip_lang;
		$spip_lang = $raper_lang;
	}
	
	$value = extraire_multi($value);
	
	if($tmp_lang) $spip_lang = $tmp_lang;
	
	return($value);
}

