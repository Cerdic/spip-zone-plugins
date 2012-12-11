<?php
/**
 * Plugin Campagnes publicitaires
 * (c) 2012 Les Développements Durables
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/*
 * Ajout d'une tache CRON pour vérifier toutes les heures les pubs à publier/dépublier
 */
function campagnes_taches_generales_cron($taches){
	$taches['campagnes_publication'] = 60 * 60;
	return $taches;
}


?>
