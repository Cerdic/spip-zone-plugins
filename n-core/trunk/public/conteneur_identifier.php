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
 * @param Champ $p
 *        Pile au niveau de la balise.
 *
 * @return Champ
 *         Pile complétée par le code à générer.
 **/
function balise_CONTENEUR_IDENTIFIER_dist($p) {

	// Récupération des arguments.
	// -- la balise utilise toujours le rangement par rang au sein du conteneur
	// -- et ne permet de filtrer les noisettes autrement que sur le conteneur.
	$plugin = interprete_argument_balise(1, $p);
	$plugin = isset($plugin) ? str_replace('\'', '"', $plugin) : '""';
	$conteneur = interprete_argument_balise(2, $p);
	$conteneur = isset($conteneur) ? str_replace('\'', '"', $conteneur) : '""';
	$stockage = interprete_argument_balise(3, $p);
	$stockage = isset($stockage) ? str_replace('\'', '"', $stockage) : '""';

	// On appelle la fonction de calcul de la liste des noisette
	$p->code = "calculer_identifiant_conteneur($plugin, $conteneur, $stockage)";

	return $p;
}

/**
 * Calcule l'identifiant unique pour le conteneur sous forme de chaine pour la balise #CONTENEUR_IDENTIFIER.
 * Cette fonction est juste un wrapper pour le service ncore_conteneur_identifier().
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *        un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param array  $conteneur
 *        Tableau associatif descriptif du conteneur accueillant la noisette.
 * @param string $stockage
 *        Identifiant du service de stockage à utiliser si précisé.
 *
 * @return string
 *        Identifiant du conteneur ou chaine vide en cas d'erreur.
 */
function calculer_identifiant_conteneur($plugin, $conteneur, $stockage) {

	include_spip('ncore/ncore');
	$identifiant = ncore_conteneur_identifier($plugin, $conteneur, $stockage);

	return $identifiant;
}
