<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function prix_objets_declarer_tables_interfaces($tables_interfaces){

		$tables_interfaces['table_des_tables']['prix_objets'] = 'prix_objets';

		return $tables_interfaces;
}

function prix_objets_declarer_tables_principales($tables_principales){
	$spip_prix_objets = array(
		"id_prix_objet" 	=> "bigint(21) NOT NULL",
		'objet' => 'varchar(25) not null default ""',
		"id_objet" 	=> "bigint(21) NOT NULL",
		"titre"   => "varchar(255)  DEFAULT '' NOT NULL",
		"reference"   => "varchar(255)  DEFAULT '' NOT NULL",
		"code_devise" 	=> "varchar(3) NOT NULL",
		"prix_ht" 		=> "decimal(15,2) NOT NULL DEFAULT '0.00'",
		"prix"       => "decimal(15,2) NOT NULL DEFAULT '0.00'",
		"taxe"   => "varchar(10)  DEFAULT '' NOT NULL",
		'extension' => 'varchar(50) not null default ""',
		"id_extension" 	=> "bigint(21) NOT NULL",
		);

	$spip_prix_objets_key = array(
		"PRIMARY KEY" 	=> "id_prix_objet",
		"KEY id_objet"	=> "id_objet,objet,id_extension,extension",
		);

	$spip_prix_objets_join = array(
		"id_prix_objet"	=> "id_prix_objet",
		"id_objet"	=> "id_objet",
		"id_objet"	=> "id_article",
		);

	$tables_principales['spip_prix_objets'] = array(
		'field' => &$spip_prix_objets,
		'key' => &$spip_prix_objets_key,
		'join' => &$spip_prix_objets_join
	);

	return $tables_principales;
}

function po_upgrade($version_cible) {

	// Remplace les champs "id_EXTENSION" par id_extension extension.
	if ($version_cible == '2.0.3') {
		$trouver_table = charger_fonction('trouver_table', 'base');
		$table = 'spip_prix_objets';
		$decription_table = $trouver_table($table);
		include_spip('inc/prix_objets');
		$extensions_declaration = prix_objets_extensions_declaration();
		$objets = array_keys($extensions_declaration);

		foreach ($objets as $objet) {
			if ($identifiant_extension = id_table_objet($objet) and
				isset($decription_table['field'][$identifiant_extension])
				) {
					$sql = sql_select($identifiant_extension . ',id_prix_objet', 'spip_prix_objets', $identifiant_extension . '>0');

					while ($data = sql_fetch($sql)) {
						sql_updateq(
							'spip_prix_objets',
							array(
								'extension' => $objet,
								'id_extension' => $data[$identifiant_extension],
							),
							'id_prix_objet=' . $data['id_prix_objet']
						);
					}
					sql_alter("TABLE $table DROP COLUMN  $identifiant_extension");
				}
		}

	}
}

