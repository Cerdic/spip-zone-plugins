<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


/**
 * Synchronise les fichiers d'un répertoire
**/
function migrateur_mig_sync($options) {

	$client = migrateur_client();
	$reponse = $client->action('SyncDirectory', $options);

	if ($reponse) {
		migrateur_log("Synchronisation OK");
		#migrateur_log($reponse['message']['data']);
	}

}


