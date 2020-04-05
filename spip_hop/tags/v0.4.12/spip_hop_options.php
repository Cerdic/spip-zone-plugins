<?php

/**
 * Déclaration systématiquement chargées.
 **/

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

// ********
// Permettre de surcharger la taille pour le petit écran et le grand écran
// Important : la valeur renseignée doit avoir une unité de mesure : em, rem, px, %, pt.
// exemple : 1200px
// ********

if (!defined('_PETIT_ECRAN')) {
	define('_PETIT_ECRAN', '');
}
if (!defined('_GRAND_ECRAN')) {
	define('_GRAND_ECRAN', '');
}

if (test_espace_prive()) {
	include_spip('inc/config');
	$hop_fge = lire_config('spip_hop/forcer_grand_ecran');
	if (!is_null($hop_fge) && $hop_fge == 'oui') {
		$GLOBALS['spip_ecran'] = $_COOKIE['spip_ecran'] = 'large';
	}
}
