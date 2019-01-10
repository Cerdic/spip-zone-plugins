<?php
/**
 * Déclarations relatives à la base de données
 *
 * @plugin     Prix Objets
 * @copyright  2012 - 2019
 * @author     Rainer Müller
 * @licence    GNU/GPL
 * @package    SPIP\Promotions_commandes\Pipelines
 */
if (! defined("_ECRIRE_INC_VERSION"))
	return;

/**
 * Déclaration des alias de tables et filtres automatiques de champs
 *
 * @pipeline declarer_tables_interfaces
 * @param array $interfaces
 *     Déclarations d'interface pour le compilateur
 * @return array
 *     Déclarations d'interface pour le compilateur
 */
function prix_objets_declarer_tables_interfaces($tables_interfaces) {
	$tables_interfaces['table_des_tables']['prix_objets'] = 'prix_objets';

	return $tables_interfaces;
}

/**
 * Déclaration des tables principales.
 *
 * @pipeline declarer_tables_interfaces
 * @param array $tables_principales
 *     Déclarations des tables principales pour le compilateur
 * @return array
 *     Déclarations des tables principales pour le compilateur
 */
function prix_objets_declarer_tables_principales($tables_principales) {
	$spip_prix_objets = array(
		"id_prix_objet" => "bigint(21) NOT NULL",
		"id_prix_objet_source" => "bigint(21) NOT NULL",
		'objet' => 'varchar(25) not null default ""',
		"id_objet" => "bigint(21) NOT NULL",
		"titre" => "varchar(255)  DEFAULT '' NOT NULL",
		"code_devise" => "varchar(3) NOT NULL",
		"prix_ht" => "decimal(15,2) NOT NULL DEFAULT '0.00'",
		"prix" => "decimal(15,2) NOT NULL DEFAULT '0.00'",
		"prix_total" => "int(1) NOT NULL DEFAULT '0'",
		"taxe" => "varchar(10)  DEFAULT '' NOT NULL",
		'extension' => 'varchar(50) not null default ""',
		"id_extension" => "bigint(21) NOT NULL",
		"rang_lien" => "int(4) NOT NULL DEFAULT '0'",
	);

	$spip_prix_objets_key = array(
		"PRIMARY KEY" => "id_prix_objet",
		"KEY id_objet" => "id_prix_objet_source,id_objet,objet,id_extension,extension"
	);

	$spip_prix_objets_join = array(
		"id_prix_objet" => "id_prix_objet",
		"id_objet" => "id_objet",
		"id_objet" => "id_article"
	);

	$tables_principales['spip_prix_objets'] = array(
		'field' => &$spip_prix_objets,
		'key' => &$spip_prix_objets_key,
		'join' => &$spip_prix_objets_join
	);

	return $tables_principales;
}

/**
 * Actualise la bd
 *
 * @param string $version_cible
 *        	la version de la bd
 */
function po_upgrade($version_cible) {

	// Remplace les champs "id_EXTENSION" par id_extension extension.
	if ($version_cible == '2.0.0') {
		$trouver_table = charger_fonction('trouver_table', 'base');
		$table = 'spip_prix_objets';
		$decription_table = $trouver_table($table);
		include_spip('inc/prix_objets');

		$extensions = array(
			'declinaison',
			'po_periode'
		);

		foreach ($extensions as $extension) {
			if ($identifiant_extension = id_table_objet($extension) and
				isset($decription_table['field'][$identifiant_extension])) {
					$sql = sql_select('*', 'spip_prix_objets',
						$identifiant_extension . '>0');

					while ($data = sql_fetch($sql)) {
						sql_insertq('spip_prix_objets',
							array(
								'id_prix_objet_source' => $data['id_prix_objet'],
								'extension' => $extension,
								'id_extension' => $data[$identifiant_extension],
								'objet' => $data['objet'],
								'id_objet' => $data['id_objet'],
								'titre' => extraire_multi(
									supprimer_numero(
										generer_info_entite(
											$data[$identifiant_extension],
											$extension,
											'titre', '*'))),
								'prix' => $data['prix_ht']
							));
					}
					sql_alter("TABLE $table DROP COLUMN  $identifiant_extension");
				}
		}
	}
}
