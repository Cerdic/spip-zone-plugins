<?php
/**
 * Ce fichier contient la balise `#NOISETTE_REPERTORIER` qui renvoie la liste des noisettes incluses dans un conteneur.
 *
 * @package SPIP\NCORE\NOISETTE\BALISE
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Compile la balise `#NOISETTE_REPERTORIER` qui renvoie la liste des noisettes incluses dans un conteneur donné.
 * La signature de la balise est : `#CONTENEUR_IDENTIFIER{plugin, conteneur[, stockage]}`.
 *
 * @package SPIP\NCORE\NOISETTE\BALISE
 * @balise
 *
 * @param Champ $p
 *        Pile au niveau de la balise.
 *
 * @return Champ
 *         Pile complétée par le code à générer.
 **/
function balise_NOISETTE_REPERTORIER_dist($p) {

	// Récupération des arguments.
	// -- la balise utilise toujours le rangement par rang au sein du conteneur
	// -- et ne permet pas de filtrer les noisettes autrement que sur le conteneur.
	$plugin = interprete_argument_balise(1, $p);
	$plugin = isset($plugin) ? str_replace('\'', '"', $plugin) : '""';
	$conteneur = interprete_argument_balise(2, $p);
	$conteneur = isset($conteneur) ? str_replace('\'', '"', $conteneur) : '""';
	$stockage = interprete_argument_balise(3, $p);
	$stockage = isset($stockage) ? str_replace('\'', '"', $stockage) : '""';

	// On appelle la fonction de calcul de la liste des noisette
	$p->code = "calculer_liste_noisettes($plugin, $conteneur, $stockage)";

	return $p;
}

/**
 * Récupère la liste des noisettes d'un conteneur pour la balise #CONTENEUR_IDENTIFIER.
 * Cette fonction est juste un wrapper pour la fonction d'API noisette_repertorier().
 *
 * @param string $plugin
 *        Identifiant qui permet de distinguer le module appelant qui peut-être un plugin comme le noiZetier ou
 *        un script. Pour un plugin, le plus pertinent est d'utiliser le préfixe.
 * @param array  $conteneur
 *        Tableau associatif descriptif du conteneur accueillant la noisette.
 * @param string $stockage
 *        Identifiant du service de stockage à utiliser si précisé.
 *
 * @return array
 *        Tableau des descriptions des noisettes du conteneur indexé par le rang de chaque noisette.
 */
function calculer_liste_noisettes($plugin, $conteneur, $stockage) {

	include_spip('inc/ncore_noisette');
	$noisettes = noisette_repertorier($plugin, $conteneur, 'rang_noisette', array(), $stockage);

	return $noisettes;
}
