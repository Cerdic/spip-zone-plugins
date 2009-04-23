<?php

$GLOBALS['i2_societes_version'] = 0.02;

function i2_societes_upgrade(){
	$version_base = $GLOBALS['i2_societes_version'];
	$current_version = 0.0;
	
	// Si la version installee est la derniere en date, on ne fait rien
	if ( (isset($GLOBALS['meta']['i2_societes_version']) )
		&& (($current_version = $GLOBALS['meta']['i2_societes_version'])==$version_base))
	return;
	
	//Si c est une nouvelle installation toute fraiche
	include_spip('base/i2_societes');
	if ($current_version==0.0){
		$config_inscription2 = $GLOBALS['meta']['inscription2'];
		
		if (!is_array(unserialize($config_inscription2))) {
	    	unset($config_inscription2);
		}
		
		$config_societes =	array(
				'id_societe' => NULL,
				'id_societe_obligatoire' => NULL,
				'id_societe_fiche_mod' => NULL,
				'id_societe_fiche' => NULL,
				'id_societe_table' => NULL
			);

		$config_finale = array_merge(unserialize($config_inscription2),$config_societes);
		
		ecrire_config('inscription2',$config_finale);
		
		include_spip('base/create');
		include_spip('base/abstract_sql');
		creer_base();
		ecrire_meta('i2_societes_version',$current_version=$version_base,'non');
	}
	if ($current_version==0.01){
		sql_alter("TABLE spip_societes ADD maj TIMESTAMP after fax");
		echo "I2_societes @ 0.02<br />";
		ecrire_meta('i2_societes_version',$current_version='0.02','non');
	}
}

function i2_societes_vider_tables() {
	include_spip('base/abstract_sql');
	sql_drop_table('spip_societes');
	effacer_meta('i2_societes');
	ecrire_metas();
}

function i2_societes_install($action){
	$version_base = $GLOBALS['i2_societes_version'];
	switch ($action){
		case 'test':
			return (isset($GLOBALS['meta']['i2_societes_version']) AND ($GLOBALS['meta']['i2_societes_version']>=$version_base));
			break;
		case 'install':
			i2_societes_upgrade();
			break;
		case 'uninstall':
			i2_societes_vider_tables();
			break;
	}
}
?>