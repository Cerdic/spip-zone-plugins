<?php
if (!defined('_ECRIRE_INC_VERSION')) return;

function balise_SPIPERIPSUM($p) {

	$langue = interprete_argument_balise(1,$p);
	$langue = isset($langue) ? str_replace('\'', '"', $langue) : '"en"';
	$jour = interprete_argument_balise(2,$p);
	$jour = isset($jour) ? str_replace('\'', '"', $jour) : '""';
	$lecture = interprete_argument_balise(3,$p);
	$lecture = isset($lecture) ? str_replace('\'', '"', $lecture) : '""';
	$info = interprete_argument_balise(4,$p);
	$info = isset($info) ? str_replace('\'', '"', $info) : '""';

	$p->code = 'spiperipsum_lire('.$langue.', '.$jour.', '.$lecture.', '.$info.')';
	$p->interdire_scripts = false;
	return $p;
}
?>