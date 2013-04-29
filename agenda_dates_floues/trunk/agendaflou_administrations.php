<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Installation/maj des tables
 *
 * @param string $nom_meta_base_version
 * @param string $version_cible
 */
function agendaflou_upgrade($nom_meta_base_version, $version_cible){
	$maj = array();
	$maj['create'] = array(
		array('maj_tables', array('spip_evenements')),
	);
	
	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

function agendaflou_vider_tables($nom_meta_base_version) {
	sql_alter('table spip_evenements drop column date_debut_floue');
	sql_alter('table spip_evenements drop column date_fin_floue');
	effacer_meta($nom_meta_base_version);
}
