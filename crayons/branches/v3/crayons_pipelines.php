<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function crayons_header_prive($flux) {

	// verifie la presence d'une meta crayons, si c'est vide
	// on ne cherche meme pas a traiter l'espace prive
	$config_espace_prive = lire_config('crayons/espaceprive');
	if ($config_espace_prive == 'on') {
		// determine les pages (exec) crayonnables
		if (test_exec_crayonnable(_request('exec'))) {
			// Calcul des droits
			include_spip('inc/crayons');
			$flux = Crayons_preparer_page($flux, '*', wdgcfg(), 'head');
		}
	}

	// retourne l'entete modifiee
	return $flux;
}
