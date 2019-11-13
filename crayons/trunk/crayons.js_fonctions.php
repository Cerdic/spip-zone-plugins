<?php
/**
 * Crayons
 * plugin for spip
 * (c) Fil, toggg 2006-2019
 * licence GPL
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

//
// Les crayons ont leur propre copie du pack de Dean Edwards
// pour compatibilite avec SPIP < [9717]
//
function pack_cQuery($chemin) {
	if (!$chemin) {
		return;
	}

	$flux = spip_file_get_contents($chemin);
	$flux = str_replace('jQuery', 'cQuery', $flux);
	$flux = str_replace('cQuery.spip', 'jQuery.spip', $flux);

	// On ne compacte PAS deux fois (c'est inutile et en plus ca bugge)
	if (!strlen($flux)
		or _request('debug_crayons') // mode debug des crayons
		or !function_exists('minifier')
		) {
		return $flux;
	}

	$flux = minifier($flux, 'js');
	return $flux;
}
