<?php 

	// amocles_mes_options.php

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

if (!defined('_DIR_PLUGIN_AMOCLES')) {
	$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	define('_DIR_PLUGIN_AMOCLES',(_DIR_PLUGINS.end($p)).'/');
} 

define("_AMOCLES_PREFIX", "amocles");
define("_AMOCLES_LANG", _AMOCLES_PREFIX.":");
define("_DIR_PLUGIN_AMOCLES_IMG_PACK", _DIR_PLUGIN_AMOCLES."images/");
define("_AMOCLES_FILEMAXSIZE", (1024 * 100)); // compresse à partir de 100 Ko.
define("_AMOCLES_META_PREFERENCES", "amocles_preferences");

// Autoriser a creer un groupe de mots
// http://doc.spip.org/@autoriser_groupemots_creer_dist
function autoriser_groupemots_creer($faire, $type, $id, $qui, $opt) {
	global $connect_id_auteur;
	include_spip('inc/amocles_api');
	$admins_groupes_mots_ids = amocles_admins_groupes_mots_get_ids();
   return(in_array($connect_id_auteur, $admins_groupes_mots_ids));
}

// Autoriser a modifier un groupe de mots $id
// http://doc.spip.org/@autoriser_groupemots_modifier_dist
function autoriser_groupemots_modifier($faire, $type, $id, $qui, $opt) {
	global $connect_id_auteur;
	include_spip('inc/amocles_api');
	$admins_groupes_mots_ids = amocles_admins_groupes_mots_get_ids();
   return(in_array($connect_id_auteur, $admins_groupes_mots_ids));
}
/**/

?>