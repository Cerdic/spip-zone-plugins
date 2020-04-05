<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

function balise_IDENTITE_dist($p) {
	$values = "lire_config('identite_extra')";
	$p->code = $values;
	return $p;
}