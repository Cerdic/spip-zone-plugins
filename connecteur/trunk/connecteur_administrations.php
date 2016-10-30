<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function connecteur_upgrade($nom_meta_base_version, $version_cible) {

	$maj = array();
	$maj['create'] = array(array('maj_tables', array('spip_connecteur')));
	$maj['1.0.1'] = array(array('maj_tables', array('spip_connecteur')));

	$maj['1.0.2'] = array(
		array('sql_alter', 'TABLE spip_connecteur DROP COLUMN expire')
	);

	$maj['1.0.3'] = array(
		array('sql_alter', 'TABLE spip_connecteur MODIFY token blob NOT NULL')
	);

	$maj['1.0.4'] = array(array('maj_tables', array('spip_connecteur')));

	$maj['1.0.5'] = array(array('maj_tables', array('spip_connecteur')));

	$maj['1.0.9'] = array(
		array('sql_alter', 'TABLE spip_connecteur CHANGE id_connecteur id_connecteur bigint(21) NOT NULL FIRST'),
		array('sql_alter', 'TABLE spip_connecteur DROP primary key'),
		array('sql_alter', 'TABLE spip_connecteur ADD INDEX `id_connecteur` (id_connecteur), MODIFY id_connecteur bigint(21) auto_increment')
	);

	$maj['1.0.12'] = array(
		array('connecteur_actualiser_signature')
	);

	$maj['1.0.13'] = array(
		array('sql_alter', 'TABLE spip_connecteur CHANGE id_connecteur id_connecteur BIGINT(21) NOT NULL AUTO_INCREMENT')
	);


	$maj['1.0.14'] = array(
		array('sql_alter', 'TABLE spip_connecteur DROP COLUMN signature')
	);

	$maj['1.0.15'] = array(
		array('sql_alter', 'TABLE spip_connecteur DROP COLUMN id_connecteur')
	);

	include_spip('base/upgrade');
	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

function connecteur_vider_tables($nom_meta_base_version) {
	sql_drop_table('spip_connecteur');
	effacer_meta($nom_meta_base_version);
}


/**
 * Cette fonction créer les signatures de token qui sont absente de la base de donnée
 *
 * @access public
 */
function connecteur_actualiser_signature() {

	// On sélectionne les tokens sans signature
	$tokens = sql_allfetsel('*', 'spip_connecteur', "signature = ''");
	include_spip('inc/securiser_action');
	spip_log($tokens, 'connecteur');
	foreach ($tokens as $token) {
		sql_updateq(
			'spip_connecteur',
			array('signature' => calculer_cle_action($token['token'])),
			'id_connecteur='.intval($token['id_connecteur'])
		);
	}
}
