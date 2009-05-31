<?php 

	// inc/barrac_api_icones.php
	
	// $LastChangedRevision$
	// $LastChangedBy$
	// $LastChangedDate$

	/*****************************************************
	Copyright (C) 2008 Christian PAULUS
	cpaulus@quesaco.org - http://www.quesaco.org/
	/*****************************************************
	
	This file is part of BarrAc.
	
	BarrAc is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.
	
	BarrAc is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with BarrAc; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	
	/*****************************************************
	
	Ce fichier est un des composants de BarrAc. 
	
	BarrAc est un programme libre, vous pouvez le redistribuer et/ou le modifier 
	selon les termes de la Licence Publique Generale GNU publiée par 
	la Free Software Foundation (version 2 ou bien toute autre version ultérieure 
	choisie par vous).
	
	BarrAc est distribué car potentiellement utile, mais SANS AUCUNE GARANTIE,
	ni explicite ni implicite, y compris les garanties de commercialisation ou
	d'adaptation dans un but spécifique. Reportez-vous à la Licence Publique Générale GNU 
	pour plus de détails. 
	
	Vous devez avoir reçu une copie de la Licence Publique Generale GNU 
	en meme temps que ce programme ; si ce n'est pas le cas, ecrivez à la  
	Free Software Foundation, Inc., 
	59 Temple Place, Suite 330, Boston, MA 02111-1307, États-Unis.
	
	*****************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/filtres_images');
include_spip('inc/filtres');
include_spip("inc/plugin_globales_lib");

/* barrac_icones_liste () 
Renvoie une liste ul/li des boutons en prenant en compte l'URI (si pas de javascript sur le poste)
*/
function barrac_icones_liste ($avec_freres = true, $avec_uri = false) {
	static $barrac_boutons_relations = false;
	static $barrac_tous_boutons_array = false;
	
	if(!$barrac_boutons_relations) $barrac_boutons_relations = unserialize(_BARRAC_BOUTONS_RELATIONS);
	if(!$barrac_tous_boutons_array) $barrac_tous_boutons_array = barrac_icones_array(true);
	
	$result = "";
	
	foreach(barrac_icones_array($avec_freres) as $key=>$value) {
	
		if ($avec_uri && ($key != _BARRAC_ACTION_POINTER)) {
		// pour acces sans javascript, force les urls
		
			if(($ii = _request($key)) && ($ii == 'oui')) {
				if($ii == 'oui') {
					$href = parametre_url(self(), $key, '');
					if(array_key_exists($key, $barrac_boutons_relations)) {
						$icone = $barrac_tous_boutons_array[$barrac_boutons_relations[$key]]['icone'];
					}
				}
				else {
				}
			}
			else {
				$href = parametre_url(self(), $key, 'oui');
				$icone = $value['icone'];
			}
		}
		else {
			$href = $value['href'];
			$icone = $value['icone'];
		}
		$result .= "<li class='item' style='display:".$value['display'].";' id='"._BARRAC_PREFIX."_item_".$key."'>" 
			. barrac_icone_link ($key, $icone, $value['titre'], $href) 
			. "</li>";
	}
	return("<ul id='barrac_boutons' class='barrac-boutons'>" . $result . "</ul>\n");
}

/* barrac_icone_link () */
function barrac_icone_link ($nom, $icone, $titre, $href = "", $return = true) {
	$style = "background-image: url($icone);";
	if($nom == _BARRAC_ACTION_POINTER) {
		$config = __plugin_lire_key_in_serialized_meta('config', _BARRAC_META_PREFERENCES);
		$href = (
			(isset($config['barrac_pointeur_ancre']) && !empty($config['barrac_pointeur_ancre']))
			? $config['barrac_pointeur_ancre']
			: _BARRAC_POINTER_DEFAULT
			);
	}
	$result = ""
			. "<a href='$href' style='$style' title=\"$titre\" id='"._BARRAC_PREFIX."_".$nom."'></a>" 
	;
	if($return) return($result);
	echo($result);
}

/** barrac_icones_array () 
Renvoie le tableau des boutons nécessaires
*/
function barrac_icones_array ($avec_freres = true, $forcer_taille = false) {
	static $barrac_boutons_legendes = false;
	
	if(!$barrac_boutons_legendes) $barrac_boutons_legendes = unserialize(_BARRAC_BOUTONS_LEGENDES);

	// Récupère la configuration
	$config = __plugin_lire_key_in_serialized_meta('config', _BARRAC_META_PREFERENCES);
	$taille = $config['barrac_taille_bouton'];
	$position = $config['barrac_position_barre'];
	$presentation = $config['barrac_presentation_barre'];
	$famille = $config['barrac_famille_boutons'];
	$flip_horizontal = $config['barrac_flip_horizontal'];
	$flip_vertical = $config['barrac_flip_vertical'];
	$flip_contextuel = $config['barrac_flip_contextuel'];
	$barrac_inverser_cssfile = $config['barrac_inverser_cssfile'];
	
	$_icones_array_tmp = array();

	if($avec_freres) {
		// prépare la fratrie
		$barrac_boutons_relations = unserialize(_BARRAC_BOUTONS_RELATIONS);
	}
	
	foreach(unserialize(_BARRAC_BOUTONS_PARENTS) as $key) {

		$$key = (isset($config[$key]) ? $config[$key] : 'non');

		if(
			// en espace public, n'active que ce qui est demandé dans la configuration
			!_DIR_RESTREINT 
			|| ((_DIR_RACINE == "") && ($$key == "oui"))
		) {

			// seul IE connait filter:Invert(). 
			// Ne pas afficher le bouton pour les autres navigateurs sauf si fichier CSS présent.
			if((_DIR_RACINE == "") // sauf pour l'espace privé
				&& ($key == _BARRAC_ACTION_INVERSER) && !barrac_browser_is_explorer() && empty($barrac_inverser_cssfile)) {
			}

			$_icones_array_tmp[$key] = array(
				'titre' => _T(_BARRAC_LANG . $barrac_boutons_legendes[$key])
				, 'icone' => _DIR_PLUGIN_BARRAC_IMG_PACK.$famille."-".$key."-"._BARRAC_ICONE_TAILLE_MAX.".png"
				, 'href' => "#"
				, 'display' => 'block'
			);
			if(
				// Recopier les frères si demandé
				$avec_freres 
				&& array_key_exists($key, $barrac_boutons_relations)
			) {
				$frere = $barrac_boutons_relations[$key];
				$_icones_array_tmp[$frere] = $_icones_array_tmp[$key];
				$_icones_array_tmp[$frere]['icone'] = _DIR_PLUGIN_BARRAC_IMG_PACK.$famille."-".$frere."-"._BARRAC_ICONE_TAILLE_MAX.".png";
			}
		}
	}
	
	$_icones_array = $_icones_array_tmp;

	if($avec_freres) {
		// Masque les boutons frères (activés/désactivés en JS)
		foreach(unserialize(_BARRAC_BOUTONS_FRERES) as $key) {
			if(isset($_icones_array[$key])) {
				$_icones_array[$key]['display'] = 'none';
			}
		}
	}

	// La taille est forcée en espace privé (appel page config...)
	if($forcer_taille > 0) {
		$taille = $forcer_taille;
	}

	// les icones à la bonne taille
	if($taille != _BARRAC_ICONE_TAILLE_MAX) {
		foreach($_icones_array as $key => $value) {
			$_icones_array[$key]['icone'] = extraire_attribut(image_reduire($_icones_array[$key]['icone'], $taille, $taille), 'src');
		}		
	}
	
	// Le pointeur dans la bonne position
	if(
		(!$flip_pointer || ($flip_pointer == 'oui'))
		&& isset($_icones_array[_BARRAC_ACTION_POINTER])) {
		$ii = unserialize(_BARRAC_POSITIONS_ARRAY);
		$ii = $ii[$position];
		$_icones_array[_BARRAC_ACTION_POINTER]['icone'] = extraire_attribut(image_rotation($_icones_array['pointer']['icone'], $ii), 'src');
		$_icones_array[_BARRAC_ACTION_POINTER]['href'] = $pointeur_ancre;
	}

	return($_icones_array);
}

/* barrac_icone_fond () */
function barrac_icone_fond ($forcer_taille = false) {
	static $icone_fond = false;
	if(!$icone_fond) {
		// Récupère la configuration
		$config = __plugin_lire_key_in_serialized_meta('config', _BARRAC_META_PREFERENCES);
		$taille = $config['barrac_taille_bouton'];
		$position = $config['barrac_position_barre'];
		$presentation = $config['barrac_presentation_barre'];
		$famille = $config['barrac_famille_boutons'];
		$flip_horizontal = $config['barrac_flip_horizontal'];
		$flip_vertical = $config['barrac_flip_vertical'];
		$flip_contextuel = $config['barrac_flip_contextuel'];

		// La taille est forcée pour l'espace privé (appel page config...)
		if($forcer_taille) {
			$taille = $forcer_taille;
		}

		$icone_fond = _DIR_PLUGIN_BARRAC_IMG_PACK.$famille."-fond-"._BARRAC_ICONE_TAILLE_MAX.".png";
		// le fond à la bonne taille
		if($taille != _BARRAC_ICONE_TAILLE_MAX) {
			$icone_fond = extraire_attribut(image_reduire($icone_fond, $taille, $taille), 'src');
		}
		
		// rotation et flip
		if(($flip_contextuel == 'oui') && ($presentation == 'vertical')) {
				$icone_fond = extraire_attribut(image_rotation($icone_fond, 90), 'src');
		}
		if(($flip_horizontal == 'oui') && preg_match(',^bottom,', $position)) {
			$icone_fond = extraire_attribut(image_flip_horizontal($icone_fond), 'src');
		}		
		if(($flip_vertical == 'oui') && preg_match(',left$,', $position)) {
			$icone_fond = extraire_attribut(image_flip_vertical($icone_fond), 'src');
		}
	}
	return($icone_fond);
}

/*
	barrac_barre_largest_side_size ()
	Donne la largeur ou la hauteur (la plus grande des deux)
*/
function barrac_barre_largest_side_size ($config, $nb_boutons) {
	return (
		(($config['barrac_taille_bouton'] + $config['barrac_marge_entre_boutons']) * $nb_boutons) 
		+ $config['barrac_marge_entre_boutons']
	);
}

/*
	barrac_boutons_actifs_count ()
	Nombre de boutons actifs dans la barre
*/
function barrac_boutons_actifs_count ($config) {
	$ii = 0;
	foreach(unserialize(_BARRAC_BOUTONS_PARENTS) as $key) {
		if($config[$key] == "oui") {
			$ii++;
		}
	}
	return($ii);
}

/* barrac_browser_is_explorer () 
	Retourne numéro de version IE ou false
*/
function barrac_browser_is_explorer () {
	$version = false;
	if(!strstr('Opera', $ii = $_SERVER['HTTP_USER_AGENT']) 
		&& preg_match('=MSIE ([0-9].[0-9]{1,2})=', $ii, $matches)
		&& ($version = intval($matches[1][0]))
		) {
		return($version);
	}
	return($version);
}


/*
	barrac_ie_6_5_fixed_position ()
	Correction pour IE 6|5.5 qui ne connait pas position:fixed
	Retourne le code CSS ou false si pas IE 6|5.5
	$is_top = true si top:0, false si bottom:0
	$is_left = true si left:0,false si right:0
	$width = largeur de la boite
	$height = hauteur de la boite
	$offset = décalage du bord en pixels
*/
function barrac_ie_6_5_fixed_position ($is_top, $is_left, $width, $height, $offset = 0) {
	if(!strstr('Opera', $ii = $_SERVER['HTTP_USER_AGENT']) 
		&& preg_match('=MSIE ([0-9].[0-9]{1,2})=', $ii, $matches)
		&& (($version = intval($matches[1][0])) < 7)
		) {
		$body = "document." . (($version >= 6) ? "documentElement" : "body");
		// lorsque la page est chargée, il vaut mieux activer la ligne suivante.
		// pourquoi ? A creuser.
		$body = "document.body";
		
		$bug = (($version >= 6) ? 4 : 0);
		$clientHeight = $body.".clientHeight";
		$clientWidth = $body.".clientWidth";
		$scrollTop = $body.".scrollTop";
		$scrollLeft = $body.".scrollLeft";
		$left_expression = ($is_left ? "" : $clientWidth."-".$width."-".$offset);
		$left_expression .= "+$scrollLeft";
		$left_expression = "(((ii = Number($left_expression))<$clientWidth) ? ii : $clientWidth)";
		return(
			"position:absolute;\n"
			. "top:expression(Number(" . ($is_top ? "" : $clientHeight  ."-$height-$offset") . "+". $scrollTop . ")+'px');\n"
			. "left:expression( " .$left_expression . " + 'px' );\n"
			. "width:".($width+$bug)."px !important;\n"
			. "height:".$height."px !important;\n"
		);
	}
	return(false);
}
//
?>