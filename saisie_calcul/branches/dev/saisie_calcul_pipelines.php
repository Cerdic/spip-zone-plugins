<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Insérer automatiquement l'appel au js dans les formulaires
 * @param array $flux
 * @return array $flux modifié
 **/
function saisie_calcul_formulaire_fond($flux) {
	static $flag;
	if (!$flag and stripos($flux['data'], 'saisie_calcul')!==false) {
		$flux['data'] .= "<script type='text/javascript' src='".find_in_path('js/saisie_calcul.js')."'></script>";
		$flag = true;
	}
	return $flux;
}
