<?php
include_spip('base/create');
include_spip('base/abstract_sql');

function breves2art_install($action){
	
	// récupére le numéro de version du fichier plugin.xml
	$Tplugins_actifs = liste_plugin_actifs();
	$version_script = $Tplugins_actifs['BREVES2ART']['version'];
		
	// install/désinstall ? 
	switch ($action){
		case 'test':
			return (lire_meta('breves2art_version') == $version_script);
		case 'install':
			include_spip('base/breves2art_base');

			creer_base();
			ecrire_meta("breves2art_version",$version_script);
			break;
		case 'uninstall':
			sql_drop_table(TABLE_BREVES_ARTICLES);
			effacer_meta("breves2art_version");
			ecrire_metas();
			break;
	}
}

?>
