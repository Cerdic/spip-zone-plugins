<?php
include_spip('base/create');
include_spip('base/abstract_sql');

function articles_vers_rubriques_install($action){
	
	// récupére le numéro de version du fichier plugin.xml
	$Tplugins_actifs = liste_plugin_actifs();
	$version_script = $Tplugins_actifs['ARTICLES_VERS_RUBRIQUES']['version'];
		
	// install/désinstall ? 
	switch ($action){
		case 'test':
			return (lire_meta("articles_vers_rubriques_version") == $version_script);
		case 'install':
			include_spip('base/articles_vers_rubriques_base');
			//print_r($tables_principales['spip_articles_rubriques']); exit;
			creer_base();
			ecrire_meta("articles_vers_rubriques_version",$version_script);
			break;
		case 'uninstall':
			sql_drop_table(TABLE_ARTICLES_RUBRIQUES);
			effacer_meta("articles_vers_rubriques_version");
			ecrire_metas();
			break;
	}
}

?>
