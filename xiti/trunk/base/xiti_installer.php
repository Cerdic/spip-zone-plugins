<?php
$GLOBALS['xiti_version'] = 0.1;

function xiti_upgrade($nom_meta_base_version,$version_cible){
	$current_version = 0.0;
	if (   (!isset($GLOBALS['meta'][$nom_meta_base_version]) )
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){

		if ($current_version==0.0){
				ecrire_meta('xiti_version','0.1');
			// Installation des bases de donn�es
				spip_log('XITI : installation version '.$GLOBALS['xiti_version'],'xiti');
			// Ecrire META
				ecrire_meta('xiti_config',serialize(array(
					'id_xiti' => '',
					'logo_xiti' => 'hit',
					'width' => '39',
					'height' => '25'
				)));
				ecrire_meta($nom_meta_base_version,$current_version=$version_cible);
		}
	
		echo "XITI : installation de la version ".$GLOBALS['xiti_version'];
		
	}

}

function xiti_vider_tables() {
	spip_log('XITI : D�sinstallation','xiti');
	spip_log('XITI : Tables effac�es','xiti');
}

/*function xiti_install($action){
	switch ($action){
		case 'install':
			xiti_upgrade();
			break;
		case 'uninstall':
			xiti_vider_tables();
			break;
	}
}*/
?>