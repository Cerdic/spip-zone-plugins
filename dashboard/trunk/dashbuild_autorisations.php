<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;


// declarer la fonction du pipeline
function dashbuild_autoriser(){}


// Seul admin a acces aux logs
function autoriser_dashboard_voir_dist($faire, $type, $id, $qui, $opt) {
	$autoriser = false;

	if (autoriser('webmestre') AND $id) {
		$dashboards = lister_dashboards();
		if (array_key_exists($id, $dashboards))
			$autoriser = true;
	}

	return $autoriser;
}

?>
