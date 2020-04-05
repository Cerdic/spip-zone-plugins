<?php
#---------------------------------------------------#
#  Plugin  : Big Brother                            #
#  Auteur  : RastaPopoulos                          #
#  Licence : GPL                                    #
#------------------------------------------------- -#

if (!defined('_ECRIRE_INC_VERSION')) return;

// La fonction de base appelée par le gestionnaire de plugins
function bigbrother_upgrade($nom_meta_base_version,$version_cible){
	$maj = array();
	$maj['create'] = array(
		array('maj_tables',array('spip_journal', 'spip_visites_auteurs', 'spip_visites_articles_auteurs')),
	);
	
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

// Supprimer les tables du plugin
function bigbrother_vider_tables($nom_meta_base_version) {
	sql_drop_table('spip_visites_auteurs');
	sql_drop_table('spip_visites_articles_auteurs');
	sql_drop_table('spip_journal');
	effacer_meta($nom_meta_base_version);
}

?>
