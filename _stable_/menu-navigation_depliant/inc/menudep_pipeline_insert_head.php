<?php 

	// exec/menudep_pipeline_insert_head.php
	
	// $LastChangedRevision$
	// $LastChangedBy$
	// $LastChangedDate$

	/*****************************************************
	Copyright (C) 2007 Christian PAULUS
	cpaulus@quesaco.org - http://www.quesaco.org/
	/*****************************************************
	
	This file is part of Menudep.
	
	Menudep is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.
	
	Menudep is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.
	
	You should have received a copy of the GNU General Public License
	along with Menudep; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
	
	/*****************************************************
	
	Ce fichier est un des composants de Menudep. 
	
	Menudep est un programme libre, vous pouvez le redistribuer et/ou le modifier 
	selon les termes de la Licence Publique Generale GNU publie'e par 
	la Free Software Foundation (version 2 ou bien toute autre version ulterieure 
	choisie par vous).
	
	Menudep est distribue car potentiellement utile, mais SANS AUCUNE GARANTIE,
	ni explicite ni implicite, y compris les garanties de commercialisation ou
	d'adaptation dans un but specifique. Reportez-vous a' la Licence Publique Generale GNU 
	pour plus de details. 
	
	Vous devez avoir recu une copie de la Licence Publique Generale GNU 
	en meme temps que ce programme ; si ce n'est pas le cas, ecrivez a' la  
	Free Software Foundation, Inc., 
	59 Temple Place, Suite 330, Boston, MA 02111-1307, Etats-Unis.
	
	*****************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;


// pipeline (plugin.xml)
// Insere les css du menu depliant dans l'espace public
function menudep_insert_head ($flux) {

	include_spip('inc/plugin_globales_lib');

	$config = __plugin_lire_key_in_serialized_meta('config', _MENUDEP_META_PREFERENCES);

	if(!$config) $config = array();

	$menudep_values_array = unserialize(_MENUDEP_DEFAULT_VALUES_ARRAY);

	$js_var_menudep = "";
	
	foreach($menudep_values_array as $key => $value) {
		if(!isset($config[$key]) || !$config[$key] || empty($config[$key])) $config[$key] = $value;
		$js_var_menudep .= "'".preg_replace(',(menudep_),','',$key)."':'".$config[$key]."',";
	}
	$js_var_menudep = rtrim($js_var_menudep, ",");

$flux .= "
<!-- "._MENUDEP_PREFIX." -->
<script type='text/javascript'>
	var menudep = { ".$js_var_menudep." };
</script>
<script type='text/javascript' src='/".find_in_path('javascript/jquery-menudep.js')."'></script>
<!-- "._MENUDEP_PREFIX." END -->
";

	return ($flux);
} // end 

?>