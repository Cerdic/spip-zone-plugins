<?php
// A completer
function balise_SPIPERIPSUM($p) {

	$jour = interprete_argument_balise(2,$p);
	$jour = isset($jour) ? str_replace('\'', '"', $jour) : '""';
	$lecture = interprete_argument_balise(1,$p);
	$lecture = isset($lecture) ? str_replace('\'', '"', $lecture) : '""';
	$langue = interprete_argument_balise(1,$p);
	$langue = isset($langue) ? str_replace('\'', '"', $langue) : '""';

	$p->code = 'calculer_spiperipsum('.$jour.', '.$lecture.', '.$langue.')';
	$p->interdire_scripts = false;
	return $p;
}

function calculer_spiperipsum($jour, $lecture, $langue) {
	include_spip('inc/spiperipsum_utils');
	$texte = '';
	return $texte;
}
?>