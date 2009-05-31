<?php 

	// lido_mes_options.php

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
	la Free Software Foundation (version 2 ou bien toute autre version ultérieure 
	choisie par vous).
	
	LiDo est distribué car potentiellement utile, mais SANS AUCUNE GARANTIE,
	ni explicite ni implicite, y compris les garanties de commercialisation ou
	d'adaptation dans un but spécifique. Reportez-vous à la Licence Publique Générale GNU 
	pour plus de détails. 
	
	Vous devez avoir reçu une copie de la Licence Publique Generale GNU 
	en meme temps que ce programme ; si ce n'est pas le cas, ecrivez à la  
	Free Software Foundation, Inc., 
	59 Temple Place, Suite 330, Boston, MA 02111-1307, États-Unis.
	
	*****************************************************/



define("_LIDO_PREFIX", "lido");
define("_LIDO_LANG", _LIDO_PREFIX.":");
define("_DIR_PLUGIN_LIDO_IMG_PACK", _DIR_PLUGIN_LIDO."images/");
define("_LIDO_META_PREFERENCES", _LIDO_PREFIX."_preferences");
define("_LIDO_NAME", "Livre d'or");
define("_LIDO_COMMENT_MAX_LEN", 1024);
define("_LIDO_COMMENT_MIN_LEN", 10);
define("_LIDO_SIGN_MAX_LEN", 64);
define("_LIDO_PRE_LOG", _LIDO_NAME." ("._LIDO_PREFIX."):");

define("_LIDO_DEFAULT_VALUES_ARRAY", 
	serialize(
		array(
			  'lido_id_rubrique' => 0
			, 'lido_table_destination' => 'articles' // ou 'breves'
			, 'lido_id_auteur' => 0
			, 'lido_valider_auto' => 'non' // ou 'oui'
			, 'lido_prevenir_moderateur' => 'non' // ou 'oui'
			, 'lido_email_moderateur' => ''
			, 'lido_email_tag' => '['.lido_initiales_du_site().']'
		)
	)
);

function lido_log($msg) {
	spip_log($msg, _LIDO_PREFIX);
}

function lido_initiales_du_site () {
	$result = "";
	foreach(
		split(' ', preg_replace('=[[:space:]]+=', ' ', trim($GLOBALS['meta']['nom_site']))) 
			as $mot) {
		$result .= strtoupper(substr($mot, 0, 1));
	}
	return($result);
}

?>