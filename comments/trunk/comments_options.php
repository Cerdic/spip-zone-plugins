<?php

// pas la peine d'init les constantes dans un autre contexte qu'en post
// ca economise
if ($_SERVER['REQUEST_METHOD'] == 'POST'){
	include_spip("inc/config");

	// Limiter la longueur des messages
	if (!defined('_FORUM_LONGUEUR_MAXI')) {
		if ($max = lire_config("comments/forum_longueur_maxi")) {
			define('_FORUM_LONGUEUR_MAXI', $max);
		}
	}
	if (!defined('_FORUM_LONGUEUR_MINI')) {
		if ($min = lire_config("comments/forum_longueur_mini")) {
			define('_FORUM_LONGUEUR_MINI', $min);
		}
	}
}

// pour la version thread
define('_FORUM_AUTORISER_POST_ID_FORUM',true);
