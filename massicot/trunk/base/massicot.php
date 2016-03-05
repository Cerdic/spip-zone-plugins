<?php
/**
 * Définitions des tables du plugin Massicot
 *
 * @plugin	   Massicot
 * @copyright  2015
 * @author	   Michel @ Vertige ASBL
 * @licence	   GNU/GPL
 */

/**
 * Déclaration des alias de tables et filtres automatiques de champs
 *
 * @pipeline declarer_tables_interfaces
 * @param array $interfaces
 *	   Déclarations d'interface pour le compilateur
 * @return array
 *	   Déclarations d'interface pour le compilateur
 */
function massicot_declarer_tables_interfaces($interfaces) {

	if ((! isset($interfaces['table_des_traitements']['FICHIER']['documents']))
		or is_null($interfaces['table_des_traitements']['FICHIER']['documents'])) {
		$interfaces['table_des_traitements']['FICHIER']['documents'] = '%s';
	}

	$interfaces['table_des_traitements']['FICHIER']['documents'] =
	  'massicoter_document(' . $interfaces['table_des_traitements']['FICHIER']['documents'] . ')';

	$interfaces['table_des_traitements']['LOGO_DOCUMENT'][] =
	  'massicoter_logo_document(%s, $connect, $Pile[1])';

	if (! isset($interfaces['table_des_traitements']['URL_DOCUMENT'])) {
		$interfaces['table_des_traitements']['URL_DOCUMENT'] = array();
	}

	if ((! isset($interfaces['table_des_traitements']['URL_DOCUMENT']['documents'])) or
		is_null($interfaces['table_des_traitements']['URL_DOCUMENT']['documents'])) {

		$interfaces['table_des_traitements']['URL_DOCUMENT']['documents'] = '%s';
	}

	$interfaces['table_des_traitements']['URL_DOCUMENT']['documents'] =
	  'massicoter_document(' . $interfaces['table_des_traitements']['URL_DOCUMENT']['documents'] . ')';

	/* On traîte aussi les balises #HAUTEUR et #LARGEUR des documents */
	$interfaces['table_des_traitements']['LARGEUR']['documents'] =
	  'massicoter_largeur(%s, $connect, $Pile[1])';
	$interfaces['table_des_traitements']['HAUTEUR']['documents'] =
	  'massicoter_hauteur(%s, $connect, $Pile[1])';

	/* Pour chaque objet éditorial existant, ajouter un traitement sur
	   les logos */
	foreach (lister_tables_objets_sql() as $table => $valeurs) {

		if ($table !== 'spip_documents') {

			$logo_type = strtoupper('LOGO_'.objet_type($table));

			if (!empty($interfaces['table_des_traitements'][$logo_type])) {
				$interfaces['table_des_traitements'][$logo_type][0]
					= 'massicoter_logo('.$interfaces['table_des_traitements'][$logo_type][0]
					. ', $connect'
					. ', '.objet_type($table)
					.', $Pile[1][\''.id_table_objet($table)
					.'\'])';
			} else {
				$interfaces['table_des_traitements'][$logo_type][]
					= 'massicoter_logo(%s, $connect, '.objet_type($table)
					. ', $Pile[1][\''.id_table_objet($table).'\'])';
			}
		}
	}

	return $interfaces;
}

/**
 * Création de la table spip_massicotages
 *
 * @pipeline declarer_tables_principales
 * @param  array $tables  Tables principales
 * @return array		  Tables principales
 */
function massicot_declarer_tables_principales($tables_principales) {

	$tables_principales['spip_massicotages'] = array(
		'field' => array(
			'id_massicotage' => 'bigint(21) NOT NULL',
			'traitements'	 => 'text NOT NULL',
		),
		'key' => array(
			'PRIMARY KEY' => 'id_massicotage',
		),
	);

	$tables_principales['spip_massicotages']['tables_jointures'][] = 'spip_massicotages_liens';

	return $tables_principales;
}

/**
 * Création de la table spip_massicotages_liens
 *
 * @pipeline declarer_tables_auxiliaires
 * @param  array $tables  Tables auxiliaires
 * @return array		  Tables auxiliaires
 */
function massicot_declarer_tables_auxiliaires($tables_auxiliaires) {

	$tables_auxiliaires['spip_massicotages_liens'] = array(
		'field' => array(
			'id_massicotage' => "bigint(21) DEFAULT '0' NOT NULL",
			'id_objet'		 => "bigint(21) DEFAULT '0' NOT NULL",
			'objet'			 => "VARCHAR(25) DEFAULT '' NOT NULL",
			'vu'			 => "VARCHAR(6) DEFAULT 'non' NOT NULL"
		),
		'key' => array(
			'PRIMARY KEY'		 => 'id_massicotage,id_objet,objet',
			'KEY id_massicotage' => 'id_massicotage',
		),
	);

	return $tables_auxiliaires;
}
