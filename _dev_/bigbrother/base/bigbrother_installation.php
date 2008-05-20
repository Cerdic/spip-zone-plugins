<?php
#---------------------------------------------------#
#  Plugin  : Big Brother                            #
#  Auteur  : RastaPopoulos                          #
#  Licence : GPL                                    #
#------------------------------------------------- -#

// La fonction de base appelée par le gestionnaire de plugins
function bigbrother_install($install){
	
	//recupère les informations de plugin.xml
	include_spip('inc/plugin');
	$infos = plugin_get_infos(_DIR_PLUGIN_BIGBROTHER);
	$version_du_plugin = $infos['version'];
	
	switch($install) {
		case 'test':
			return isset($GLOBALS['meta']['bigbrother_version_base']) AND ($GLOBALS['meta']['bigbrother_version_base']>=$version_du_plugin);
		case 'install':
			bigbrother_maj();
			break;
		case 'uninstall':
			bigbrother_vider_tables();
			break;
	}
	
}

// Supprimer les tables du plugin
function bigbrother_vider_tables() {
	include_spip('base/abstract_sql');
	sql_query("DROP TABLE spip_visites_auteurs");
	sql_query("DROP_TABLE spip_visites_articles_auteurs");
	effacer_meta('bigbrother_version_base');
}

// Compatibilite <= SPIP 1.92
function bigbrother_sql_swhotable($table, $table_spip = false, $serveur='') {
	return spip_abstract_showtable($table, $serveur, $table_spip);
}

// Met à jour le numéro de version dans les métas
function bigbrother_maj_version(&$v1, $v2) {
	echo "MAJ Big Brother : $v1 =&gt; $v2<br />";
	ecrire_meta('bigbrother_version_base', $v1=$v2, 'non');
}

// Là où se trouver les mises à jour
function bigbrother_maj(){
	
	//recupère les informations de plugin.xml
	include_spip('inc/plugin');
	$infos = plugin_get_infos(_DIR_PLUGIN_BIGBROTHER);
	$version_du_plugin = $infos['version'];
	$version_en_cours = isset($GLOBALS['meta']['bigbrother_version_base'])
		?$GLOBALS['meta']['bigbrother_version_base']
		:0.0;
	
	// On met à jour seulement si les versions sont différentes
	if ($version_en_cours != $version_du_plugin){
		
		include_spip('base/create');
		include_spip('base/abstract_sql');
		include_spip('base/bigbrother_tables');
		
		// compatibilite SPIP 1.92
		$showtable = function_exists('sql_showtable')?'sql_showtable':'bigbrother_sql_swhotable';
		$fetch = function_exists('sql_fetch')?'sql_fetch':'spip_fetch_array';
				
		if ($version_en_cours == 0.0){
			// A la première installation on crée les tables
			creer_base();
			bigbrother_maj_version($version_en_cours, 0.1);
		}
		
/*
		if ($version_en_cours < ($version_de_test = 0.11)){
			//$desc = $showtable("spip_jeux", true);
			
			bigbrother_maj_version($version_en_cours, $version_de_test);
		}
*/

	}
}

?>
