<?php
/**
 * Ce fichier contient l'ensemble des constantes et fonctions implémentant le "service" ALTERNATES.
 * ALTERNATES n'est pas un service à proprement parlé, c'est un mécanisme interne à Nomenclatures de
 * créer une table annexe des codes alternatifs au code ISO-3166-2 des pays et autres subdivisions géographiques.
 *
 * @package SPIP\ISOCODE\SERVICES\ALTERNATES
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

$GLOBALS['isocode']['alternates'] = array(
	'iso3166alternates' => array(
		'groupe'       => 'geographie',
		'basic_fields' => array(
			'iso'  => 'code_3166_2',
			'type' => 'type_alter',
			'code' => 'code_alter',
		),
		'label_field'  => false,
		'populating'   => 'file_csv',
		'delimiter'    => ';',
		'multiple'     => true,
		'extension'    => '.csv'
	)
);

// ----------------------------------------------------------------------------
// ------------- API du service ALTERNATES - Actions principales --------------
// ----------------------------------------------------------------------------
