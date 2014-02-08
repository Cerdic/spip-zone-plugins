<?php

include_spip('base/create');

function relecture_upgrade($nom_meta_base_version, $version_cible){

	$maj = array();
	$defaut_config = array(
		'autoriser_tous_relecteurs' => 'non',
	);

	$maj['create'] = array(
		array('maj_tables', array('spip_relectures', 'spip_commentaires')),
		array('ecrire_config', 'relecture', $defaut_config)
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

function relecture_vider_tables($nom_meta_base_version) {

	// Supprimer les tables creees par le plugin
	sql_drop_table('spip_relectures');
	sql_drop_table('spip_commentaires');

	// Supprimer les relecteurs (enregistrements dans auteurs_liens)
	sql_delete('spip_auteurs_liens', 'objet=' . sql_quote('relecture'));

	// on efface la meta de configuration du plugin
	effacer_meta('relecture');

	// Supprimer la meta du plugin
	effacer_meta($nom_meta_base_version);
}

?>
