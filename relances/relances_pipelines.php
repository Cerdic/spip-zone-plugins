<?php
/**
 * Plugin Abonnements
 * (c) 2012-2013 Les Développements Durables
 * Licence GNU/GPL v3
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/*
 * Ajout d'une tache CRON pour envoyer les relances
 */
function relance_taches_generales_cron($taches){
	$taches['abonnements_verifier_notifications'] = 24 * 3600; // une fois par jour
	return $taches;
}

 
?>
