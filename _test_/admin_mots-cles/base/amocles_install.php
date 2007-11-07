<?php 

	// base/amocles_install.php
	
	// $LastChangedRevision$
	// $LastChangedBy$
	// $LastChangedDate$

	/*****************************************************
	Copyright (C) 2007 Christian PAULUS
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
	selon les termes de la Licence Publique Generale GNU publiée par 
	la Free Software Foundation (version 2 ou bien toute autre version ultérieure 
	choisie par vous).
	
	Amocles est distribué car potentiellement utile, mais SANS AUCUNE GARANTIE,
	ni explicite ni implicite, y compris les garanties de commercialisation ou
	d'adaptation dans un but spécifique. Reportez-vous à la Licence Publique Générale GNU 
	pour plus de détails. 
	
	Vous devez avoir reçu une copie de la Licence Publique Generale GNU 
	en meme temps que ce programme ; si ce n'est pas le cas, ecrivez à la  
	Free Software Foundation, Inc., 
	59 Temple Place, Suite 330, Boston, MA 02111-1307, États-Unis.

	*****************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/amocles_api');

function amocles_install ($action) {
spip_log("amocles_install() --", _AMOCLES_PREFIX);

	switch($action) {
		case 'test':
			// si renvoie true, c'est que la base est à jour, inutile de re-installer
			// la valise plugin "effacer tout" apparaît.
			// si renvoie false, SPIP revient avec $action = 'install' (une seule fois)
			return(isset($GLOBALS['meta'][_AMOCLES_META_PREFERENCES]));
			break;
		case 'install':
			return(amocles_init());
			break;
		case 'uninstall':
			// est appellé lorsque "Effacer tout" dans exec=admin_plugin
			return(amocles_vider_tables());
			break;
		default:
			break;
	}
	
	return(true);
}

function amocles_init () {
	$amocles_init = array(
		'prefix' => _AMOCLES_PREFIX
		, 'version' => __plugin_real_tag_get('version')
		, 'date' => date('Y-m-d_H:i:s')
		, 'admins_groupes_mots_ids' => array()
	);
	ecrire_meta(_AMOCLES_META_PREFERENCES, serialize($amocles_init));
	return(__ecrire_metas());
}

function amocles_vider_tables () {
spip_log("amocles_vider_tables() --", _AMOCLES_PREFIX);

	effacer_meta(_AMOCLES_META_PREFERENCES);
	return(__ecrire_metas());
}

?>