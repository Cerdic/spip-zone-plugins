<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Exporter la base de données source
**/
function migrateur_mig_exporter_bdd() {

	sous_repertoire(_DIR_TMP . 'dump');

	$source = migrateur_source();
	$dest   = migrateur_destination();

	$sauvegarde = $dest->dir . 'tmp/dump/' . MIGRATEUR_NOM_EXPORT_SQL;

	$output = "";
	exec("rm $dir_dest$source_sql;");
	exec("rm $dir_dest$source_sql.gz;");

	// source par ssh ?
	if ($ssh = $source->ssh) {
		$connexion = $ssh->obtenir_commande_connexion();
		$cmd = $ssh->obtenir_commande_serveur('mysqldump');

		if ($cmd) {
			migrateur_log("Exécution de mysqldump distant…");
			$gzip   = $ssh->obtenir_commande_serveur('gzip');
			$gunzip = $ssh->obtenir_commande_serveur('gunzip');
			if ($gzip and $gunzip) {
				migrateur_log("Gzip présents : utilisation de compression");
				$run = "$ssh_cmd \"$cmd -u {$source->sql->user} --password={$source->sql->pass} {$source->sql->bdd} | $gzip\" > $sauvegarde.gz 2>&1";
			} else {
				$run = "$ssh_cmd \"$cmd -u {$source->sql->user} --password={$source->sql->pass} {$source->sql->bdd}\" > $sauvegarde 2>&1";
			}
			#migrateur_log($run);
			exec($run, $output, $err);

			if (!$err and $gzip and $gunzip) {
				exec("$gunzip $sauvegarde.gz", $goutput, $gerr);
				if ($gerr) {
					migrateur_log("! Erreurs de décompression : $gerr");
				} else {
					migrateur_log("Décompression OK");
					migrateur_log( implode("\n", $goutput) );
				}
			}

			if ($err) {
				migrateur_log("! Erreurs survenues : $err");
			} else {
				$taille = filesize($sauvegarde);
				include_spip('inc/filtres');
				migrateur_log("> Fichier : " . $sauvegarde);
				migrateur_log("> Taille : " . taille_en_octets($taille));
				$firstline = shell_exec("head -n1 $sauvegarde");
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
		$cmd = $source->commande('mysqldump');
		if ($cmd) {
			migrateur_log("Exécution de mysqldump…");
			exec("$cmd -u {$source->sql->user} --password={$source->sql->pass} {$source->sql->bdd} > $sauvegarde 2>&1", $output, $err);
			if ($err) {
				migrateur_log("! Erreurs survenues : $err");
			} else {
				$taille = filesize($sauvegarde);
				include_spip('inc/filtres');
				migrateur_log("> Fichier : " . $sauvegarde);
				migrateur_log("> Taille : " . taille_en_octets($taille));
			}
		}
	}
}
