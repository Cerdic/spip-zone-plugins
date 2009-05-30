<?php 

	// base/amocles_install.php
	
	// $LastChangedRevision$
	// $LastChangedBy$
	// $LastChangedDate$

	/*****************************************************
	Copyright (C) 2008 Christian PAULUS
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

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/amocles_api_globales');
include_spip('inc/amocles_api_prive');

function amocles_install ($action) {

	switch($action) {
		case 'test':
			// si renvoie true, c'est que la base est a jour, inutile de re-installer
			// la valise plugin "effacer tout" apparait.
			// si renvoie false, SPIP revient avec $action = 'install' (une seule fois)
			$result = isset($GLOBALS['meta'][_AMOCLES_META_PREFERENCES]);
			amocles_log("TEST meta", $result);
			return($result);
			break;
		case 'install':
			$result = amocles_init();
			amocles_log("INSTALL meta", $result);
			return($result);
			break;
		case 'uninstall':
			// si *_vider_tables() n'existe pas, passe par ici
			// sinon, appelle directement *_vider_tables()
			$result = amocles_vider_tables();
			amocles_log("DELETE meta", $result);
			return($result);
			break;
		default:
			break;
	}
	
	return(true);
}

/*
 * Installe les preferences par defaut du plugin dans la table 'spip_meta'
 */
function amocles_init () 
{
	$amocles_init = unserialize(_AMOCLES_PREFERENCES_DEFAULT);
	$amocles_init['version'] = amocles_real_version();
	$amocles_init['date'] = date('Y-m-d_H:i:s');
	amocles_log("VERSION " . $amocles_init['version']);
	ecrire_meta(_AMOCLES_META_PREFERENCES, serialize($amocles_init));
	return(amocles_ecrire_metas());
}

/*
 * Desinstalle le plugin en supprimant les metas de preferences
 * Est directement appelle' lorsque click sur "Effacer tout" dans exec=admin_plugin
 */
function amocles_vider_tables () 
{
	effacer_meta(_AMOCLES_META_PREFERENCES);
	amocles_log("DELETE meta");
	
	// recharge les metas en cache 
	return(amocles_ecrire_metas());
}

?>