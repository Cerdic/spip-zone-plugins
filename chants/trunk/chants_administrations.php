<?php
if (!defined('_ECRIRE_INC_VERSION')) return;


function chants_upgrade($nom_meta_base_version, $version_cible){

	$maj = array();
	$maj['create'] = array(
		array('maj_tables', array('spip_chants', 'spip_chants_liens'))
	);
	// champ : statut, lang, id_trad, langue_choisie
	$maj['0.1.0'] = array(
		array('maj_tables', array('spip_chants')),
		array('sql_updateq', 'spip_chants', array('statut'=>'publie'))
	);

	$maj['0.2.0'] = array(
		array('maj_tables', array('spip_chants')),
	);
	// correction du bug de tables_auxiliaires de spip_chants_liens
	$maj['0.5.0'] = array(
		array('maj_tables', array('spip_chants_liens')),
	);
	// ajout de la tonalité, alias (autre titre), ligne principale.
	// renommage de nombre_hymne en numéro : cela correspond au numéro d'apparition du chant.
	$maj['0.6.1'] = array(
		array('maj_tables', array('spip_chants')),
		array('maj_tables', array('spip_chants_liens'))
	);
	$maj['0.6.3'] = array(
		array('maj_tables', array('spip_chants_liens'))
	);
	

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

function chants_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_chants");
	sql_drop_table("spip_chants_liens");
	effacer_meta($nom_meta_base_version);
}

?>