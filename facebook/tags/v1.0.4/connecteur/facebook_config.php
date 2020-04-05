<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function connecteur_facebook_config_dist() {
	return array(
		'connecteur' => 'token', // Type de connecteur
		'type' => 'facebook', // Pour la forme
		// Fonction de l'API pour trouver le token
		'trouver_token' => 'facebook_access_token',
		// Charger un fichier avant d'executer la fonction trouver_token
		'charger_fichier' => 'inc/facebook'
	);
}
