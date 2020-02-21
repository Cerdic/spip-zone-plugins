<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Afficher le diff des saisies de formulaire
 *
 * @param string $champ
 * @param string $old
 * @param string $new
 * @param string $format
 *   apercu, diff ou complet
 * @return string
 */
function afficher_diff_formulaire_traitements($champ, $old, $new, $format = 'diff') {
	$afficher_diff = charger_fonction('array_serialized', 'afficher_diff', true);
	return $afficher_diff($champ, $old, $new, 'complet');
}


