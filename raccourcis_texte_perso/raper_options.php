<?php

// raper_options.php

	/*****************************************************
	Copyright (C) 2009 Christian PAULUS
	cpaulus@quesaco.org - http://www.quesaco.org/
	/*****************************************************
	
	This file is part of RaPer.
	
	RaPer is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.
	
	RaPer is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with RaPer; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	
	/*****************************************************
	
	Ce fichier est un des composants de RaPer. 
	
	RaPer est un programme libre, vous pouvez le redistribuer et/ou le modifier 
	selon les termes de la Licence Publique Generale GNU publie'e par 
	la Free Software Foundation (version 2 ou bien toute autre version ulterieure 
	choisie par vous).
	
	RaPer est distribue' car potentiellement utile, mais SANS AUCUNE GARANTIE,
	ni explicite ni implicite, y compris les garanties de commercialisation ou
	d'adaptation dans un but spe'cifique. Reportez-vous a' la Licence Publique Ge'ne'rale GNU 
	pour plus de details. 
	
	Vous devez avoir reçu une copie de la Licence Publique Generale GNU 
	en meme temps que ce programme ; si ce n'est pas le cas, ecrivez a' la  
	Free Software Foundation, Inc., 
	59 Temple Place, Suite 330, Boston, MA 02111-1307, Etats-Unis.
	
	*****************************************************/
	
// $LastChangedRevision$
// $LastChangedBy$
// $LastChangedDate$

// *_options est appelé à chaque hit

if (!defined('_ECRIRE_INC_VERSION')) return;


if (version_compare($GLOBALS['spip_version_code'], '1.9300', '>')) {
	include_spip('inc/lang');
}

if(!defined('_DIR_PLUGIN_RAPER')) {
	$p = explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
	define('_DIR_PLUGIN_RAPER',(_DIR_PLUGINS.end($p)).'/');
}
define("_DIR_RAPER_IMG_PACK", _DIR_PLUGIN_RAPER."images/");
define("_DIR_RAPER_LANGUES", _DIR_PLUGIN_RAPER."lang/");

define("_RAPER_LANG_FILENAME_PREFIX", "local");
define("_RAPER_TYPE_LANGUES_UTILISEES", "langues_utilisees");
define("_RAPER_TYPE_LANGUES_MULTILINGUE", "langues_multilingue");

define("_RAPER_PREFIX", "raper");
define("_RAPER_META_PREFS", _RAPER_PREFIX."_preferences");
define("_RAPER_AUTORISER_GERER_AUCUN", "aucun");
define("_RAPER_AUTORISER_GERER_RESTREINTS", "restreints");

define("_RAPER_DEFAULT_VALUES_ARRAY", 
 	serialize(
		array(
			// peut contenir
			// 'aucun' : pas de délégation d'administration
			// 'restreints' : tous les admins restreints peuvent gérer les raccourcis texte
			// '3,4,5' : les admins $id_admin 3, 4 et 5 peuvent gérer les raccourcis texte // TODO qd le temps dispo
			'autoriser_gerer' => "aucun"
			
			// prendre toutes les langues définies ou seulement celles utilisées ?
			, 'type_langues' => _RAPER_TYPE_LANGUES_UTILISEES
			
			// les modèles de traduction
			, 'editer_tout' => "non" 
			, 'editer_public' => "oui" // prendre en charge public_nn.php ?
			, 'editer_ecrire' => "non" // ecrire_nn.php ?
			, 'editer_spip' => "non" // spip_nn.php ?
			, 'editer_local' => "oui" // (lang/)local_nn.php ou (lang/)local.php
			
			// les raccourcis personnalisés sont stockés dans les prefs.
			// (Ils sont lus des fichiers local_* du raper et placés ici pour le traitement)
			, 'raccourcis' => array(
			)
		)
	)
);


