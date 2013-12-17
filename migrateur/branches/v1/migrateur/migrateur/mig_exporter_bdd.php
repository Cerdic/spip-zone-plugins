<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Exporter la base de données source
**/
function migrateur_mig_exporter_bdd() {

	$user = MIGRATEUR_SOURCE_SQL_USER;
	$pass = MIGRATEUR_SOURCE_SQL_PASS;
	$bdd  = MIGRATEUR_SOURCE_SQL_BDD;
	$dest = MIGRATEUR_DESTINATION_DIR . 'tmp/dump/';
	sous_repertoire(_DIR_TMP . 'dump');

	$source_sql = MIGRATEUR_NOM_EXPORT_SQL;

	$output = "";
	exec("rm $dest$source_sql;");
	migrateur_log("Exécution de mysqldump…");
	exec("mysqldump -u $user --password=$pass $bdd > $dest$source_sql", $output, $err);
	if ($err) {
		migrateur_log("! Erreurs survenues : $err");
	} else {
		$taille = filesize($dest . $source_sql);
		include_spip('inc/filtres');
		migrateur_log("> Fichier : " . $dest . $source_sql);
		migrateur_log("> Taille : " . taille_en_octets($taille));
	}
}
