<?php
/**
 * Ce fichier contient l'ensemble des constantes et fonctions implémentant le service M49.
 *
 * @package SPIP\ISOCODE\SERVICES\M49
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


$GLOBALS['isocode']['m49']['tables'] = array(
	'm49regions' => array(
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

function m49regions_completer_enregistrement($enregistrement, $config) {

	// Il s'agit uniquement de supprimer les labels ayant permis de calculer le nom multi.
	unset($enregistrement['label_fr']);
	unset($enregistrement['label_en']);

	return $enregistrement;
}

