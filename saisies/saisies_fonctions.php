<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/saisies');
include_spip('balise/saisie');
// picker_selected (spip 3)
include_spip('formulaires/selecteur/generique_fonctions');


/**
 * Passer un nom en une valeur compatible avec une classe css
 * toto => toto,
 * toto/truc => toto_truc,
 * toto[truc] => toto_truc,
**/
function saisie_nom2classe($nom) {
	return str_replace(array('/', '[', ']'), '_', $nom);
}

/**
 * Passer un nom en une valeur compatible avec un name de formulaire
 * toto => toto,
 * toto/truc => toto[truc],
 * toto[truc] => toto[truc],
**/
function saisie_nom2name($nom) {
	if (false === strpos($nom, '/')) {
		return $nom;
	}
	$nom = explode('/', $nom);
	$premier = array_shift($nom);
	$nom = implode('][', $nom);
	return $premier . '[' . $nom . ']';
}


?>
