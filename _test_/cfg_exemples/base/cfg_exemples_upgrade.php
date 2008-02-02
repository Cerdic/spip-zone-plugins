<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function cfg_exemples_upgrade($version_cible) {
	spip_log("cfg_exemples_upgrade $nom_meta_base_version => $version_cible");
	include_spip('base/create');
	include_spip('base/abstract_sql');
	include_spip('base/cfg_exemples');
	creer_base();

	ecrire_meta('cfg_exemples_base_version', $version_cible, 'non');
	ecrire_metas();
}

function cfg_exemples_vider_tables() {
	spip_log("cfg_exemples_vider_tables");
	spip_query("DROP TABLE spip_cfg_exemples");
	effacer_meta('cfg_exemples_base_version');
	ecrire_metas();
}



function cfg_exemples_install($action){
	$version_base = $GLOBALS['cfg_exemples_base_version'];
	switch ($action){
		case 'test':
			return (isset($GLOBALS['meta']['cfg_exemples_base_version']) 
			AND ($GLOBALS['meta']['cfg_exemples_base_version']>=$version_base));
			break;
		case 'install':
			cfg_exemples_upgrade($version_base);
			break;
		case 'uninstall':
			cfg_exemples_vider_tables();
			break;
	}
}

?>
