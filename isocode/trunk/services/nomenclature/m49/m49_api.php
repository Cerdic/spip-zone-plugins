<?php
/**
 * Ce fichier contient l'ensemble des constantes et fonctions implémentant le service M49.
 *
 * @package SPIP\ISOCODE\SERVICES\M49
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


$GLOBALS['isocode']['m49'] = array(
	'm49regions' => array(
		'groupe'       => 'geographie',
		'basic_fields' => array(
			'code_num' => 'code_num',
			'parent'   => 'parent',
			'category' => 'category',
			'label_fr' => 'label_fr',
			'label_en' => 'label_en',
		),
		'unused_fields' => array(
			'label_fr' => '',
			'label_en' => '',
		),
		'label_field'  => true,
		'populating'   => 'file_csv',
		'delimiter'    => ';',
		'extension'    => '.txt',
	)
);

// ----------------------------------------------------------------------------
// ---------------- API du service M49 - Actions principales ----------------
// ----------------------------------------------------------------------------
