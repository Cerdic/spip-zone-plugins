<?php 

// base/ImageFlow_init.php

	/*****************************************************
	Copyright (C) 2008 Christian PAULUS
	cpaulus@quesaco.org - http://www.quesaco.org/
	/*****************************************************
	
	This file is part of "Portfolio ImageFlow".
	
	"Portfolio ImageFlow" is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.
	
	"Portfolio ImageFlow" is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with "Portfolio ImageFlow"; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	
	/*****************************************************
	
	Ce fichier est un des composants de "Portfolio ImageFlow". 
	
	"Portfolio ImageFlow" est un programme libre, vous pouvez le redistribuer et/ou le modifier 
	selon les termes de la Licence Publique Generale GNU publiée par 
	la Free Software Foundation (version 2 ou bien toute autre version ultérieure 
	choisie par vous).
	
	"Portfolio ImageFlow" est distribué car potentiellement utile, mais SANS AUCUNE GARANTIE,
	ni explicite ni implicite, y compris les garanties de commercialisation ou
	d'adaptation dans un but spécifique. Reportez-vous à la Licence Publique Générale GNU 
	pour plus de détails. 
	
	Vous devez avoir reçu une copie de la Licence Publique Generale GNU 
	en meme temps que ce programme ; si ce n'est pas le cas, ecrivez à la  
	Free Software Foundation, Inc., 
	59 Temple Place, Suite 330, Boston, MA 02111-1307, États-Unis.
	
	*****************************************************/
	
// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

///////////////////////////////////////
// A chaque appel de exec/admin_plugin, si le plugin est active', 
// spip de'tecte ImageFlow_install() et l'appelle 3 fois :
// 1/ $action = 'test'
// 2/ $action = 'install'
// 3/ $action = 'test'
// 

include_spip('base/abstract_sql');
include_spip('inc/utils');
include_spip('inc/imageflow_api_globales');
include_spip('inc/imageflow_api_prive');

function imageflow_install ($action) {

	switch($action) {
		case 'test':
			// si renvoie true, c'est que la base est a` jour, inutile de re-installer
			// la valise plugin "effacer tout" apparait.
			// si renvoie false, SPIP revient avec $action = 'install' (une seule fois)
			$result = isset($GLOBALS['meta'][_IMAGEFLOW_META_PREFERENCES]);
			imageflow_log("TEST meta:", $result);
			return($result);
			break;
		case 'install':
			if(($config_error = imageflow_verifier_versions()) === false) {
				if(!($result = isset($GLOBALS['meta'][_IMAGEFLOW_META_PREFERENCES]))) {
					// cree les preferences par defaut
					$result = imageflow_set_all_preferences();
					imageflow_log("CREATE meta:" . _IMAGEFLOW_META_PREFERENCES);
				}
				if(!$result) {
					// nota: SPIP ne filtre pas le resultat. Si retour en erreur,
					// la case a cocher du plugin sera quand meme cochee
					imageflow_log("PLEASE REINSTALL PLUGIN");
				}
				else {
					// invite de configuration si installation OK
					echo(_T('imageflow:imageflow_aide_install'
						, array('url_config' => generer_url_ecrire("imageflow_configure"))
						));
				}
				imageflow_log("INSTALL:", $result);
			}
			else {
				echo( imageflow_boite_alerte(
					_T('imageflow:portfolio_imageflow')
					, "<strong>\n" . _T('forum_titre_erreur') . "</strong><br />"
						. _T('imageflow:'.$config_error)
					));
			}
			
			return($result);
			break;
		case 'uninstall':
			// est appellé lorsque "Effacer tout" dans exec=admin_plugin
			$result = imageflow_vider_tables();
			imageflow_log("UNINSTALL:", $result);
			return($result);
			break;
		default:
			break;
	}
}


// effacer les metas (prefs, etc.)
function imageflow_vider_tables () {

	effacer_meta(_IMAGEFLOW_META_PREFERENCES);
	imageflow_log("DELETE meta", $result);
	
	// recharge les metas en cache 
	imageflow_ecrire_metas();
	
	return(true);
} //

?>