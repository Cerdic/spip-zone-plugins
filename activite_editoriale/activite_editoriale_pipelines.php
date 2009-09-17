<?php
/**
 * Plugin FullText/Gestion des documents
 *
 *
 */

function activite_editoriale_taches_generales_cron($taches_generales) {
	$taches_generales['activite_editoriale_alerte'] = 60; // toutes les minutes
  	return $taches_generales;
}

