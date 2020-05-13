<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Afficher le diff d'un tableaux serialize
 *
 * @param string $champ
 * @param string $old
 * @param string $new
 * @param string $format
 *   apercu, diff ou complet
 * @return string
 */
function afficher_diff_array_serialized($champ, $old, $new, $format = 'diff') {
	// Pour le diff de saisies, faire comme un diff de yaml
	include_spip('inc/yaml');
	$tenter_unserialize = charger_fonction('tenter_unserialize', 'filtre/');
	$new = $tenter_unserialize($new);
	$old = $tenter_unserialize($old);
	if (is_array($new)) {
		$new = yaml_encode($new);
	} else {
		$new = '';
	}
	if (is_array($old)) {
		$old = yaml_encode($old);
	} else {
		$old = '';
	}
	$afficher_diff = charger_fonction('champ', 'afficher_diff', true);
	return $afficher_diff($champ, $old, $new, 'complet');
}


