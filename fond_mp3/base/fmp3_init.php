<?php 

// base/fmp3_init.php

// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

	/*****************************************************
	Copyright (C) 2008 Christian PAULUS
	cpaulus@quesaco.org - http://www.quesaco.org/
	/*****************************************************
	
	This file is part of Fmp3.
	
	Fmp3 is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.
	
	Fmp3 is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with Fmp3; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	
	/*****************************************************
	
	Ce fichier est un des composants de Fmp3. 
	
	Fmp3 est un programme libre, vous pouvez le redistribuer et/ou le modifier 
	selon les termes de la Licence Publique Generale GNU publiée par 
	la Free Software Foundation (version 2 ou bien toute autre version ulterieure 
	choisie par vous).
	
	Fmp3 est distribué car potentiellement utile, mais SANS AUCUNE GARANTIE,
	ni explicite ni implicite, y compris les garanties de commercialisation ou
	d'adaptation dans un but spécifique. Reportez-vous à la Licence Publique Générale GNU 
	pour plus de details. 
	
	Vous devez avoir reçu une copie de la Licence Publique Generale GNU 
	en même temps que ce programme ; si ce n'est pas le cas, ecrivez à la  
	Free Software Foundation, Inc., 
	59 Temple Place, Suite 330, Boston, MA 02111-1307, Etats-Unis.
	
	*****************************************************/
	
///////////////////////////////////////
// A chaque appel de exec/admin_plugin, si le plugin est active', 
// spip de'tecte fmp3_install() et l'appelle 3 fois :
// 1/ $action = 'test'
// 2/ $action = 'install'
// 3/ $action = 'test'
// 

include_spip('base/abstract_sql');
include_spip('inc/utils');
include_spip('inc/fmp3_api_globales');
include_spip('inc/fmp3_api_prive');

function fmp3_install ($action) {

	switch($action) {
		case 'test':
			// si renvoie true, c'est que la base est a` jour, inutile de re-installer
			// la valise plugin "effacer tout" apparait.
			// si renvoie false, SPIP revient avec $action = 'install' (une seule fois)
			$result = isset($GLOBALS['meta'][_FMP3_META_PREFERENCES]);
			fmp3_log("TEST meta:", $result);
			return($result);
			break;
		case 'install':
			if(!($result = isset($GLOBALS['meta'][_FMP3_META_PREFERENCES]))) {
				// cree les preferences par defaut
				$result = fmp3_set_all_preferences();
				fmp3_log("CREATE meta:" . _FMP3_META_PREFERENCES);
			}
			if(!$result) {
				fmp3_log("ERROR: PLEASE REINSTALL PLUGIN");
			}
			else {
				// invite de configuration si installation OK
				echo(_T('fmp3:fmp3_aide_install'
					, array('url_config' => generer_url_ecrire("fmp3_configure"))
					));
			}
			fmp3_log("INSTALL:", $result);
			
			return($result);
			break;
		case 'uninstall':
			// est appellé lorsque "Effacer tout" dans exec=admin_plugin
			$result = fmp3_vider_tables();
			fmp3_log("UNINSTALL:", $result);
			return($result);
			break;
		default:
			break;
	}
}


// effacer les metas (prefs, etc.)
function fmp3_vider_tables () {

	include_spip('inc/fmp3_api_globales');

	effacer_meta(_FMP3_META_PREFERENCES);
	fmp3_log("DELETE meta");
	
	// recharge les metas en cache 
	fmp3_ecrire_metas();
	
	return(true);
} 

?>