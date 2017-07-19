<?php

function balise_URL_SECTEUR_dist($p) {
	$value = interprete_argument_balise(1, $p);
	if (strlen(trim($value)) == 0) {
		$value = calculer_balise('id_rubrique', $p)->code;
	}
	$p->code = 'calculer_URL_SECTEUR(' . $value . ')';

	return $p;
}
