<?php 

	// base/lilo_install.php
	
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
	selon les termes de la Licence Publique Generale GNU publi�e par 
	la Free Software Foundation (version 2 ou bien toute autre version ult�rieure 
	choisie par vous).
	
	LiLo est distribu� car potentiellement utile, mais SANS AUCUNE GARANTIE,
	ni explicite ni implicite, y compris les garanties de commercialisation ou
	d'adaptation dans un but sp�cifique. Reportez-vous � la Licence Publique G�n�rale GNU 
	pour plus de d�tails. 
	
	Vous devez avoir re�u une copie de la Licence Publique Generale GNU 
	en meme temps que ce programme ; si ce n'est pas le cas, ecrivez � la  
	Free Software Foundation, Inc., 
	59 Temple Place, Suite 330, Boston, MA 02111-1307, �tats-Unis.
	
	*****************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip("inc/plugin_globales_lib");

function lilo_install ($action) {

	switch($action) {
		case 'test':
			// si renvoie true, c'est que la base est � jour, inutile de re-installer
			// la valise plugin "effacer tout" appara�t.
			// si renvoie false, SPIP revient avec $action = 'install' (une seule fois)
			$result = intval(isset($GLOBALS['meta'][_LILO_META_PREFERENCES]));
			return($result);
			break;
		case 'install':
			return(lilo_init());
			break;
		case 'uninstall':
			// est appell� lorsque "Effacer tout" dans exec=admin_plugin
			return(lilo_vider_tables());
			break;
		default:
			break;
	}
	
	return(true);
}

function lilo_init () {
	$lilo_init = array(
		'prefix' => _LILO_PREFIX
		, 'version' => __plugin_real_tag_get(_LILO_PREFIX, 'version')
		, 'version_base' => __plugin_real_tag_get(_LILO_PREFIX, 'version_base')
		, 'date_install' => date('Y-m-d_H:i:s')
		, 'config' => unserialize(_LILO_DEFAULT_VALUES_ARRAY)
	);
	ecrire_meta(_LILO_META_PREFERENCES, serialize($lilo_init));
	return(__ecrire_metas());
}

function lilo_vider_tables () {
	effacer_meta(_LILO_META_PREFERENCES);
	return(__ecrire_metas());
}

?>