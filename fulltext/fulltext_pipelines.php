<?php
/**
 * Plugin FullText/Gestion des documents
 */

function fulltext_taches_generales_cron($taches_generales) {
	$taches_generales['fulltext_index_document'] = 60; // toutes les minutes
  return $taches_generales;
}

