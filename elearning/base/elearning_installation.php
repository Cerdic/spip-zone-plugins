<?php
#---------------------------------------------------#
#  Plugin  : E-Learning                             #
#  Auteur  : RastaPopoulos                          #
#  Licence : GPL                                    #
#--------------------------------------------------------------- -#
#  Documentation : http://www.spip-contrib.net/Plugin-E-learning  #
#-----------------------------------------------------------------#

// La fonction de base appelée par le gestionnaire de plugins
function elearning_install($install){
	
	//recupère les informations de plugin.xml
	include_spip('inc/plugin');
	$infos = plugin_get_infos(_DIR_PLUGIN_ELEARNING);
	$version_du_plugin = $infos['version'];
	
	switch($install) {
		case 'test':
			return isset($GLOBALS['meta']['elearning_version_base']) AND ($GLOBALS['meta']['elearning_version_base']>=$version_du_plugin);
		case 'install':
			elearning_maj();
			break;
		case 'uninstall':
			elearning_vider_tables();
			break;
	}
	
}

// Supprimer les tables du plugin
function elearning_vider_tables() {
	//include_spip('base/abstract_sql');
	/* Blabla effacer les tables */
	effacer_meta('elearning_version_base');
}

// Met à jour le numéro de version dans les métas
function elearning_maj_version(&$v1, $v2) {
	echo "MAJ E-Learning : $v1 =&gt; $v2<br />";
	ecrire_meta('elearning_version_base', $v1=$v2, 'non');
}

// Là où se trouve les mises à jour
function elearning_maj(){
	
	//recupère les informations de plugin.xml
	include_spip('inc/plugin');
	$infos = plugin_get_infos(_DIR_PLUGIN_ELEARNING);
	$version_du_plugin = $infos['version'];
	$version_en_cours = isset($GLOBALS['meta']['elearning_version_base'])
		?$GLOBALS['meta']['elearning_version_base']
		:0.0;
	
	// On met à jour seulement si les versions sont différentes
	if ($version_en_cours != $version_du_plugin){
		
		//include_spip('base/create');
		//include_spip('base/abstract_sql');
				
		if ($version_en_cours == 0.0){
			elearning_maj_version($version_en_cours, 0.1);
		}
		
		/*if ($current_version < ($version_de_test = 0.11)){
			//$desc = $showtable("spip_jeux", true);
			
			elearning_maj_version($current_version, $test_version);
		}*/
	}
}

?>
