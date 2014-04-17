<?php
/*
 * Plugin Compositions
 * (c) 2007-2009 Cedric Morin
 * Distribue sous licence GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;


function codes_postaux_jqueryui_plugins($scripts){
	$scripts[] = "jquery.ui.autocomplete";
	return $scripts;
}

function codes_postaux_rechercher_liste_des_champs($tables){
  $tables['code_postal']['titre'] = 3;
  $tables['code_postal']['code'] = 5;
  return $tables;
}


function codes_postaux_declarer_tables_objets_surnoms($surnoms) {
	$surnoms['codes_postaux'] = 'code_postal';
	return $surnoms;
}



?>
