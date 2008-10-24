<?php 

	// base/menudep_install.php
	
	// $LastChangedRevision$
	// $LastChangedBy$
	// $LastChangedDate$

	/*****************************************************
	Copyright (C) 2007 Christian PAULUS
	cpaulus@quesaco.org - http://www.quesaco.org/
	/*****************************************************
	
	This file is part of Menudep.
	
	Menudep is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.
	
	Menudep is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with Menudep; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	
	/*****************************************************
	
	Ce fichier est un des composants de Menudep. 
	
	Menudep est un programme libre, vous pouvez le redistribuer et/ou le modifier 
	selon les termes de la Licence Publique Generale GNU publie'e par 
	la Free Software Foundation (version 2 ou bien toute autre version ulterieure 
	choisie par vous).
	
	Menudep est distribue car potentiellement utile, mais SANS AUCUNE GARANTIE,
	ni explicite ni implicite, y compris les garanties de commercialisation ou
	d'adaptation dans un but specifique. Reportez-vous a' la Licence Publique Generale GNU 
	pour plus de details. 
	
	Vous devez avoir recu une copie de la Licence Publique Generale GNU 
	en meme temps que ce programme ; si ce n'est pas le cas, ecrivez a' la  
	Free Software Foundation, Inc., 
	59 Temple Place, Suite 330, Boston, MA 02111-1307, Etats-Unis.
	
	*****************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip("inc/plugin_globales_lib");

function menudep_install ($action) {

	switch($action) {
		case 'test':
			// si renvoie true, c'est que la base est a' jour, inutile de re-installer
			// la valise plugin "effacer tout" apparait.
			// si renvoie false, SPIP revient avec $action = 'install' (une seule fois)
			$result = intval(isset($GLOBALS['meta'][_MENUDEP_META_PREFERENCES]));
			return($result);
			break;
		case 'install':
			return(menudep_init());
			break;
		case 'uninstall':
			// est appelle lorsque "Effacer tout" dans exec=admin_plugin
			return(menudep_vider_tables());
			break;
		default:
			break;
	}
	
	return(true);
}

function menudep_init () {
	$menudep_init = array(
		'prefix' => _MENUDEP_PREFIX
		, 'version' => __plugin_real_tag_get(_MENUDEP_PREFIX, 'version')
		, 'date_install' => date('Y-m-d_H:i:s')
		, 'config' => array() // configuration completee par exec_config
	);
	ecrire_meta(_MENUDEP_META_PREFERENCES, serialize($menudep_init));
	return(__ecrire_metas());
}

function menudep_vider_tables () {

	effacer_meta(_MENUDEP_META_PREFERENCES);
	return(__ecrire_metas());
}

?>