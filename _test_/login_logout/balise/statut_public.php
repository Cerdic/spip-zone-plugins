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
	selon les termes de la Licence Publique Generale GNU publiée par 
	la Free Software Foundation (version 2 ou bien toute autre version ultérieure 
	choisie par vous).
	
	LiLo est distribué car potentiellement utile, mais SANS AUCUNE GARANTIE,
	ni explicite ni implicite, y compris les garanties de commercialisation ou
	d'adaptation dans un but spécifique. Reportez-vous à la Licence Publique Générale GNU 
	pour plus de détails. 
	
	Vous devez avoir reçu une copie de la Licence Publique Generale GNU 
	en meme temps que ce programme ; si ce n'est pas le cas, ecrivez à la  
	Free Software Foundation, Inc., 
	59 Temple Place, Suite 330, Boston, MA 02111-1307, États-Unis.
	
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
		
		// initialiser la config par défaut si besoin (installation non configurée)
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
			. (($config['lilo_statut_fixed']=='oui') ? 'fixed' : 'absolute') . ";"
			. (($config['lilo_statut_position'][0]=='t') ? "top" : "bottom") . ":-1px;"
			. (($config['lilo_statut_position'][1]=='l') ? "left" : "right") . ":-1px;"
			. "background-color:#".$config['lilo_statut_bgcolor'].";"
			;
		$auteur_session['statut_style'] = "style='$statut_style $transparent'";
		
		$lilo_boite_statut_inseree = true;
		
		return array('formulaires/statut_public'
			, 0 //$GLOBALS['delais']
			, $auteur_session
			);
	}
	return (false);
}

?>