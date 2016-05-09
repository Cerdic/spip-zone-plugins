<?php
/**
 * Crayons
 * plugin for spip
 * (c) Fil, toggg 2006-2013
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
		or ($GLOBALS['meta']['auto_compress_js'] == 'oui' 	// le vieil auto_compress_js
		and @file_exists(_DIR_RESTREINT.'inc/compacte_js.php'))
		or !function_exists('test_espace_prive')// ou l'espace prive
		or test_espace_prive()) {
		return $flux;
	}

	include_spip('lib/JavaScriptPacker/class.JavaScriptPacker');
	$packer = new JavaScriptPacker($flux, 0, true, false);

	// en cas d'echec (?) renvoyer l'original
	if (strlen($t = $packer->pack())) {
		return $t;
	}

	// erreur
	spip_log('erreur de pack_js');
	return $flux;
}
