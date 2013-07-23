<?php
#---------------------------------------------------#
#  Plugin  : E-Learning                             #
#  Auteur  : RastaPopoulos                          #
#  Licence : GPL                                    #
#--------------------------------------------------------------- -#
#  Documentation : http://www.spip-contrib.net/Plugin-E-learning  #
#-----------------------------------------------------------------#

// La fonction de base appelée par le gestionnaire de plugins
function elearning_upgrade($nom_meta_version_base, $version_cible){
	include_spip('inc/meta');
	include_spip('base/abstract_sql');
	
	$current_version = 0.0;
	
	if (
		!isset($GLOBALS['meta'][$nom_meta_version_base])
		or
		(($current_version = $GLOBALS['meta'][$nom_meta_version_base]) != $version_cible)
	){
		if (version_compare($current_version, '0.0', '<=')){
			ecrire_meta($nom_meta_version_base, $current_version=$version_cible, 'non');
		}
	}
}

// Supprimer les tables du plugin
function elearning_vider_tables($nom_meta_version_base) {
	//include_spip('base/abstract_sql');
	/* Blabla effacer les tables */
	effacer_meta($nom_meta_version_base);
}

?>
