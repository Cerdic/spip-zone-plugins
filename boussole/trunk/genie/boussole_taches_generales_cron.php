<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function boussole_taches_generales_cron($taches_generales) {

	// Ajout de la tache CRON de mise a jour reguliere des boussoles disponibles sur le site en mode client et serveur
	// Par defaut, toutes les 24h
	// -- Pour le CRON serveur on vérifie qu'une boussole est déclarée
	$boussoles = array();
	$boussoles = pipeline('declarer_boussoles', $boussoles);
	if ($boussoles)
		$taches_generales['boussole_actualiser_serveur'] = 24*3600;

	$taches_generales['boussole_actualiser_client'] = 24*3600;

	return $taches_generales;
}

?>
