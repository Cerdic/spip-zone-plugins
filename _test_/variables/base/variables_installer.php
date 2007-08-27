<?php

$GLOBALS['variables_version'] = 0.1;

function tables_upgrade() {
	
	$version_base = $GLOBALS['variables_version'];
	$current_version = 0.0;
	
	// Si la version installee est la derniere en date, on ne fait rien
	if ( (isset($GLOBALS['meta']['variables_version']) )
		&& (($current_version = $GLOBALS['meta']['variables_version']) == $version_base))
	return;
			
	//Si c est une nouvelle installation toute fraiche
	if ($current_version == 0.0){
		
		//insertion des infos par defaut
		$lala = lire_meta('tables');
		
		echo "Variables installé @ ".$version_base;
		ecrire_meta('variables_version',$current_version=$version_base);
	}
	ecrire_metas();
}


	//supprime les données depuis la table spip_auteurs_ajouts
	function tables_vider_tables() {
		effacer_meta('tables');
		effacer_meta('tables_version');
		ecrire_metas();
	}
	
	function tables_install($action){
		$version_base = $GLOBALS['variables_version'];
		switch ($action){
			case 'test':
 				return (isset($GLOBALS['meta']['variables_version']) AND ($GLOBALS['meta']['variables_version']>=$version_base));
				break;
			case 'install':
				tables_upgrade();
				break;
			case 'uninstall':
				tables_vider_tables();
				break;
		}
	}
?>