<?php
/**
 * Déclarations relatives à la base de données
 *
 * @plugin     Immeubles
 * @copyright  2018 - 2019
 * @author     Rainer Müller
 * @licence    GNU/GPL v3
 * @package    SPIP\Location_objets\Pipelines
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function location_immeubles_declarer_champs_extras($champs = array()) {
	$champs['spip_objets_locations']['personnes'] = array(
		'saisie' => 'input',//Type du champ (voir plugin Saisies)
		'options' => array(
			'nom' => 'personnes',
			'label' => _T('location_immeubles:champ_personnes_label'),
			'sql' => 'bigint(21) NOT NULL',
			'defaut' => '1',// Valeur par défaut
		),
	);
	return $champs;
}