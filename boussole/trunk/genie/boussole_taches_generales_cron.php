<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function boussole_taches_generales_cron($taches_generales) {

	// Ajout des taches CRON de mise a jour périodique (toutes les 24h) :
	// - des boussoles disponibles pour le serveur
	// - des boussoles ajoutés pour le client

	// -- Pour le CRON serveur on vérifie que le mode serveur est actif
	//    (l'existence d'une boussole disponible est vérifiée dans la tache elle-même)
	include_spip('inc/config');
	$serveur_actif = lire_config('boussole/serveur/actif') == 'on';
	if ($serveur_actif) {
		$taches_generales['boussole_actualiser_serveur'] = 24*3600;
	}

	// -- Pour le CRON client la vérification d'une boussole ajoutée est faite dans la tache elle-même
	$taches_generales['boussole_actualiser_client'] = 24*3600;

	return $taches_generales;
}

?>
