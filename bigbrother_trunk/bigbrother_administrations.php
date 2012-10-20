<?php
#---------------------------------------------------#
#  Plugin  : Big Brother                            #
#  Auteur  : RastaPopoulos                          #
#  Licence : GPL                                    #
#------------------------------------------------- -#

if (!defined("_ECRIRE_INC_VERSION")) return;

// La fonction de base appelée par le gestionnaire de plugins
function bigbrother_upgrade($nom_meta_base_version,$version_cible){
	include_spip('inc/meta');
	include_spip('base/abstract_sql');
	$current_version = 0.0;
	if ((!isset($GLOBALS['meta'][$nom_meta_base_version]))
		|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)){

		if (version_compare($current_version,'0.0','<=')){
			include_spip('base/create');
			// A la première installation on crée les tables
			creer_base();
			ecrire_meta($nom_meta_base_version,$current_version=$version_cible,'non');
		}
		if (version_compare($current_version,'0.1','<')){
			include_spip('base/create');
			// A la première installation on crée les tables
			creer_base();
			ecrire_meta($nom_meta_base_version,$current_version=0.1,'non');
		}
		if (version_compare($current_version,'0.2','<')){
			include_spip('base/create');
			// A la première installation on crée les tables
			creer_base();
			ecrire_meta($nom_meta_base_version,$current_version=0.2,'non');
		}
	}
}

// Supprimer les tables du plugin
function bigbrother_vider_tables($nom_meta_base_version) {
	include_spip('base/abstract_sql');
	sql_query("DROP TABLE spip_visites_auteurs");
	sql_query("DROP_TABLE spip_visites_articles_auteurs");
	sql_query("DROP TABLE spip_journal");
	effacer_meta($nom_meta_base_version);
}

?>
