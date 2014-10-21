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

	// source et destination sur serveurs différents
	if ($ssh = migrateur_source_ssh()) {

		$ssh_cmd = $ssh->obtenir_commande_serveur();
		$cmd = $ssh->obtenir_commande_serveur_distant('mysqldump');

		if ($ssh_cmd and $cmd) {
			migrateur_log("Exécution de mysqldump distant…");
			$run = "$ssh_cmd \"$cmd -u $user --password=$pass $bdd\" > $dest$source_sql 2>&1";
			#migrateur_log($run);
			exec($run, $output, $err);

			if ($err) {
				migrateur_log("! Erreurs survenues : $err");
			} else {
				$taille = filesize($dest . $source_sql);
				include_spip('inc/filtres');
				migrateur_log("> Fichier : " . $dest . $source_sql);
				migrateur_log("> Taille : " . taille_en_octets($taille));
				$firstline = shell_exec("head -n1 $dest$source_sql");
				migrateur_log("> 1ere ligne : " . $firstline);
				if ( false === stripos($firstline, 'mysql') ) {
					migrateur_log("> /!\ 1ere ligne sans texte Mysql !!!");
				}
			}
		} else {
			migrateur_log("Connexion au serveur source impossible");
		}
	}

	// source et destination sur le meme serveur
	else {
		$cmd = migrateur_obtenir_commande_serveur('mysqldump');
		if ($cmd) {
			migrateur_log("Exécution de mysqldump…");
			exec("$cmd -u $user --password=$pass $bdd > $dest$source_sql 2>&1", $output, $err);
			if ($err) {
				migrateur_log("! Erreurs survenues : $err");
			} else {
				$taille = filesize($dest . $source_sql);
				include_spip('inc/filtres');
				migrateur_log("> Fichier : " . $dest . $source_sql);
				migrateur_log("> Taille : " . taille_en_octets($taille));
			}
		}
	}
}
