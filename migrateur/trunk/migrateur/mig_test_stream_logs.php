<?php

if (!defined("_ECRIRE_INC_VERSION")) return;


/**
 * Test des l'affichage des logs locaux en stream js
 *
 * S'active avec ?exec=migrateur&stream=1
**/
function migrateur_mig_test_stream_logs() {
	migrateur_log("Entrée dans la fonction de test stream.");
	migrateur_log("Delay 1 seconde.");
	sleep(1);
	migrateur_log("Delay 2 secondes.");
	sleep(2);
	$max = 10;
	for ($i = 1; $i<$max+1; $i++) {
		migrateur_log("Delay 0.5 seconde ($i/$max).");
		usleep(500000);
	}
	migrateur_log("Sortie de la fonction de test stream.");
}
