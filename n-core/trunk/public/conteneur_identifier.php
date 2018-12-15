<?php
/**
 * Ce fichier contient la balise `#CONTENEUR_IDENTIFIER` qui calcule l'identifiant unique d'un conteneur.
 *
 * @package SPIP\NCORE\CONTENEUR\BALISE
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Compile la balise `#CONTENEUR_IDENTIFIER` qui calcule l'identifiant unique d'un conteneur au format
 * chaîne de caractères à partir de la description tabulaire du conteneur.
 * La signature de la balise est : `#CONTENEUR_IDENTIFIER{plugin, conteneur[, stockage]}`.
 *
 * @balise
 *
 * @uses conteneur_identifier()
 *
 * @param Champ $p
 *        Pile au niveau de la balise.
 *
 * @return Champ
 *         Pile complétée par le code à générer.
 **/
function balise_CONTENEUR_IDENTIFIER_dist($p) {

	// Récupération des arguments.
	$plugin = interprete_argument_balise(1, $p);
	$plugin = isset($plugin) ? str_replace('\'', '"', $plugin) : '""';
	$conteneur = interprete_argument_balise(2, $p);
	$conteneur = isset($conteneur) ? str_replace('\'', '"', $conteneur) : '""';
	$stockage = interprete_argument_balise(3, $p);
	$stockage = isset($stockage) ? str_replace('\'', '"', $stockage) : '""';

	// On appelle la fonction de calcul de l'identifiant du conteneur.
	$p->code = "conteneur_identifier($plugin, $conteneur, $stockage)";

	return $p;
}
