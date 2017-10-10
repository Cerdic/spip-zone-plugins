<?php

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

// creer un identifiant unique
function balise_UID_dist($p) {
	$p->code = "uniqid()";
	return $p;
}