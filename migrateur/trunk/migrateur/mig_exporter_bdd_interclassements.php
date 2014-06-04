<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Exporter la base de données en cours (destination) et
 * passer les interclassements en utf8 au lieu de iso
 *
 * @link http://zzz.rezo.net/Reparer-le-charset-d-une-base-SPIP.html
**/
function migrateur_mig_exporter_bdd_interclassements() {

	$user = MIGRATEUR_DESTINATION_SQL_USER;
	$pass = MIGRATEUR_DESTINATION_SQL_PASS;
	$bdd  = MIGRATEUR_DESTINATION_SQL_BDD;
	$dest = MIGRATEUR_DESTINATION_DIR . 'tmp/dump/';
	sous_repertoire(_DIR_TMP . 'dump');

	$source_sql_data   = 'export_data.sql';
	$source_sql_struct = 'export_struct.sql';

	$output = "";
	exec("rm $dest$source_sql_data;");
	exec("rm $dest$source_sql_struct;");

	$cmd = migrateur_obtenir_commande_serveur('mysqldump');
	if ($cmd) {

		migrateur_log("Exécution de mysqldump : backup des données…");
		exec("$cmd -u $user --password=$pass  --default-character-set=latin1 --no-create-info $bdd > $dest$source_sql_data 2>&1", $output, $err);
		if ($err) {
			migrateur_log("! Erreurs survenues : $err");
		} else {
			$taille = filesize($dest . $source_sql_data);
			include_spip('inc/filtres');
			migrateur_log("> Fichier : " . $dest . $source_sql_data);
			migrateur_log("> Taille : " . taille_en_octets($taille));
		}

		migrateur_log("Exécution de mysqldump : backup de la structure");
		exec("$cmd -u $user --password=$pass  --default-character-set=latin1 --no-data $bdd > $dest$source_sql_struct 2>&1", $output, $err);
		if ($err) {
			migrateur_log("! Erreurs survenues : $err");
		} else {
			$taille = filesize($dest . $source_sql_struct);
			include_spip('inc/filtres');
			migrateur_log("> Fichier : " . $dest . $source_sql_struct);
			migrateur_log("> Taille : " . taille_en_octets($taille));
		}
	} else {
		return false;
	}

	$cmd = migrateur_obtenir_commande_serveur('perl');
	if ($cmd) {
		$cmd = $cmd . ' -pi';
	} elseif ($cmd = migrateur_obtenir_commande_serveur('sed')) {
		$cmd = $cmd . ' -i';
	} else {
		migrateur_log("Commandes perl ou sed absentes. Modifications non réalisées.");
		return false;
	}

	migrateur_log("Modifications de la déclaration de la structure (iso -> utf8)");
	exec("$cmd -e's/latin1/utf8/g;' $dest$source_sql_struct");
	migrateur_log("Modifications de la déclaration des données (iso -> utf8)");
	exec("$cmd -e's/SET NAMES latin1/SET NAMES utf8/g;' $dest$source_sql_data");
}

