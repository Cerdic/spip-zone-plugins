<?php
/**
 * Base pour owncloud
 *
 * @plugin     owncloud
 * @copyright  2016
 * @author     cyp
 * @licence    GNU/GPL
 * @package    SPIP\owncloud\base
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Interfaces des tables owncloud pour le compilateur
 *
 * @param array $interfaces
 * @return array
 */
function owncloud_declarer_tables_interfaces($interfaces) {
	$interfaces['table_des_tables']['ownclouds'] = 'ownclouds';
	
	return $interfaces;
}

function owncloud_declarer_tables_objets_sql($tables) {

	$tables['spip_ownclouds'] = array(
		'type' 			=> 'owncloud',
		'texte_retour' 		=> 'icone_retour',
		'texte_objets' 		=> 'owncloud:owncloud',
		'texte_objet' 		=> 'owncloud:owncloud',
		'texte_modifier' 	=> 'owncloud:icone_modifier_owncloud',
		'texte_creer' 		=> 'owncloud:icone_nouveau_owncloud',
		'titre' 			=> 'titre',
		'principale' 		=> 'oui',
		'field'=> array(
			'id_owncloud' 	=> 'bigint(21) NOT NULL',
			'titre' 		=> "varchar(255) NOT NULL default 'NUL'",
			'md5' 			=> "varchar(255) NOT NULL default 'NUL'",
			'date_modif' 	=> "datetime NOT NULL default '0000-00-00 00:00:00'",
			'maj'			=> 'TIMESTAMP'
		),
		'key' => array(
			'PRIMARY KEY' 	=> 'id_owncloud',
		),
		'join' => array(
			'id_owncloud' => 'id_owncloud'
		),
		'champs_editables'  => array('titre'),
	);

	return $tables;
}

function owncloud_declarer_champs_extras($champs = array()) {
	$champs['spip_documents']['md5'] = array(
		'saisie' => 'input',
		'options' => array(
			'nom' => 'md5',
			'label' => _T('owncloud:md5'),
			'sql' => "varchar(255) NOT NULL DEFAULT ''",
			'defaut' => '',
			'restrictions'=>array(	'voir' 		=> array('auteur'=>''),
									'modifier'	=> array('auteur'=>'0minirezo'))),
		'verifier' => array());

	return $champs;
}
