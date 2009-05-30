<?php 

	// balise/statut_public.php
	
	// $LastChangedRevision$
	// $LastChangedBy$
	// $LastChangedDate$

	/*****************************************************
	Copyright (C) 2007 Christian PAULUS
	cpaulus@quesaco.org - http://www.quesaco.org/
	/*****************************************************
	
	This file is part of LiLo.
	
	LiLo is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.
	
	LiLo is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with LiLo; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	
	/*****************************************************
	
	Ce fichier est un des composants de LiLo. 
	
	LiLo est un programme libre, vous pouvez le redistribuer et/ou le modifier 
	selon les termes de la Licence Publique Generale GNU publie'e par 
	la Free Software Foundation (version 2 ou bien toute autre version ulterieure 
	choisie par vous).
	
	LiLo est distribue' car potentiellement utile, mais SANS AUCUNE GARANTIE,
	ni explicite ni implicite, y compris les garanties de commercialisation ou
	d'adaptation dans un but specifique. Reportez-vous a' la Licence Publique Generale GNU 
	pour plus de details. 
	
	Vous devez avoir recu une copie de la Licence Publique Generale GNU 
	en meme temps que ce programme ; si ce n'est pas le cas, ecrivez a la  
	Free Software Foundation, Inc., 
	59 Temple Place, Suite 330, Boston, MA 02111-1307, Etats-Unis.
	
	*****************************************************/
	
/*
	La balise STATUT_PUBLIC renvoie le statut/session et logo
*/
function balise_STATUT_PUBLIC ($p, $nom='STATUT_PUBLIC') {
	return calculer_balise_dynamique($p, $nom, array());
}

function balise_STATUT_PUBLIC_stat ($args, $filtres) {
	return array($filtres[0] ? $filtres[0] : $args[0], $args[1], $args[2]);
}

function balise_STATUT_PUBLIC_dyn () {
	
	static $lilo_boite_statut_inseree = false;

	if(isset($GLOBALS['auteur_session']) && !$lilo_boite_statut_inseree) {
		
		$auteur_session = $GLOBALS['auteur_session'];
		
		include_spip('inc/plugin_globales_lib');
		$config = __plugin_lire_key_in_serialized_meta('config', _LILO_META_PREFERENCES);
		
		$lilo_values_array = unserialize(_LILO_DEFAULT_VALUES_ARRAY);
		
		if($lilo_values_array) {
			// initialiser la config par d�faut si besoin (installation non configur�e)
			foreach($lilo_values_array as $key => $value) {
				$config[$key] = (isset($config[$key]) && !empty($config[$key])) ? $config[$key] : $lilo_values_array[$key];
			}
	
			if(isset($config['lilo_statut_voir_logo']) && ($config['lilo_statut_voir_logo']=='oui')) {
				$chercher_logo = charger_fonction('chercher_logo', 'inc');
				if (list($logo_auteur) = $chercher_logo($auteur_session['id_auteur'], 'id_auteur', 'on')) {
					$auteur_session['logo_auteur'] = $logo_auteur;
					$auteur_session['voir_logo_auteur'] = 'oui';
				}
			}
			$auteur_session['voir_boutons_admins'] = $config['lilo_statut_voir_boutons_admins'];
			$transparent = ($config['lilo_statut_transparent']=='oui')
				? "filter:alpha(opacity=75); -moz-opacity:0.75; opacity: 0.75;"
				: ""
				;
			
			$statut_style = ""
				. "position:"
				. (
					(($fixed = ($config['lilo_statut_fixed'] == 'oui'))
					&& !($is_ie6 = (($ii = lilo_browser_is_explorer()) && ($ii < 7 )) ? $ii : false)
					) ? 'fixed' : 'absolute') . " !important;"
				.	(
					($fixed && !($is_top = $config['lilo_statut_position'][0] == 't') && !$is_ie6) ? "bottom:" : "top:"
					)
					. lilo_css_value_get($fixed, $is_ie6, $is_top, "Top", "Height") . ";"
				.	(
					($fixed && !($is_left = $config['lilo_statut_position'][1] == 'l') && !$is_ie6) ? "right:" : "left:"
					)
					. lilo_css_value_get($fixed, $is_ie6, ($is_left), "Left", "Width") . ";"
				. "background-color:#".$config['lilo_statut_bgcolor'].";"
				;
			$auteur_session['statut_style'] = "$statut_style $transparent";
			
			$lilo_boite_statut_inseree = true;
			
			return array('formulaires/statut_public'
				, 0 //$GLOBALS['delais']
				, $auteur_session
				);
		}
		else {
			include_spip('inc/utils');
			spip_log("LILO: appel de la balise en cache sur plugin inactif.");
		}
	}
	return (false);
}

/*
	Valeur css pour top ou left
*/
function lilo_css_value_get ($fixed, $is_ie6, $origin, $t, $l) {
	if($fixed && $is_ie6) {
		$b = ($is_ie6 == 6) ? "document.documentElement" : "document.body";
		$result = 
			($origin)
			? "expression(Number(".$b.".scroll".$t.") + 'px')"
			: "expression(((ii = Number(".$b.".client".$l." - this.client".$l." + ".$b.".scroll".$t.")) < (".$b.".scroll".$l." - this.client".$l.") ? ii :  ".$b.".scroll".$l." - this.client".$l." - 4) + 'px')"
			;
	}
	else {
		$result = "-1px";
	}
	return($result);
}


/* 
	Retourne num�ro de version (pas sous-version) si IE ou false
*/
function lilo_browser_is_explorer () {
	$version = false;
	if(!strstr('Opera', $ii = $_SERVER['HTTP_USER_AGENT']) 
		&& preg_match('=MSIE ([0-9].[0-9]{1,2})=', $ii, $matches)
		&& ($version = intval($matches[1][0]))
		) {
		return($version);
	}
	return($version);
}

?>