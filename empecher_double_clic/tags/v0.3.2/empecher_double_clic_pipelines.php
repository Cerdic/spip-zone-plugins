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
	static $flag;
	if (!test_espace_prive() and !$flag) {
		$flux['data'] .= "<script type='text/javascript' src='".find_in_path('js/empecher_double_clic.js')."'></script>";
		$flux['data'] .= "<link rel='stylesheet' type='text/css' media='all' href='".find_in_path('css/empecher_double_clic.css')."' />";
		$flag = true;
	}
	return $flux;
}
