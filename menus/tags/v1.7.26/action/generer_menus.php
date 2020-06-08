<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function action_generer_menus_dist($arg = null) {
	include_spip('menus_fonctions');
	include_spip('action/editer_objet');

	if (is_null($arg)) {
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	$identifiant = trim($arg);
	$menus_utiles = menus_utiles();

	// S'il y a un identifiant précis on ne garde que celui-là
	if ($identifiant) {
		$menus_utiles = array_intersect_key($menus_utiles, array($identifiant => 'oui'));
	}

	// Pour chaque menu qui reste, on le génère
	foreach ($menus_utiles as $identifiant => $titre) {
		objet_inserer('menu', 0, array('identifiant' => $identifiant, 'titre' => $titre));
	}
}
