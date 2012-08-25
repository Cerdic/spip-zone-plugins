<?php
/*
 * Plugin Compositions
 * (c) 2007-2009 Cedric Morin
 * Distribue sous licence GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;



/*
function cp_rechercher_liste_des_jointures($tables) {
	$tables['cog_commune']['code_postal']['nom'] = 2;
	return $tables;
}
*/



function cp_rechercher_liste_des_champs($tables){
	  $tables['code_postal']['titre'] = 3;
          $tables['code_postal']['code'] = 5;
	  return $tables;
	}


function cp_declarer_tables_objets_surnoms($surnoms) {
	$surnoms['code_postaux'] = 'code_postal';
return $surnoms;
}



?>
