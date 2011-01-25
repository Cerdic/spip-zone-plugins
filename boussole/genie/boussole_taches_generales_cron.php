<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function boussole_taches_generales_cron($taches_generales) {

	// Ajout de la tache CRON de mise a jour reguliere de la boussole SPIP
	// Par defaut, toutes les 24h
	$taches_generales['boussole_spip_actualiser'] = 24*3600;

	return $taches_generales;
}

?>
