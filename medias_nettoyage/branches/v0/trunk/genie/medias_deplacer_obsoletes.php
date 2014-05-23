<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('medias_nettoyage_fonctions');
include_spip('inc/filtres');
include_spip('inc/meta');


/**
 * On passe par un cron pour s'occuper des dossiers et répertoires obsolètes
 * On est à une fréquence de toutes les 5 heures. 
 * 
 * Mais il faudrait trouver une astuce 
 * pour ne lancer le cron que s'il y a un répertoire ou fichier obsolète.
 * 
 * @param  unknown $t
 * 
 * @return bool
 */
function genie_medias_deplacer_obsoletes_dist ($t) {

	// Si on est en SPIP 2, on regarde $GLOBALS
	// En SPIP 3, on passe par la fonction lire_config
	if (intval(spip_version())==2) {
		$medias_nettoyage = ($GLOBALS['meta']['medias_nettoyage']) ? @unserialize($GLOBALS['meta']['medias_nettoyage']) : null;
	} else if ( intval(spip_version()) == 3 ){
		include_spip('inc/config');
		$medias_nettoyage = lire_config('medias_nettoyage');
	} else {
		$medias_nettoyage = null;
	}

	// Si le plugin n'a pas encore été configuré,
	// on lance le script entre 00h00 et 06h00
	if (!isset($medias_nettoyage)) {
		spip_log(date_format(date_create(), 'Y-m-d H:i:s') . ' : On est dans la tranche horaire par défaut. On lance le script ' . __FUNCTION__ ,"medias_orphelins");
		medias_lancer_script();

	} else if (isset($medias_nettoyage['activation']) 
		AND $medias_nettoyage['activation'] == 'oui'
		AND (!isset($medias_nettoyage['horaires']) OR $medias_nettoyage['horaires'] == '')) {
		// Si on a activé la tranche horaire mais qu'on a pas choisi le créneau
		// On lance le script entre 00h00 et 06h00
			spip_log(date_format(date_create(), 'Y-m-d H:i:s') . ' : Le CRON par tranche horaire est actif mais non défini. On lance le script ' . __FUNCTION__ ,"medias_orphelins");
			medias_lancer_script();

	} else if (isset($medias_nettoyage['activation']) AND isset($medias_nettoyage['horaires'])) {
		// Si on a activé la tranche horaire et qu'on a choisi le créneau
		// On lance le script dans la tranche horaire choisie
		$horaires = ($medias_nettoyage['horaires']=='') ? array(0,600) : explode('-', $medias_nettoyage['horaires']);
		spip_log(date_format(date_create(), 'Y-m-d H:i:s') . ' : Le CRON par tranche horaire est actif et défini. On lance le script ' . __FUNCTION__ . ' entre ' . $horaires[0] . ' et ' . $horaires[1],"medias_orphelins");
		medias_lancer_script($horaires[0],$horaires[1]);

	} else if (isset($medias_nettoyage['activation']) AND $medias_nettoyage['activation'] == 'non') {
		// Si on a sélectionné 'non' pour la tranche horaire,
		// on lance le script toutes les 5heures comme prévu dans medias_nettoyage_pipelines.php
		spip_log(date_format(date_create(), 'Y-m-d H:i:s') . ' : Le CRON par tranche horaire est désactivé. On lance le script ' . __FUNCTION__ . ' toutes les 5h.',"medias_orphelins");
		if (function_exists('medias_deplacer_rep_obsoletes')) {
			medias_deplacer_rep_obsoletes();
		}
	}

	return 1;
}

function medias_lancer_script ($debut = 0, $fin = 600) {
	$timer = date_format(date_create(), 'Hi');

	// On vérifie bien que nous sommes bien dans la bonne tranche horaire
	if ($timer >= $debut AND $timer < $fin) {
		if (function_exists('medias_deplacer_rep_obsoletes')) {
			medias_deplacer_rep_obsoletes();
		}
	}

	return;
}

?>