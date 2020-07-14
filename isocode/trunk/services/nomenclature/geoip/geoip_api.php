<?php
/**
 * Ce fichier contient l'ensemble des constantes et fonctions implémentant le service GEOIP.
 *
 * @package SPIP\ISOCODE\SERVICES\GEOIP
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


$GLOBALS['isocode']['geoip'] = array(
	'geoipcontinents' => array(
		'groupe'       => 'geographie',
		'basic_fields' => array(
			'Code'     => 'code',
			'code_num' => 'code_num',
			'fr'       => 'label_fr',
			'en'       => 'label_en',
		),
		'unused_fields' => array(
			'label_fr' => '',
			'label_en' => '',
		),
		'label_field'  => true,
		'populating'   => 'file_json',
		'node'         => '',
		'extension'    => '.json',
	)
);

// ----------------------------------------------------------------------------
// ---------------- API du service GEOIP - Actions principales ----------------
// ----------------------------------------------------------------------------
