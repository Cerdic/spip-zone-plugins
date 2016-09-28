<?php

/**
 * Fonctions pour Petit Cochon
 *
 * @plugin     Petit Cochon
 * @copyright  2014
 * @author     cyp
 * @licence    GNU/GPL
 * @package    SPIP\petitcochon\fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Trouver l'utilisateur qui trouve le bon poids
 *
 * @param string $poids
 * @param string $nom
 */
function gagnant_cochon($poids, $nom) {
	include_spip('inc/config');

	$poids_cochon = number_format(lire_config('petitcochon/poids_cochon', 0), 3);

	// Si celui qui trouve le poids exact sinon on prends le poids le plus poche
	// Poids exacte => couleur 2
	if ($poids == $poids_cochon) {
		$couleur = 'couleur2';
	} else {
		$precision = number_format(lire_config('petitcochon/precision_poids', 0), 3);

		$min = number_format($poids_cochon,3)-number_format($precision,3);
		$max = number_format($poids_cochon,3)+number_format($precision,3);

		// On test qui gagne
		$interval = sql_getfetsel('nom', 'spip_petitcochon', 'poids between ' . $min . ' and ' . $max.' AND nom='.sql_quote($nom));
		if ($interval) {
			$couleur = 'couleur1';
		}
	}

	return $couleur;
}
