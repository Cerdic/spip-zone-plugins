<?php
/*
 * Plugin traduire_texte
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;


function traduiretexte_declarer_tables_auxiliaires($tables_auxiliaires){

	$tables_auxiliaires['spip_traductions'] = array(
		"field" => array(
			"hash"   => "varchar(32) NOT NULL",
			"langue" => "varchar(5) NOT NULL",
			"texte"  => "text NOT NULL"
		),
		"key" => array(
			"KEY hash" => "`hash`"
		)
	);

	return $tables_auxiliaires;

}

function traduiretexte_upgrade($nom_meta_base_version,$version_cible){
	$current_version = 0.0;
	if ((!isset($GLOBALS['meta'][$nom_meta_base_version]) )
	|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){
		include_spip('base/abstract_sql');

		if (version_compare($current_version,"0.0.1",'<')){
			include_spip('base/serial');
			include_spip('base/auxiliaires');
			include_spip('base/create');
			creer_base();

			maj_tables(array(
				'spip_traductions'
			));

			ecrire_meta($nom_meta_base_version,$current_version=$version_cible,'non');

		}

	}
}

function traduiretexte_vider_tables($nom_meta_base_version) {
	effacer_meta($nom_meta_base_version);
	sql_drop_table("spip_traductions");
}


function traduiretexte_install($action,$prefix,$version_cible){
	$version_base = $GLOBALS[$prefix."_base_version"];

	switch ($action){
		case 'test':
			$ok = (isset($GLOBALS['meta'][$prefix."_base_version"])
				AND version_compare($GLOBALS['meta'][$prefix."_base_version"],$version_cible,">="));
			return $ok;
			break;
		case 'install':
			traduiretexte_upgrade($prefix."_base_version",$version_cible);
			break;
		case 'uninstall':
			traduiretexte_vider_tables($prefix."_base_version");
			break;
	}
}


?>
