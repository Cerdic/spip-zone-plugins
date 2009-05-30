<?php
	
	$GLOBALS['getid3_base_version'] = 0.2;
	function getid3_verifier_base(){
		$version_base = $GLOBALS['getid3_base_version'];
		$current_version = 0.0;
		if ((!isset($GLOBALS['meta']['getid3_base_version']) )
				|| (($current_version = $GLOBALS['meta']['getid3_base_version'])!=$version_base)){
			if ($current_version<0.1){
				sql_alter("TABLE spip_documents ADD `duree` VARCHAR(255) DEFAULT '' NOT NULL AFTER `hauteur`");
				sql_alter("TABLE spip_documents ADD `bitrate` INTEGER AFTER `duree`");
				sql_alter("TABLE spip_documents ADD `bitrate_mode` text AFTER `bitrate`");
				sql_alter("TABLE spip_documents ADD `audiosamplerate` INTEGER AFTER `bitrate_mode`");
				sql_alter("TABLE spip_documents ADD `encodeur` text AFTER `audiosamplerate`");
				ecrire_meta('getid3_base_version',$current_version=0.1);
				echo _T('getid3:mise_a_jour_base',array('version'=>'0.1'));
			}
			if ($current_version<0.2){
				sql_alter("TABLE spip_documents ADD `bits` INTEGER AFTER `encodeur`");
				sql_alter("TABLE spip_documents ADD `canaux` text AFTER `bits`");
				ecrire_meta('getid3_base_version',$current_version=0.2);
				echo _T('getid3:mise_a_jour_base',array('version'=>'0.2'));
			}
			ecrire_metas();
		}
	}
	
	function getid3_vider_tables() {
	}
	
	function getid3_install($action){
		$version_base = $GLOBALS['getid3_base_version'];
		switch ($action){
			case 'test':
				return (isset($GLOBALS['meta']['getid3_base_version']) AND ($GLOBALS['meta']['getid3_base_version']>=$version_base));
				break;
			case 'install':
				getid3_verifier_base();
				break;
			case 'uninstall':
				//getid3_vider_tables();
				break;
		}
	}	
?>