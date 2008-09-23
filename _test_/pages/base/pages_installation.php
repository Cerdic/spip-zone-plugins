<?php
#---------------------------------------------------#
#  Plugin  : Pages                                  #
#  Auteur  : RastaPopoulos                          #
#  Licence : GPL                                    #
#--------------------------------------------------------------- -#
#  Documentation : http://www.spip-contrib.net/Plugin-Pages       #
#-----------------------------------------------------------------#

if (!defined("_ECRIRE_INC_VERSION")) return;

// La fonction de base appelée par le gestionnaire de plugins
function pages_install($install){
	
	//recupère les informations de plugin.xml
	find_in_path('plugin.php', 'inc/', true);
	$infos = plugin_get_infos(_DIR_PLUGIN_PAGES);
	$version_du_plugin = $infos['version'];
	
	switch($install) {
		case 'test':
			return isset($GLOBALS['meta']['pages_version_base']) AND ($GLOBALS['meta']['pages_version_base']>=$version_du_plugin);
			return true;
		case 'install':
			pages_maj();
			break;
		case 'uninstall':
			pages_vider_tables();
			break;
	}
	
}

// Supprimer les tables du plugin
function pages_vider_tables() {
	find_in_path('abstract_sql.php', 'base/', true);
	sql_alter('table spip_articles drop page');
	effacer_meta('pages_version_base');
}

// Met à jour le numéro de version dans les métas
function pages_maj_version(&$v1, $v2) {
	echo "MAJ Pages : $v1 =&gt; $v2<br />";
	ecrire_meta('pages_version_base', $v1=$v2, 'non');
}

// Là où se trouve les mises à jour
function pages_maj(){
	
	//recupère les informations de plugin.xml
	find_in_path('plugin.php', 'inc/', true);
	$infos = plugin_get_infos(_DIR_PLUGIN_PAGES);
	$version_du_plugin = $infos['version'];
	$version_en_cours = isset($GLOBALS['meta']['pages_version_base'])
		?$GLOBALS['meta']['pages_version_base']
		:0.0;
	
	// On met à jour seulement si les versions sont différentes
	if ($version_en_cours != $version_du_plugin){
		
		find_in_path('create.php', 'base/', true);
		include_spip('abstract_sql.php', 'base/', true);
		include_spip('pages_tables.php', 'base/', true);
				
		if ($version_en_cours == 0.0){
			creer_base();
			sql_alter('table spip_articles add `page` varchar(20) not null');
			pages_maj_version($version_en_cours, 0.1);
		}
		
		/*if ($version_en_cours < ($version_de_test = 0.2)){
			//$desc = $showtable("spip_jeux", true);
			
			pages_maj_version($version_en_cours, $version_de_test);
		}*/
	}
}

?>
