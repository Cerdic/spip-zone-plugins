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
 * Ajouter un traitement automatique sur une balise
 *
 * On peut restreindre l'application du traitement au balises appelées dans un
 * type de boucle via le paramètre optionnel $table.
 *
 * @param array $interfaces
 *    Les interfaces du pipeline declarer_tables_interfaces
 * @param string $traitement
 *    Un format comme pour sprintf, dans lequel le compilateur passera la valeur de la balise
 * @param string $balise
 *    Le nom de la balise à laquelle on veut appliquer le traitement
 * @param string $table (optionnel)
 *    Un type de boucle auquel on veut restreindre le traitement.
 */
function ajouter_traitement_automatique($interfaces, $traitement, $balise, $table = 0) {

	$table_traitements = $interfaces['table_des_traitements'];

	if (! isset($table_traitements[$balise])) {
		$table_traitements[$balise] = array();
	}

	/* On essaie d'être tolérant sur le nom de la table */
	if ($table) {
		include_spip('base/objets');
		$table = table_objet($table);
	}

	if (isset($table_traitements[$balise][$table])) {
		$traitement_existant = $table_traitements[$balise][$table];
	}

	if (!isset($traitement_existant) or (! $traitement_existant)) {
		$traitement_existant = '%s';
	}

	$interfaces['table_des_traitements'][$balise][$table] = sprintf($traitement, $traitement_existant);

	return $interfaces;
}

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

	$interfaces = ajouter_traitement_automatique(
		$interfaces,
		'massicoter_document(%s)',
		'FICHIER',
		'documents'
	);

	$interfaces = ajouter_traitement_automatique(
		$interfaces,
		'massicoter_logo_document(%s, $Pile[1])',
		'LOGO_DOCUMENT'
	);

	$interfaces = ajouter_traitement_automatique(
		$interfaces,
		'massicoter_document(%s)',
		'URL_DOCUMENT',
		'documents'
	);

	/* On traîte aussi les balises #HAUTEUR et #LARGEUR des documents */
	$interfaces = ajouter_traitement_automatique(
		$interfaces,
		'massicoter_largeur(%s, $Pile[1])',
		'LARGEUR',
		'documents'
	);

	$interfaces = ajouter_traitement_automatique(
		$interfaces,
		'massicoter_hauteur(%s, $Pile[1])',
		'HAUTEUR',
		'documents'
	);

	/* Pour chaque objet éditorial existant, ajouter un traitement sur
	   les logos */
	if (isset($GLOBALS['spip_connect_version'])) {
		foreach (lister_tables_objets_sql() as $table => $valeurs) {
			if ($table !== 'spip_documents') {
				$interfaces = ajouter_traitement_automatique(
					$interfaces,
					'massicoter_logo(%s, \''.objet_type($table).'\', $Pile[1][\''.id_table_objet($table).'\'])',
					strtoupper('LOGO_'.objet_type($table))
				);

				$interfaces = ajouter_traitement_automatique(
					$interfaces,
					'massicoter_logo(%s, \''.objet_type($table).'\', $Pile[1][\''.id_table_objet($table).'\'])',
					strtoupper('LOGO_'.objet_type($table)) . '_NORMAL'
				);

				$interfaces = ajouter_traitement_automatique(
					$interfaces,
					'massicoter_logo(%s, \''.objet_type($table).'\', $Pile[1][\''.id_table_objet($table).'\'], \'logo_survol\')',
					strtoupper('LOGO_'.objet_type($table)) . '_SURVOL'
				);
			}
		}
	}

	/* sans oublier #LOGO_ARTICLE_RUBRIQUE… */
	$interfaces = ajouter_traitement_automatique(
		$interfaces,
		'massicoter_logo(%s,null,null,null,$Pile[0])',
		'LOGO_ARTICLE_RUBRIQUE'
	);

	/* …ni les #LOGO_SITE_SPIP ! */
	$interfaces = ajouter_traitement_automatique(
		$interfaces,
		'massicoter_logo(%s,"site","0","",$Pile[0])',
		'LOGO_SITE_SPIP'
	);
	$interfaces = ajouter_traitement_automatique(
		$interfaces,
		'massicoter_logo(%s,"site","0","logo_survol",$Pile[0])',
		'LOGO_SITE_SPIP_SURVOL'
	);

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
			'vu'			 => "VARCHAR(6) DEFAULT 'non' NOT NULL",
			'role'			 => "VARCHAR(30) DEFAULT '' NOT NULL"
		),
		'key' => array(
			'PRIMARY KEY'		 => 'id_massicotage,id_objet,objet,role',
			'KEY id_massicotage' => 'id_massicotage',
		),
	);

	return $tables_auxiliaires;
}
