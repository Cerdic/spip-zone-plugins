<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('medias_nettoyage_fonctions');
include_spip('inc/meta');

function genie_medias_reparer_documents_dist ($t) {
	include_spip('medias_nettoyage_fonctions');

	if (!isset(lire_metas('medias_nettoyage'))) {
		// Si le plugin n'a pas encore été configuré,
		// on lance le script entre 00h00 et 06h00
		medias_lancer_script();

	} else if (isset(lire_metas('medias_nettoyage/activation') 
		AND lire_metas('medias_nettoyage/activation') == 'oui'
		AND !isset(lire_metas('medias_nettoyage/horaires'))) {
		// Si on a activé la tranche horaire mais qu'on a pas choisi le créneau
		// On lance le script entre 00h00 et 06h00
		medias_lancer_script();

	} else if (isset(lire_metas('medias_nettoyage/activation')) AND isset(lire_metas('medias_nettoyage/horaires'))) {
		// Si on a activé la tranche horaire et qu'on a choisi le créneau
		// On lance le script dans la tranche horaire choisie
		$horaires = explode('-', lire_metas('medias_nettoyage/horaires'));
		medias_lancer_script($horaires[0],$horaires[1]);

	} else if (isset(lire_metas('medias_nettoyage/activation')) AND lire_metas('medias_nettoyage/activation') == 'non') {
		// Si on a sélectionné 'non' pour la tranche horaire,
		// on lance le script toutes les 5heures comme prévu dans medias_nettoyage_pipelines.php
		if (function_exists('medias_reparer_documents_fichiers')) {
			medias_reparer_documents_fichiers();
		}
	}

	return 1;
}

function medias_lancer_script ($debut = 0, $fin = 600) {
	$timer = date_format(date_create(), 'Hi');

	// On vérifie bien que nous sommes bien dans la bonne tranche horaire
	if ($timer >= $debut AND $timer < $fin) {
		if (function_exists('medias_reparer_documents_fichiers')) {
			medias_reparer_documents_fichiers();
		}
	}

	return;
}

?>