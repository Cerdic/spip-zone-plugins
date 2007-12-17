<?php 

	// inc/amocles_api_vieilles_defs.php
	
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
	selon les termes de la Licence Publique Generale GNU publi�e par 
	la Free Software Foundation (version 2 ou bien toute autre version ult�rieure 
	choisie par vous).
	
	Amocles est distribu� car potentiellement utile, mais SANS AUCUNE GARANTIE,
	ni explicite ni implicite, y compris les garanties de commercialisation ou
	d'adaptation dans un but sp�cifique. Reportez-vous � la Licence Publique G�n�rale GNU 
	pour plus de d�tails. 
	
	Vous devez avoir re�u une copie de la Licence Publique Generale GNU 
	en meme temps que ce programme ; si ce n'est pas le cas, ecrivez � la  
	Free Software Foundation, Inc., 
	59 Temple Place, Suite 330, Boston, MA 02111-1307, �tats-Unis.
	
	*****************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

if(version_compare($GLOBALS['spip_version_code'],'1.9300','<')) { 
	return(false);
}

if (!function_exists(spip_num_rows)) {
function spip_num_rows ($r) {
	return sql_count($r);
}
}

if (!function_exists(bouton_block_invisible)) {
function bouton_block_invisible ($nom_block, $icone='') {
	include_spip('inc/layer');
	return bouton_block_depliable(_T("info_sans_titre"),false,$nom_block);
}
}

if (!function_exists(bouton_block_visible)) {
function bouton_block_visible ($nom_block) {
	include_spip('inc/layer');
	return bouton_block_depliable(_T("info_sans_titre"),true,$nom_block);
}
}

if (!function_exists(debut_block_visible)) {
function debut_block_visible ($id="") {
	include_spip('inc/layer');
	return debut_block_depliable(true,$id);
}
}

if (!function_exists(debut_block_invisible)) {
function debut_block_invisible ($id="") {
	include_spip('inc/layer');
	return debut_block_depliable(false,$id);
}
}

?>