<?php

/**
 * Base pour petitcochon
 *
 * @plugin     petitcochon
 * @copyright  2016
 * @author     cyp
 * @licence    GNU/GPL
 * @package    SPIP\petitcochon\base
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Interfaces des tables petitcochon pour le compilateur
 *
 * @param array $interfaces
 * @return array
 */
function petitcochon_declarer_tables_interfaces($interfaces) {
	$interfaces['table_des_tables']['petitcochon'] = 'petitcochon';
	$interfaces['table_des_traitements']['POIDS'][] = 'number_format(%s,3)';
	return $interfaces;
}

function petitcochon_declarer_tables_objets_sql($tables) {

	$tables['spip_petitcochon'] = array(
		'texte_retour' => 'icone_retour',
		'texte_objets' => 'petitcochon:petitcochon',
		'texte_objet' => 'petitcochon:petitcochon',
		'texte_modifier' => 'petitcochon:icone_modifier_petitcochon',
		'texte_creer' => 'petitcochon:icone_nouveau_petitcochon',
		'principale' => 'oui',
		'field'=> array(
			'id_petitcochon' => 'bigint(21) NOT NULL',
			'nom' => 'varchar(255) NOT NULL',
			'poids'	=> 'varchar(255) NOT NULL',
			'date_modif' => "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL",
			'maj'	=> 'TIMESTAMP'
		),
		'key' => array(
			'PRIMARY KEY' => 'id_petitcochon',
		),
		'rechercher_champs' => array(
			'nom' => 5, 'poids' => 2
		),
		'champs_editables'  => array('nom','poids'),
		'champs_versionnes' => array('nom','poids'),
	);

	return $tables;
}
