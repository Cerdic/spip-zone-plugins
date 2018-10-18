<?php
/**
 * Fichier gérant l'installation et désinstallation du plugin Continents
 *
 * @plugin     Continents
 * @copyright  2013 - 2018
 * @author     Rainer Müller
 * @licence    GNU/GPL
 * @package    SPIP\Continents\Installation
 */
if (!defined('_ECRIRE_INC_VERSION'))
	return;

function continents_upgrade($nom_meta_base_version, $version_cible) {
	$maj = array();

	include_spip('base/upgrade');
	include_spip('base/continents_peupler');
	$maj['create'] = array(
		array(
			'maj_tables',
			array(
				'spip_pays',
				'spip_continents'
			)
		),
		array(
			'peupler_base_continents'
		),
		array(
			'inserer_table_pays'
		)
	);
	$maj['1.1.0'] = array(
		array(
			'maj_tables',
			array(
				'spip_continents'
			)
		),
		array(
			'inserer_codes_iso'
		)
	);

	maj_plugin($nom_meta_base_version, $version_cible, $maj);
}

function continents_vider_tables($nom_meta_base_version) {
	sql_drop_table("spip_continents");

	effacer_meta($nom_meta_base_version);
}
