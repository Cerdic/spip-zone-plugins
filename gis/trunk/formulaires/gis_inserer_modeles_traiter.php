<?php
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function formulaires_gis_inserer_modeles_traiter_dist($champs) {

	// champs a ne pas prendre en compte
	$ignorer = array('adresse','code_postal','ville','pays');

	$code = '<' . _request('modele');
	if (_request('id_modele') && _request('id_modele') != '') {
		$code .= _request('id_modele');
	}
	if (_request('variante') && _request('variante')!='') {
		$code .= '|'._request('variante');
	}
	if (_request('classe') && _request('classe')!='') {
		$code .= '|'._request('classe');
	}
	if (_request('align') && _request('align')!='') {
		$code .= '|'._request('align');
	}
	foreach ($champs as $champ) {
		if (!in_array($champ, $ignorer) && $champ != 'modele' && $champ != 'variante' && $champ != 'classe' && $champ != 'id_modele' && $champ != 'align' && _request($champ) && _request($champ)!='') {
			if ($champ == _request($champ)) {
				$code .= "|$champ";
			} elseif (is_array(_request($champ))) {
				// On transforme les tableaux en une liste
				$code .= "|$champ=".implode(',', _request($champ));
			} else {
				$code .= "|$champ=" . _request($champ);
			}
		}
	}
	$code .= '>';

	return $code;
}
