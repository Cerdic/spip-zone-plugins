<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


/**
 * Test d'envoi en stream
 *
 * S'active pour le moment avec ?exec=migrateur&stream=1
**/
function migrateur_02_test_communication() {

	$client = migrateur_client();
	$reponse = $client->action('Test');

	if ($reponse) {
		migrateur_log("Retour OK, avec message :");
		migrateur_log($reponse['message']['data']);
	}

}


