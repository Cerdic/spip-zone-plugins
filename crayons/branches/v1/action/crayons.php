<?php
/**
 * Crayons
 * plugin for spip
 * (c) Fil, toggg 2006-2013
 * licence GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

// On a ete appele par un formulaire normal (ajax est traite par crayons_store)
function action_crayons_dist() {
	include_spip('action/crayons_store');
	$r = crayons_store();

	// soit exit, soit un redirect gere par SPIP
	if (trim($r['$erreur'])) {
		include_spip('inc/minipres');
		echo minipres($r['$erreur']);
		exit;
	}

	// S'il n'y a pas de redirect, on est mal : eviter toutefois la page blanche
	if (!_request('redirect')) {
		die('OK');
	}

	// Invalider le cache parce que bon... a priori on est dans une
	// interface qui va avoir besoin de refresh
	include_spip('inc/invalideur');
	suivre_invalideur('1');
}
