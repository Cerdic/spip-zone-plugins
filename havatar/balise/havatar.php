<?php

function balise_HAVATAR($p) {
	return calculer_balise_dynamique($p, 'HAVATAR', array());
}

function balise_HAVATAR_stat($args, $filtres) {
	return array($args[0], $args[1], $args[2]);
}

function balise_HAVATAR_dyn($email, $size, $havatar_default) {
	include_spip('inc/filtres_images');
	include_spip('inc/havatar');
	return inserer_attribut(image_reduire(sinon(havatar($email),$havatar_default), $size ? $size : 80), "alt", "");
}

?>
