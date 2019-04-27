<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Insérer automatiquement l'appel au js dans les formulaires
 * @param array $flux
 * @return array $flux modifié
 **/
function empecher_double_clic_formulaire_fond($flux) {
	if (!test_espace_prive()) {
		$flux['data'] .= "<script type='text/javascript' src='".find_in_path('js/empecher_double_clic.js')."'></script>";
	}
	return $flux;
}
