<?php 

	// base/lido_install.php

	// $LastChangedRevision$
	// $LastChangedBy$
	// $LastChangedDate$

	/*****************************************************
	Copyright (C) 2008 Christian PAULUS
	cpaulus@quesaco.org - http://www.quesaco.org/
	/*****************************************************
	
	This file is part of LiDo.
	
	LiDo is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.
	
	LiDo is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with LiDo; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	
	/*****************************************************
	
	Ce fichier est un des composants de LiDo. 
	
	LiDo est un programme libre, vous pouvez le redistribuer et/ou le modifier 
	selon les termes de la Licence Publique Generale GNU publiée par 
	la Free Software Foundation (version 2 ou bien toute autre version ult�rieure 
	choisie par vous).
	
	LiDo est distribué car potentiellement utile, mais SANS AUCUNE GARANTIE,
	ni explicite ni implicite, y compris les garanties de commercialisation ou
	d'adaptation dans un but spécifique. Reportez-vous à la Licence Publique Générale GNU 
	pour plus de d�tails. 
	
	Vous devez avoir reçu une copie de la Licence Publique Generale GNU 
	en meme temps que ce programme ; si ce n'est pas le cas, ecrivez à la  
	Free Software Foundation, Inc., 
	59 Temple Place, Suite 330, Boston, MA 02111-1307, Etats-Unis.
	
	*****************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip("inc/plugin_globales_lib");

function lido_install ($action) {

	switch($action) {
		case 'test':
			// si renvoie true, c'est que la base est à jour, inutile de re-installer
			// la valise plugin "effacer tout" apparait.
			// si renvoie false, SPIP revient avec $action = 'install' (une seule fois)
			$result = intval(isset($GLOBALS['meta'][_LIDO_META_PREFERENCES]));
			return($result);
			break;
		case 'install':
			return(lido_init());
			break;
		case 'uninstall':
			// est appell� lorsque "Effacer tout" dans exec=admin_plugin
			return(lido_vider_tables());
			break;
		default:
			break;
	}
	
	return(true);
}

function lido_init () {
	lido_log(_LIDO_PRE_LOG." INSTALL");
	$lido_init = array(
		'prefix' => _LIDO_PREFIX
		, 'version' => __plugin_real_tag_get(_LIDO_PREFIX, 'version')
		, 'version_base' => __plugin_real_tag_get(_LIDO_PREFIX, 'version_base')
		, 'date_install' => date('Y-m-d_H:i:s')
		, 'config' => unserialize(_LIDO_DEFAULT_VALUES_ARRAY)
	);
	ecrire_meta(_LIDO_META_PREFERENCES, serialize($lido_init));
	return(__ecrire_metas());
}

function lido_vider_tables () {
	lido_log(_LIDO_PRE_LOG." UNINSTALL");
	effacer_meta(_LIDO_META_PREFERENCES);
	return(__ecrire_metas());
}

?>