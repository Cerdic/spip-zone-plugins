<?php

use SPIP\Migrateur\Serveur;

if (!defined("_ECRIRE_INC_VERSION")) return;


/**
 * Reçoit une demande d'action d'un site client qui migre 
**/
function action_migrateur_serveur_dist() {

	// forcer l'absence de redirection ajax
	$GLOBALS['redirect'] = "";

	include_spip('inc/config');
	include_spip('migrateur_options'); # autoloader
	include_spip('inc/migrateur'); # helpers

	if ('source' != lire_config('migrateur/type')) {
		Serveur::transmettre_json(array('error' => 'Server Out'));
		exit;
	}

	$serveur = new Serveur(lire_config('migrateur/auth_key'), lire_config('migrateur/aes_key'));
	$serveur->setLogger( new Serveur\Log() );
	$serveur->run();
	exit;
}
