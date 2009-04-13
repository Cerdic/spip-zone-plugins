<?php

$GLOBALS['prang_version'] = 0.02;

function prang_upgrade(){
	$version_base = $GLOBALS['prang_version'];
	$current_version = 0.0;
	
	// Si la version installee est la derniere en date, on ne fait rien
	if ( (isset($GLOBALS['meta']['prang_version']) )
		&& (($current_version = $GLOBALS['meta']['prang_version'])==$version_base))
	return;
	
	//Si c est une nouvelle installation toute fraiche
	include_spip('base/prang');
	if ($current_version==0.0){				
		include_spip('base/create');
		include_spip('base/abstract_sql');
		maj_tables('spip_rubriques');
		maj_tables('spip_articles');
		ecrire_meta('prang_version',$current_version=$version_base,'non');
	}
	
	//conversion des titres existants 
	if ($current_version==0.01) {
	    sql_query("UPDATE spip_articles SET rang = SUBSTRING_INDEX(titre,'.',1), titre= TRIM(SUBSTRING(titre, LOCATE('.', titre)+1)) WHERE titre REGEXP '^[0-9]+\..*$';");
	    sql_query("UPDATE spip_rubriques SET rang = SUBSTRING_INDEX(titre,'.',1), titre= TRIM(SUBSTRING(titre, LOCATE('.', titre)+1)) WHERE titre REGEXP '^[0-9]+\..*$';");
	}
}

function prang_vider_tables() {
	include_spip('base/abstract_sql');
	sql_alter("TABLE spip_rubriques DROP COLUMN rang"); 
	sql_alter("TABLE spip_articlesDROP COLUMN rang"); 
	effacer_meta('prang_version');
}

function prang_install($action){
	$version_base = $GLOBALS['prang_version'];
	switch ($action){
		case 'test':
			return (isset($GLOBALS['meta']['prang_version']) AND ($GLOBALS['meta']['prang_version']>=$version_base));
			break;
		case 'install':
			prang_upgrade();
			break;
		case 'uninstall':
			prang_vider_tables();
			break;
	}
}
?>
