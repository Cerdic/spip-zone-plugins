<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

function fusionner_intervalles($intervalle_a, $intervalle_b) {

	// On recupere les bornes de chaque intervalle
	$borne_a = extraire_bornes($intervalle_a);
	$borne_b = extraire_bornes($intervalle_b);

	// On initialise la borne min de chaque intervalle a 1.9.0 inclus si vide
	if (!$borne_a['min']['valeur']) {
		$borne_a['min']['valeur'] = _SVP_VERSION_SPIP_MIN;
		$borne_a['min']['incluse'] = true;
	}
	if (!$borne_b['min']['valeur']) {
		$borne_b['min']['valeur'] = _SVP_VERSION_SPIP_MIN;
		$borne_b['min']['incluse'] = true;
	}
	
	// On calcul maintenant :
	// -- la borne min de l'intervalle fusionne = min(min_a, min_b)
	// -- suivant l'intervalle retenu la borne max est forcement dans l'autre intervalle = max(autre intervalle)
	//    On presuppose evidemment que les intervalles ne sont pas disjoints et coherents entre eux
	if (spip_version_compare($borne_a['min']['valeur'], $borne_b['min']['valeur'], '<=')) {
		$bornes_fusionnees['min'] = $borne_a['min'];
		$bornes_fusionnees['max'] = $borne_b['max'];
	}
	else {
		$bornes_fusionnees['min'] = $borne_b['min'];
		$bornes_fusionnees['max'] = $borne_a['max'];
	}

	return contruire_intervalle($bornes_fusionnees);
}

function extraire_bornes($intervalle) {
	static $borne_vide = array('valeur' => '', 'incluse' => false);
	$bornes = array('min' => $borne_vide, 'max' => $borne_vide);

	if ($intervalle
	AND preg_match(',^[\[\(\]]([0-9.a-zRC\s\-]*)[;]([0-9.a-zRC\s\-\*]*)[\]\)\[]$,Uis', $intervalle, $matches)) {
		if ($matches[1]) {
			$bornes['min']['valeur'] = trim($matches[1]);
			$bornes['min']['incluse'] = ($intervalle{0} == "[");
		}
		if ($matches[2]) {
			$bornes['max']['valeur'] = trim($matches[2]);
			$bornes['max']['incluse'] = (substr($intervalle,-1) == "]");
		}
	}
	
	return $bornes;
}

function contruire_intervalle($bornes, $dtd='paquet') {
	return ($bornes['min']['incluse'] ? '[' : ($dtd=='paquet' ? ']' : '('))
			. $bornes['min']['valeur'] . ';' . $bornes['max']['valeur']
			. ($bornes['max']['incluse'] ? ']' : ($dtd=='paquet' ? '[' : ')'));
}
?>
