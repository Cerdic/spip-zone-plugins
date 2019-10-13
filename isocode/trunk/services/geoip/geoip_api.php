<?php
/**
 * Ce fichier contient l'ensemble des constantes et fonctions implémentant le service GEOIP.
 *
 * @package SPIP\ISOCODE\SERVICES\GEOIP
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


$GLOBALS['isocode']['geoip']['tables'] = array(
	'geoipcontinents' => array(
		'basic_fields' => array(
			'Code' => 'code',
			'fr'   => 'label_fr',
			'en'   => 'label_en',
		),
		'populating'   => 'file_json',
		'extension'    => '.json',
	)
);

// ----------------------------------------------------------------------------
// ---------------- API du service GEOIP - Actions principales ----------------
// ----------------------------------------------------------------------------

