<?php 

	// inc/amocles_api_prive.php
	
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
	
include_spip('inc/plugin');
include_spip('inc/presentation');
include_spip('inc/texte');

// fonctions utilises en espace privé
// pour presentation


/*
 * @return Petite boite info du plug-in 
 */
function amocles_boite_plugin_info () {
	include_spip('inc/meta');
	$result = false;

	$plug_infos = amocles_get_plugin_meta_infos(_AMOCLES_PREFIX); // dir et version
	$info = plugin_get_infos($plug_infos['dir']);
	$icon = 
		(isset($info['icon']))
		? "<div "
			. " style='width:64px;height:64px;"
				. "margin:0 auto 1em;"
				. "background: url(". _DIR_PLUGINS.$plug_infos['dir']."/".trim($info['icon']).") no-repeat center center;overflow: hidden;'"
			. " title='Logotype plugin "._AMOCLES_PREFIX."'>"
			. "</div>\n"
		: ""
		;
	$result = "";
	foreach(array('version', 'etat', 'auteur', 'lien') as $key) {
		if(isset($info[$key]) && !empty($info[$key])) {
			$result .= "<li>" . ucfirst($key) . ": " . propre($info[$key]) . "</li>\n";
		}
	}
	$result = ""
		. "<ul style='list-style-type:none;margin:0;padding:0 1ex' class='detailplugin verdana2'>\n"
		. $result
		. "</ul>\n"
		;

	if(!empty($result)) {
		$result = ""
			. debut_cadre_relief('plugin-24.gif', true, '', $info['nom'])
			. $icon
			. $result
			. fin_cadre_relief(true)
			;
	}

	return($result);
}

/*
 * @param $prefix Prefix du plugin
 * @return Numero de version present dans plugin.xml
 */
function amocles_real_version ($prefix = _AMOCLES_PREFIX) 
{
	if(isset($GLOBALS['meta']['plugin'])) 
	{
		$dir = amocles_get_plugin_meta_dir($prefix);

		if ($dir) 
		{
			$f = _DIR_PLUGINS.$dir."/"._FILE_PLUGIN_CONFIG;

			if(is_readable($f) && ($c = file_get_contents($f))) 
			{
				$p = array("/<!--(.*?)-->/is","/<\/version>.*/s","/.*<version>/s");
				$r = array("","","");
				$r = trim(preg_replace($p, $r, $c));
			}
			return(!empty($r) ? $r : false);
		}
	}
	return (false);
}

/*
 * @return Petite signature en bas de page de config
 */
function amocles_html_signature ($prefix, $html = true) 
{
	$info = plugin_get_infos(amocles_get_plugin_meta_dir($prefix));
	$nom = typo($info['nom']);
	$version = typo($info['version']);
	$version = 
		($version && $html) 
		? " <span style='color:gray;'>" . $version . "</span>"
		: $version
		;
	$result = ""
		. $nom
		. " " . $version
		;
	if($html) {
		$result = "<p class='verdana1 spip_xx-small' style='font-weight:bold;'>$result</p>\n";
	}
	return($result);
}

/*
 * @return Titre du groupe de mots
 * @param $id_groupe ID du groupe de mots
 */
function amocles_titre_groupe_get ($id_groupe) {
	$row = spip_fetch_array(spip_query("SELECT titre FROM spip_groupes_mots WHERE id_groupe=$id_groupe LIMIT 1"));
	return($row['titre']);
}

/*
 * @return TRUE ou FALSE
 */
function amocles_mots_edit_inserer_milieu () {
	static $inserer_milieu = false;
	if(!$inserer_milieu) {
		$config = amocles_get_all_preferences();
		$inserer_milieu = isset($config['inserer_milieu']) ? $config['inserer_milieu'] : "non";
	}
	return ($inserer_milieu == "oui");
}

/*
 * Petit bloc de raccourcis dans colonne droite
 * @return Le bloc HTML complet à afficher
 * @param $bloc Le bloc à insérer
 */
function amocles_bloc_raccourcis ($titre, $array_bloc) {
	global $spip_display;

	$result = "";
	$titre = ucfirst(strtolower($titre));
	
	$array_bloc = (is_string($array_bloc) ? array(trim($array_bloc)) : $array_bloc);
	
	if(count($array_bloc) > 0)
	{
		if($spip_display != 4)
		{
			$result = implode("</div><div class='element_raccourci'>", $array_bloc);
			$result = "\n"
				. "<div class='verdana1 titre_raccourcis'>" . $titre
				. "<div class='element_raccourci'>" . $result . "</div>"
				. "</div>"
				;
			
		}
		else 
		{
			foreach($array_bloc as $v) {
				$result .= "<li class='element_raccourci'>" . $v . "</li>\n";
			}
			$result = "\n"
				. "<h3 class='titre_raccourcis'>" . $titre . "</h3><ul>"
				. $result
				. "</ul>"
				;
		}
		
		$result =
			"\n"
			. debut_cadre_enfonce('', true)
			. $result
			. "\n"
			. fin_cadre_enfonce(true)
			;
	}
	
	return ($result);
}

function amocles_boite_alerte ($message) {
	$result = ""
		. debut_boite_alerte()
		.  http_img_pack("warning.gif"
			, _T('info_avertissement')
			, "class='image-alerte'")
		. "<span class='message-alerte'>$message</span>\n"
		. fin_boite_alerte()
		. "\n<br />"
		;
	return ($result);
}

?>