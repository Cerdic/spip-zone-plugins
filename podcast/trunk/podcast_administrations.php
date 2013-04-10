<?php
/**
* Plugin Podcast
* par kent1
*
* Copyright (c) 2010-2012
* Logiciel libre distribué sous licence GNU/GPL.
*
* Installation
*
**/
if (!defined("_ECRIRE_INC_VERSION")) return;

function podcast_upgrade($nom_meta_base_version,$version_cible){
	$maj = array();
	
	$maj['create'] = array(
		array('maj_tables',array('spip_documents'))
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

/**
 * Fonction de désinstallation
 * - on vire la meta d'installation
 * - on vire la meta de configuration
 */
function podcast_vider_tables($nom_meta_base_version) {
	effacer_meta($nom_meta_base_version);
	effacer_meta('podcast');
}
?>