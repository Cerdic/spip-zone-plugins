<?php
/**
 *
 */

function action_langonet_afficher_dist(){

	// Securisation: aucun argument attendu
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$file_name = $securiser_action();
	if (!@is_readable($file_name)) {
		spip_log("*** LANGONET (action_langonet_afficher_dist) ERREUR: $file_name pas accessible en lecture");
	}

	// Lecture du fichier de log (.log) ou de langue (.php)
	echo '<pre style="font-size: 2em; white-space: pre-wrap;">' . file_get_contents($file_name) . '</pre>';
	exit();

}

?>