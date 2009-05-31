<?php 

// action/raper_ajax.php

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
include_spip('inc/raper_api_prive');
include_spip('inc/raper_api_edit');

function action_raper_ajax () {

//raper_log("appel ajax");

	// demander les globales pour forcer identifier id_auteur, ... 
	$auth = charger_fonction('auth', 'inc');
	$auth = $auth();
	
	if(autoriser_raccourcis_gerer()) {
		
		include_spip('inc/traduire');
		
		$raper_lang = _request('raper_lang');
		$raper_do = _request('raper_do');
		$raper_id = _request('raper_id');
		$raper_value = _request('raper_value');
		
		$prefs = raper_lire_preferences();
		
		// recharger la version originale du raccourci
		$raccourcis_spip = raper_raccourcis_spip($raper_lang, $prefs);
		
		switch($raper_do) {
			case 'apply':
				if(!$prefs['raccourcis']) $prefs['raccourcis'] = array();
				$raper_value = raper_multi_swap_entities ($raper_value, false);
				raper_raccourci_modifier($raper_id, $raper_value);
				$value = raper_extraire_multi($raper_value, $raper_lang);
				echo($value);
				break;
			case 'cancel':
			// surcharger par les prefs du raper
				$raccourcis_list = raper_raccourcis_fusionner($raccourcis_spip, $prefs);
				$value = $raccourcis_list[$raper_id];
				$value = 
					($value['raper'])
					? raper_extraire_multi($value['value'], $raper_lang)
					: $value['value']
					;
				echo($value);
				break;
			case 'edit':
			// surcharger par les prefs du raper
				$raccourcis_list = raper_raccourcis_fusionner($raccourcis_spip, $prefs);
				$formulaire_edit = raper_edit_form_mini_edit ($raper_id, $raccourcis_list, $prefs, $raper_lang);
				echo($formulaire_edit);
				break;
			case 'drop':
			// supprimer le raccourci dans les prefs du raper
				raper_raccourci_supprimer ($raper_id);
				// renvoyer l'original
				echo($raccourcis_spip[$raper_id]);
				break;
			case 'info':
				// surcharger par les prefs du raper
				if(!($raccourcis_raper = $prefs['raccourcis'])) $raccourcis_raper = array();
				$raccourcis_list = array_merge($raccourcis_spip, $raccourcis_raper);
				$nb_spip = count($raccourcis_spip);
				$nb_raper = count($raccourcis_raper);
				$msg_info = raper_msg_info($nb_raper, $nb_spip);
				echo($msg_info);
				break;
		}

	}
	else {
		raper_log("modification from " . $GLOBALS['REMOTE_ADDR'] . " not allowed");
	}
}

?>