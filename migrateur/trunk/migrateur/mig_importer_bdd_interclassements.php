<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

// partage de fonctions
include_spip('migrateur/mig_transferer_bdd');

/**
 * Importer la base de données dont la structure a été passée en utf8
 *
 * @link http://zzz.rezo.net/Reparer-le-charset-d-une-base-SPIP.html
**/
function migrateur_mig_importer_bdd_interclassements() {

	// vider toutes les tables avant insertion
	migrateur_supprimer_toutes_tables_sql();

	migrateur_log("Structure");
	migrateur_copier_la_bdd('export_struct.sql');
	migrateur_log("Données");
	migrateur_copier_la_bdd('export_data.sql');
	migrateur_log("Définir les metas");
	sql_query("
		REPLACE spip_meta (nom,valeur,impt,maj) VALUES
		 ('charset_sql_base', 'utf8', 'oui', NOW()),
		 ('charset_collation_sql_base', 'utf8_general_ci', 'oui', NOW()),
		 ('charset_sql_connexion', 'utf8', 'oui', NOW()),
		 ('charset', 'utf-8', 'oui', NOW());
	");
}
