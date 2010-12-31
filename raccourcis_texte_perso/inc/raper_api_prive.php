<?php

// inc/raper_api_prive.php

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

/*
 * Obtenir la langue désirée pour l'affichage des raccourcis
 * @return string
 */
function raper_lang () {
	global $spip_lang;
	static $lang;
	if(!$lang) {
		if(!$lang = _request('raper_lang')) $lang = $spip_lang;
	}
	return($lang);
}


/*
 * Gros titre de la page en espace privé
 * @return string
 * @param $titre string
 * @param $ze_logo string[optional]
 */
function raper_gros_titre ($titre, $ze_logo='') {
	if(!raper_spip_est_inferieur_193()) {
		$ze_logo = ""; // semble ne plus etre utilise dans exec/*
	}
	$r = gros_titre($titre, $ze_logo, $aff);
	return($r);
}

/*
 * Message pour page non autorisée
 * @return string
 */
function raper_terminer_page_non_autorisee () {
	$result = "<p>"._T('avis_non_acces_page')."</p>";
	return($result);
}

/* numero de version du plugin connu de SPIP (meta)
 * @return string 
 */
function raper_meta_plugin_version () {
	if(isset($GLOBALS['meta']['plugin'])) {
		$result = unserialize($GLOBALS['meta']['plugin']);
		$prefix = strtoupper(_RAPER_PREFIX);
		if(isset($result[$prefix])) $version = $result[$prefix]['version'];
	}
	return($version);
}

/*
 * Petite boite info, colonne gauche
 * @return string
 * @param $titre string
 * @param $message string
 */
function raper_boite_info ($titre, $message) {
	$result = 
		  debut_cadre_relief(raper_icone_24(), true, '', $titre)
		. "<span id='msg-boite-info' class='arial2'>"
		. $message
		. "</span>\n"
		. fin_cadre_relief(true)
		. "<br />\n"
		;
	return($result);
}

/*
 * Boite des raccourcis pour la page de configuration
 * @return string
 * @param $rubrique string
 */
function raper_boite_raccourcis ($rubrique) {
	
	include_spip('inc/raper_api_journal');
	
	if($rubrique == 'configuration') {
		$result = ""
		. debut_cadre_enfonce('', true)
		. "<span class='verdana2' style='font-size:80%;text-transform: uppercase;font-weight:bold;'>" . _T('titre_cadre_raccourcis') . "</span>\n"
		. "<ul id='liste-raccourcis'>\n"
		. "<!-- Journal du plugin -->\n"
		. "<li>"
		. raper_raccourci_journal()
		. "</li>\n"
		. "<!-- aide en ligne -->\n"
		. "<li>"
		. icone_horizontale(
			_T('raper:aide_en_ligne')
			, generer_url_ecrire("raper_aide")
			, _DIR_RAPER_IMG_PACK."aide-24.png"
			, ""
			, false
			, " onclick=\"javascript:window.open(this.href,'raper_aide','scrollbars=yes,resizable=yes,width=740,height=580');return false;\" "
			)
		. "</li>\n"
		. "</ul>\n"
		. fin_cadre_enfonce(true)
		;
	}
	return($result);
}

/*
 * Donne chemin de l'icone pour le bouton et boite info
 * @return string
 */
function raper_icone_24 () {
	static $icone;
	if(!$icone) {
		$icone = 
			_DIR_IMG_PACK 
			. 	(
				(raper_site_langues_compter() > 1)
				? "traductions-24.gif"
				: "langues-24.gif"
				)
			;
	}
	return($icone);
}

/*
 * Petite signature pour bas de page RaPer et les exports
 * @return string
 * @param $html bool[optional] si true, renvoie la version HTML, sinon, textebrut
 */
function raper_html_signature ($html = true) {
	$nom = _T('raper:raper');
	$version = raper_meta_plugin_version();
	$revision = ($ii = raper_plugin_revision()) ? " [<span class='revision'>$ii</span>]" : "";
	if($html) {
		$nom = "<span class='titre'>" . $nom . "</span>\n";
		$version = (($version) ? " <span class='version'>" . $version . "</span>\n" : "");
		$result = "<p class='verdana1 spip_xx-small plug-sign'>" . $nom . $version . $revision . "</p>\n";
	}
	else {
		$result = $nom . " " . $version;
	}
	return($result);
}

/*
 * demande si autorisé à gérer les raccourcis perso
 * @return bool
 */
function autoriser_raccourcis_gerer () {
	
	$prefs = raper_lire_preferences();
	$autoriser_gerer = $prefs['autoriser_gerer'];

	switch($autoriser_gerer) {
		case _RAPER_AUTORISER_GERER_AUCUN:
			$result = $GLOBALS['connect_toutes_rubriques'];
			break;
		case _RAPER_AUTORISER_GERER_RESTREINTS:
			$result = ($GLOBALS['connect_statut'] == "0minirezo");
			break;
		default:
			if((strpos($autoriser_gerer, ",") !== false) || (intval($autoriser_gerer) == $autoriser_gerer)) {
				$result = in_array($GLOBALS['auteur_session']['id_auteur'], explode(",", $autoriser_gerer));
			}
	}
	return($result);
}

/*
 * Petite boite info sur le plugin pour la page configure.
 * @return string
 */
function raper_boite_plugin_info () {
	include_spip('inc/meta');
	include_spip('inc/plugin');

	// 20101231, correcion de doriaN
	// http://www.quesaco.org/Surcharger-les-raccourcis-texte#forum720
	if(version_compare($GLOBALS['spip_version_code'],'15375','>=')) {
		$get_infos = charger_fonction('get_infos','plugins');
		$info = $get_infos(_DIR_PLUGIN_RAPER);
	}
	else {
		$info = plugin_get_infos(_DIR_PLUGIN_RAPER);
	}

	$icon = 
		(isset($info['icon']))
		? "<div class='icone' "
			. " style='width:64px;height:64px;"
				. "margin:0 auto 1em;"
				. "background: url(". _DIR_PLUGIN_RAPER . trim($info['icon']).") no-repeat center center;overflow: hidden;'"
			. " title='Logotype plugin " . _RAPER_PREFIX . "'>"
			. "</div>\n"
		: ""
		;
	$result = "";
	foreach(array('version', 'etat', 'auteur', 'lien') as $key) {
		$propre = trim(propre($info[$key]));
		if(!raper_spip_est_inferieur_193()){
			// supprimer la balise enveloppe de SPIP 2
			$propre = preg_replace(';(^<p>(.*)<\/p>)$;s', '${2}', $propre);
		}
		if(isset($info[$key]) && !empty($info[$key])) {
			$result .= "<li>" . ucfirst($key) . ": " . $propre . "</li>\n";
		}
	}
	$result = ""
		. "<ul style='list-style-type:none;margin:0;padding:0 1ex' class='detailplugin verdana2'>\n"
		. $result
		. "</ul>\n"
		;
	// $result .= affiche_bloc_plugin(_DIR_PLUGIN_MAO, $info);
	if(!empty($result)) {
		$result = ""
			. debut_cadre_relief('plugin-24.gif', true, '', _T('raper:raper'))
			. "<p style='text-align:center;font-size:90%'>" . $info['nom'] . "</p>\n"
			. $icon
			. $result
			. fin_cadre_relief(true)
			;
	}
	return($result);
}

