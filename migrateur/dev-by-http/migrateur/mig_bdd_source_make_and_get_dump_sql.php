<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


/**
 * Récupère la base de données source
**/
function migrateur_mig_bdd_source_make_and_get_dump_sql() {

	$client = migrateur_client();
	$reponse = $client->action('DumpDatabase');

	if (!$reponse) {
		migrateur_log("Échec de la sauvegarde");
		return;
	}

	$fichier = $reponse['message']['data']['fichier'];
	$hash    = $reponse['message']['data']['hash'];

	migrateur_log("Sauvegarde effectuée en " .  $reponse['message']['data']['duree']);
	migrateur_log("Fichier source " . $fichier . " (" .$reponse['message']['data']['taille_octets']  . ")");


	migrateur_log("Récupération de la sauvegarde");

	$reponse = $client->action('GetFile', array(
		'fichier' => $fichier,
		'hash' => $hash,
	));

	if (!$reponse) {
		migrateur_log("Échec de récupération du fichier");
		return;
	} 

	migrateur_log("Récupération ok");

	$file = $reponse['message']['data']['fichier'];

	// decompresser si nécessaire
	if (substr($file, -3, 3) == '.gz') {
		migrateur_get_database_decompresser($reponse['message']['data']['chemin']);
	}
}


function migrateur_get_database_decompresser($chemin) {
	migrateur_log("Décompression du dump");
	$futur = substr($chemin, 0, -3);
	@unlink($futur); 

	$destination = migrateur_infos();
	$gunzip = $destination->obtenir_commande_serveur('gunzip');
	if (!$gunzip) {
		migrateur_log("! Erreurs de décompression : gunzip absent !");
		return false;
	}

	spip_timer('gunzip');
	exec("$gunzip $chemin", $goutput, $gerr);
	$t = spip_timer('gunzip');

	if ($gerr) {
		migrateur_log("! Erreurs de décompression : $gerr");
	} else {
		migrateur_log("Décompression OK en $t");
		$message = trim(implode("\n", $goutput));
		if ($message) {
			migrateur_log($message);
		}
	}
}
