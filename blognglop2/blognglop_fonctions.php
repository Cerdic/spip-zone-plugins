<?php

/* Balise #BNG_VERSION renvoyant la version du squelette B'nG */
function bng_version() {
	$my_bng_version="2.0a";
	return $my_bng_version;
}

function balise_BNG_VERSION($p) {
	$p->code = "bng_version()";
	$p->interdire_scripts = false;
	return $p;
}
?>
