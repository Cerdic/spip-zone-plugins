<?php

$GLOBALS['accesrestreint_version_base'] = 0.1;
function AccesRestreint_upgrade(){
	global $accesrestreint_version_base;
	$meta_base = 'accesrestreint_base_version';
	$version_base = $accesrestreint_version_base;
	$current_version = 0.0;
	if (   (isset($GLOBALS['meta'][$meta_base]) )
			&& (($current_version = $GLOBALS['meta'][$meta_base])>=$version_base))
		return;

	include_spip('base/acces_restreint');
	if ($current_version==0.0){
		include_spip('base/create');
		include_spip('base/abstract_sql');
		creer_base();
		ecrire_meta($meta_base,$current_version=$version_base,'non');
	}
		var_dump($current_version);
	ecrire_metas();
}

function AccesRestreint_install($action){
	global $accesrestreint_version_base;
	switch ($action){
		case 'test':
			return (isset($GLOBALS['meta']['accesrestreint_base_version']) AND ($GLOBALS['meta']['accesrestreint_base_version']>=$accesrestreint_version_base));
			break;
		case 'install':
			AccesRestreint_upgrade();
			break;
		case 'uninstall':
			AccesRestreint_vider_tables();
			break;
	}
}
?>