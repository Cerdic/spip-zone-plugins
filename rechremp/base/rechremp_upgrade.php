<?php
include_spip('base/create');
include_spip('base/abstract_sql');

function rechremp_install($action){
	
	// récupére le numéro de version du fichier plugin.xml
	$Tplugins_actifs = liste_plugin_actifs();
	$version_script = $Tplugins_actifs['rechremp']['version'];
		
	// install/désinstall ? 
	switch ($action){
		case 'test':
			return (lire_meta('rechremp_version') == $version_script);

		case 'install':
			include_spip('rechremp_pipelines.php');
			$GLOBALS['spip_pipeline']['rechercher_liste_des_champs'] .="|rechremp_rechercher_liste_des_champs";
			ecrire_meta("rechremp_version",$version_script);
			break;
			
		case 'uninstall':
			effacer_meta("rechremp_version");
			ecrire_metas();
			break;
	}
}

?>
