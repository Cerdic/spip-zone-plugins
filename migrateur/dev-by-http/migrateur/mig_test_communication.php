<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


/**
 * Test de la communication entre le serveur source et ce site.
 *
 * S'il y a un retour OK, c'est que le cryptage / décryptage
 * s'est bien déroulé des 2 côtés.
**/
function migrateur_mig_test_communication() {

	$client = migrateur_client();
	$reponse = $client->action('Test');

	if ($reponse) {
		migrateur_log("Retour OK, avec message :");
		migrateur_log($reponse['message']['data']);
	}

}


