<?php
#---------------------------------------------------#
#  Plugin  : Pages                                  #
#  Auteur  : RastaPopoulos                          #
#  Licence : GPL                                    #
#--------------------------------------------------------------- -#
#  Documentation : http://www.spip-contrib.net/Plugin-Pages       #
#-----------------------------------------------------------------#

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/meta');


function pages_upgrade($nom_meta_base_version,$version_cible){
	$current_version = 0.0;

	if ((!isset($GLOBALS['meta'][$nom_meta_base_version]) )
			|| (($current_version = $GLOBALS['meta'][$nom_meta_base_version])!=$version_cible)) {
		if ($current_version==0.0) {
			include_spip('base/create');
			maj_tables('spip_articles');
			ecrire_meta($nom_meta_base_version, $current_version=$version_cible, 'non');
		}
		// remise a jour du nouveau version base pour les anciennes installations.
		if ($current_version<1.0){
			ecrire_meta($nom_meta_base_version, $current_version=$version_cible, 'non');				
		}
	}
}

// Supprimer la colonne 'page' du plugin
function pages_vider_tables($nom_meta_base_version) {
	sql_alter("TABLE spip_articles DROP page");
	effacer_meta($nom_meta_base_version);
}



?>
