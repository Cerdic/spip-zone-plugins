<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function boussole_taches_generales_cron($taches_generales) {

	// Ajout de la tache CRON de mise a jour reguliere des boussoles disponibles sur le site en mode client et serveur
	// Par defaut, toutes les 24h
	$taches_generales['boussole_actualiser_client'] = 24*3600;
	$taches_generales['boussole_actualiser_serveur'] = 24*3600;

	return $taches_generales;
}

?>
