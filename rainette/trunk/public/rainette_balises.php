<?php
function balise_RAINETTE_INFOS($p) {

	$code_meteo = interprete_argument_balise(1,$p);
	$code_meteo = isset($code_meteo) ? str_replace('\'', '"', $code_meteo) : '""';
	$type_info = interprete_argument_balise(2,$p);
	$type_info = isset($type_info) ? str_replace('\'', '"', $type_info) : '""';
	$service = interprete_argument_balise(3,$p);
	$service = isset($service) ? str_replace('\'', '"', $service) : '"weather"';

	$p->code = 'calculer_infos('.$code_meteo.', '.$type_info.', '.$service.')';
	$p->interdire_scripts = false;
	return $p;
}

function calculer_infos($lieu, $type, $service) {

	// Traitement des cas ou les arguments sont vides
	if (!$lieu) return '';
	if (!$service) $service = 'weather';

	include_spip('inc/rainette_utils');
	$nom_fichier = charger_meteo($lieu, 'infos', $service);
	lire_fichier($nom_fichier,$tableau);
	if (!isset($type) OR !$type)
		return $tableau;
	else {
		$tableau = unserialize($tableau);
		$info = $tableau[strtolower($type)];
		return $info;
	}
}
?>