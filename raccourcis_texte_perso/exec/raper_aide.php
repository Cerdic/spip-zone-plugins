<?php

// exec/raper_aide.php

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

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/raper_api_globales');
include_spip('inc/raper_api_prive');

function aide_raper_erreur() {
	echo minipres(_T('forum_titre_erreur'),
		 "<div>"._T('aide_non_disponible')."<br /></div><div align='right'>".menu_langues('var_lang_ecrire')."</div>");
	exit;
}

function exec_raper_aide () {

	global $spip_lang;

	include_spip('inc/plugin');
	
	$var_lang = _request('var_lang');
	
	if (!changer_langue($var_lang)) {
		$var_lang = $spip_lang;
		changer_langue($var_lang);
	}
	
	$f = _DIR_PLUGIN_RAPER . "docs/raper_aide.".$var_lang.".html";
	
	if(
		is_readable($f)
		&& ($content = @file_get_contents($f))
	) {
		// corrige les liens images
		$content = str_replace("../img_docs/", _DIR_PLUGIN_RAPER."img_docs/", $content);
		// place les vars
		$pattern = array(
			"/@plugin_name@/"
			, "/@sous_titre@/"
			,"/@plugin_version@/"
			,'/\$LastChangedDate:/'
			,'/\$LastChangedBy:/'
			, '/\$@@/'
			,'/@_aide@/'
			);
		$replacement = array(
			_T('raper:raper')
			, _T('raper:raccourcis_perso')
			, raper_meta_plugin_version()
			, ''
			, ''
			, ''
			, _T('raper:_aide')
			);
		$content = preg_replace($pattern, $replacement, $content);
		
		echo($content);
	}
	else {
		aide_raper_erreur();
	}
}

