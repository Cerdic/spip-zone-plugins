<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function activite_editoriale_taches_generales_cron($taches_generales) {
	$taches_generales['activite_editoriale_alerte'] = 24*3600; // tous les jours
	return $taches_generales;
}
