<?php 

// inc/raper_pipeline_header_prive.php

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

/*
	
	Nota: plugin.xml en cache.
		si modif plugin.xml, il faut reactiver le plugin (config/plugin: desactiver/activer)
		Pas sur SPIP.2
	Nota:
		SPIP 2 fait automatiquement un cache des css pour l'espace prive'.
		Penser a vider le cache ou simplement local/cache-css/ lors de modification de la feuille.
	
*/

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/raper_api_prive');

function raper_header_prive ($flux) {

	$exec = _request('exec');

	$pages_raper = array('raper_configure', 'raper_edit');

	if(in_array($exec, $pages_raper)) {
		
		$flux .= ""
			. "\n"
			. "<!-- raper -->\n"
			. "<link rel='stylesheet' href='"._DIR_PLUGIN_RAPER."raper_style_prive.css' type='text/css' />\n"
			. "<style type='text/css'>\n"
			. "<!--\n"
			. ".raper-edit { background-image: url("._DIR_RAPER_IMG_PACK."edit-16.png); }\n"
			. ".raper-drop { background-image: url("._DIR_RAPER_IMG_PACK."drop-16.png); }\n"
			. ".raper-cancel { background-image: url("._DIR_RAPER_IMG_PACK."cancel-16.png) !important; }\n"
			. ".raper-apply { background-image: url("._DIR_RAPER_IMG_PACK."apply-16.png) !important;}\n"
			. ".raper-rload { background-image: url("._DIR_RAPER_IMG_PACK."rload-16.gif); }\n"
			. "-->\n"
			. "</style>\n"
			// passer quelques parametres communs (js php) par les metas XHTML.
			. "<meta id='x-raper_lang' content='" . raper_lang() . "' />\n"
			. "<meta id='x-raper_title_drop' content=\"" . _T('raper:perso_drop') . "\" />\n"
			. "<meta id='x-raper_title_edit' content=\"" . _T('raper:perso_edit') . "\" />\n"
			. "<script type='text/javascript' src='" . find_in_path("javascript/raper_prive.js") . "'></script>\n"
			. "<!-- !raper -->\n"
			;
	}
	return ($flux);
}

