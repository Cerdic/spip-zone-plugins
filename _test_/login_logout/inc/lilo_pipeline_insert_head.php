<?php 

	// exec/lilo_pipeline_insert_head.php
	
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
	selon les termes de la Licence Publique Generale GNU publiée par 
	la Free Software Foundation (version 2 ou bien toute autre version ultérieure 
	choisie par vous).
	
	LiLo est distribué car potentiellement utile, mais SANS AUCUNE GARANTIE,
	ni explicite ni implicite, y compris les garanties de commercialisation ou
	d'adaptation dans un but spécifique. Reportez-vous à la Licence Publique Générale GNU 
	pour plus de détails. 
	
	Vous devez avoir reçu une copie de la Licence Publique Generale GNU 
	en meme temps que ce programme ; si ce n'est pas le cas, ecrivez à la  
	Free Software Foundation, Inc., 
	59 Temple Place, Suite 330, Boston, MA 02111-1307, États-Unis.
	
	*****************************************************/
	
if (!defined("_ECRIRE_INC_VERSION")) return;

// pipeline (plugin.xml)
// Insère les js et css dans head de l'espace public
function lilo_insert_head ($flux) {

	include_spip('inc/filtres');
	include_spip('inc/plugin_globales_lib');
	
	$config = __plugin_lire_key_in_serialized_meta('config', _LILO_META_PREFERENCES);

	if(!$config) $config = array();

	$lilo_values_array = unserialize(_LILO_DEFAULT_VALUES_ARRAY);

	$lilo_config_vars = "";
	
	foreach($lilo_values_array as $key => $value) {
		if(!isset($config[$key]) || !$config[$key] || empty($config[$key])) $config[$key] = $value;
		$lilo_config_vars .= "'".preg_replace(',(lilo_),','',$key)."':'".$config[$key]."',";
	}
	$lilo_config_vars = ""
		. "<script language='JavaScript' type='text/javascript'>"
		. " var lilo_config = { "
		. rtrim($lilo_config_vars, ",")
		. " };"
		. "</script>"
		;

	// 
	$flux .= "
<!-- "._LILO_PREFIX." START -->
$lilo_config_vars
<script type='text/javascript' src='".compacte(find_in_path('javascript/lilo_login.js'), 'js')."'></script>
<link rel='stylesheet' href='".compacte(find_in_path('lilo_public.css'), 'css')."' type='text/css' />
$lilo_css_fixed_ie6
<!-- "._LILO_PREFIX." END -->
		";

	return ($flux);
} // end lilo_insert_head()
?>