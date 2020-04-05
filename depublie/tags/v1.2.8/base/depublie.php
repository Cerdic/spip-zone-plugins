<?php
/**
 * Déclarations des pipelines
 *
 * @plugin     Depublie
 * @copyright  2014
 * @licence    GNU/GPL
 * @package    SPIP\Depublier\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Déclaration des alias de tables et filtres automatiques de champs
 *
 * @pipeline declarer_tables_interfaces
 * @param array $interfaces
 *     Déclarations d'interface pour le compilateur
 * @return array
 *     Déclarations d'interface pour le compilateur
 */
function depublie_declarer_tables_interfaces($interfaces) {

	$interfaces['table_des_tables']['depublies'] = 'depublies';
	$interfaces['tables_jointures']['spip_articles'][] = 'depublies';
	$interfaces['tables_jointures']['spip_depublies'][] = 'articles';

	return $interfaces;
}

/**
 * Insertion dans le pipeline declarer_tables_auxiliaires (SPIP)
 * Déclarer la table auxiliaire spip_depublies pour le compilateur
 * 
 * @pipeline declarer_tables_auxiliaires
 * @param array $tables_auxiliaires
 * 		Déclarations des tables pour le compilateur
 * @return array
 * 		Déclarations des tables pour le compilateur
 */
function depublie_declarer_tables_auxiliaires($tables_auxiliaires){	
	$spip_depublies = array(
		"objet"		 => "varchar(21) DEFAULT '' NOT NULL",
		"id_objet" 	 => "BIGINT(21) NOT NULL DEFAULT '0'",
		"statut"	 => "text DEFAULT '' NOT NULL",
		"date_depublie"	 => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'",
		"maj"            => "TIMESTAMP"
	);
	$spip_depublies_key = array(
		"PRIMARY KEY" => "objet, id_objet",
		"KEY objet" => "objet",
		"KEY id_objet" => "id_objet"
	);

	$tables_auxiliaires['spip_depublies'] = array(
		'field' => &$spip_depublies,
		'key' => &$spip_depublies_key
	);

	return $tables_auxiliaires;
}


