<?php

	// inc/spiplistes_api_vieilles_defs.php

	// $LastChangedRevision$
	// $LastChangedBy$
	// $LastChangedDate$

if (!defined("_ECRIRE_INC_VERSION")) return;

if(spiplistes_spip_est_inferieur_193()) { 
	return(false);
}

// conflit (doublons) avec plugins important vieilles defs...
// en attendant de tout nettoyer
$included_files = get_included_files();
foreach ($included_files as $filename) {
	if(basename($filename) == "vieilles_defs.php") {
		return(true);
	}
}

if(!function_exists('debut_block_visible')) {
	function debut_block_visible ($id="") {
		include_spip('inc/layer');
		return debut_block_depliable(true,$id);
	}
}

if(!function_exists('debut_block_invisible')) {
	function debut_block_invisible ($id="") {
		include_spip('inc/layer');
		return debut_block_depliable(false,$id);
	}
}

// utilisé en 192C (inc/boutons.php)
// toujours a cette valeur a present en 193
$GLOBALS['options'] = 'avancees';

if(!function_exists('spip_abstract_showtable')) {
	function spip_abstract_showtable ($table, $serveur='', $table_spip = false) {
		vieilles_log('spip_abstract_showtable()');
		 return sql_showtable($table, $serveur, $table_spip);
	}
}

// utilisé en 192C (base/db_mysql.php)
// constantes spip pour mysql_fetch_array() qui est encore dans inc/utils.php en 193
@define('SPIP_BOTH', MYSQL_BOTH);
@define('SPIP_ASSOC', MYSQL_ASSOC);
@define('SPIP_NUM', MYSQL_NUM);


?>