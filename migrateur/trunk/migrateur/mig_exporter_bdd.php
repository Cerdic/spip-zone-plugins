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
	exec("rm $sauvegarde;");
	exec("rm $sauvegarde.gz;");

	// source par ssh ?
	if ($ssh = $source->ssh) {
		$connexion = $ssh->obtenir_commande_connexion();
		$cmd = $ssh->obtenir_chemin_commande_serveur('mysqldump');

		if ($cmd) {
			migrateur_log("Exécution de mysqldump distant…");
			$gzip   = $ssh->obtenir_chemin_commande_serveur('gzip');
			$gunzip = $dest->obtenir_commande_serveur('gunzip');

			if ($gzip and $gunzip) {
				migrateur_log("Récupération avec compression gz");
				$compression = "| $gzip";
				$_sauvegarde = "$sauvegarde.gz";
			} else {
				migrateur_log("Récupération sans compression");
				$compression = "";
				$_sauvegarde = "$sauvegarde";
			}

			if ($source->sql->login_path) {
				migrateur_log("Connexion avec login-path : {$source->sql->login_path}");
				$identifiants = "--login-path={$source->sql->login_path}";
			} elseif ($source->sql->user and $source->sql->pass) {
				migrateur_log("Connexion avec user (et mot de passe) : {$source->sql->user}");
				$identifiants = "-u {$source->sql->user} --password={$source->sql->pass}";
			} else {
				migrateur_log("/!\ Erreur de configuration : aucun 'login-path' ou couple 'user/password' défini pour se connecter à la base de données.");
				migrateur_log("/!\ Vérifiez les constantes de configuration.");
				return;
			}

			$run = "$connexion \"$cmd $identifiants {$source->sql->bdd} $compression\" > $_sauvegarde 2>&1";

			#migrateur_log($run);
			exec($run, $output, $err);

			if (!$err and $gzip and $gunzip) {
				exec("$gunzip $_sauvegarde", $goutput, $gerr);
				if ($gerr) {
					migrateur_log("! Erreurs de décompression : $gerr");
				} else {
					migrateur_log("Décompression OK");
					migrateur_log( implode("\n", $goutput) );
				}
			}

			if ($err) {
				migrateur_log("! Erreurs survenues : $err");
				if ($output) migrateur_log( implode("\n", $output) );
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

			if ($source->sql->login_path) {
				migrateur_log("Connexion avec login-path : {$source->sql->login_path}");
				$identifiants = "--login-path={$source->sql->login_path}";
			} elseif ($source->sql->user and $source->sql->pass) {
				migrateur_log("Connexion avec user (et mot de passe) : {$source->sql->user}");
				$identifiants = "-u {$source->sql->user} --password={$source->sql->pass}";
			} else {
				migrateur_log("/!\ Erreur de configuration : aucun 'login-path' ou couple 'user/password' défini pour se connecter à la base de données.");
				migrateur_log("/!\ Vérifiez les constantes de configuration.");
				return;
			}

			exec("$cmd $identifiants {$source->sql->bdd} > $sauvegarde 2>&1", $output, $err);

			if ($err) {
				migrateur_log("! Erreurs survenues : $err");
				if ($output) migrateur_log( implode("\n", $output) );
			} else {
				$taille = filesize($sauvegarde);
				include_spip('inc/filtres');
				migrateur_log("> Fichier : " . $sauvegarde);
				migrateur_log("> Taille : " . taille_en_octets($taille));
			}
		}
	}
}
