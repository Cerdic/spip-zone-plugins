<?php
/**
 *
 * Balise dynamique permettant de renvoyer la vignette du site
 * Un peu desuet maintenant mais permet quand mme de choisir la vignette simplement
 *
 **/
if (!defined("_ECRIRE_INC_VERSION")) return;

function balise_THUMBSHOT($p) {
	return calculer_balise_dynamique($p, 'THUMBSHOT', array());
}

function balise_THUMBSHOT_stat($args, $filtres) {
	return array($args[0], $args[1], $args[2]);
}

function balise_THUMBSHOT_dyn($url, $taille, $defaut) {
	include_spip('inc/filtres_images');
	include_spip('inc/thumbsites_filtres');
	return inserer_attribut(image_reduire(sinon(thumbshot($url),$defaut), $taille ? $taille : 120), "alt", "");
}

?>