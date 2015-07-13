<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


/**
 * Synchronise les fichiers du répertoire IMG
**/
function migrateur_mig_sync_img() {

	$client = migrateur_client();
	$reponse = $client->action('SyncDirectory', 'IMG');

	if ($reponse) {
		migrateur_log("Synchronisation OK");
		#migrateur_log($reponse['message']['data']);
	}

}


