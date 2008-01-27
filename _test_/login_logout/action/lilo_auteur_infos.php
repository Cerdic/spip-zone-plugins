<?php 

	// action/lilo_auteur_infos.php
	
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

if (!defined("_ECRIRE_INC_VERSION")) return;

/*
	Ajax, renvoie les auteur_infos + logo auteur (si existe) au formulaire login
*/
function action_lilo_auteur_infos_dist () {

	$var_login = trim($_POST['var_login']);
	
	if(!empty($var_login)) {
	
		$logo_silouhette = find_in_path('images/lilo-silouhette-128.png');
		$logo_src = "";
		
		$sql_select = "id_auteur,alea_actuel,alea_futur";
		$sql_query = "SELECT $sql_select FROM spip_auteurs WHERE login='$var_login' LIMIT 1";
		$sql_result = spip_query($sql_query);
	
		if($row = spip_fetch_array($sql_result)) {
			$ok = true;
			foreach(split(',', $sql_select) as $key) {
				$$key = trim($row[$key]);
			}

			// lire la config du plugin
			//include_spip('inc/utils');
			include_spip('inc/plugin_globales_lib');
			$config = __plugin_lire_key_in_serialized_meta('config', _LILO_META_PREFERENCES);

			if(isset($config['lilo_login_voir_logo']) && ($config['lilo_login_voir_logo']=='oui')) {
				$chercher_logo = charger_fonction('chercher_logo', 'inc');
				list($logo_src) = $chercher_logo($id_auteur, 'id_auteur', 'on');
				if (!$logo_src || (empty($logo_src))) {
					$logo_src = $logo_silouhette;
				}
			}
		}
		else {
			// inconnu ? 
			foreach(split(',', $sql_select) as $key) {
				$$key = "";
			}
		}
		
		// preparer le resultat à renvoyer
		$result = "";
		$sql_select .= ",logo_src";
		foreach(split(',', $sql_select) as $key) {
			$result .= $$key . _LILO_AJAX_RESULT_SEPARATOR;
		}
		//spip_log("action result: $result", 'lilo');
		
		echo($result);
		return (true);
	}
	return (false);
}

?>