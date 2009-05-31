<?php 

	// base/barrac_install.php
	
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

include_spip('inc/plugin_globales_lib');

function barrac_install ($action) {
spip_log("barrac_install() --", _BARRAC_PREFIX);

	switch($action) {
		case 'test':
			// si renvoie true, c'est que la base est à jour, inutile de re-installer
			// la valise plugin "effacer tout" apparaît.
			// si renvoie false, SPIP revient avec $action = 'install' (une seule fois)
			return(isset($GLOBALS['meta'][_BARRAC_META_PREFERENCES]));
			break;
		case 'install':
			return(barrac_init());
			break;
		case 'uninstall':
			// est appellé lorsque "Effacer tout" dans exec=admin_plugin
			return(barrac_vider_tables());
			break;
		default:
			break;
	}
	
	return(true);
}

function barrac_init () {
	$barrac_init = array(
		'prefix' => _BARRAC_PREFIX
		, 'version' => __plugin_real_tag_get(_BARRAC_PREFIX, 'version')
		, 'date_install' => date('Y-m-d_H:i:s')
		, 'config' => unserialize(_BARRAC_DEFAULT_VALUES_ARRAY)
	);
	ecrire_meta(_BARRAC_META_PREFERENCES, serialize($barrac_init));
	return(__ecrire_metas());
}

function barrac_vider_tables () {
spip_log("barrac_vider_tables() --", _BARRAC_PREFIX);

	effacer_meta(_BARRAC_META_PREFERENCES);
	return(__ecrire_metas());
}

?>