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
 * La signature de la balise est : `#NOISETTE_REPERTORIER{plugin, conteneur[, stockage]}`.
 *
 * @balise
 *
 * @uses noisette_repertorier()
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

	// On appelle la fonction de calcul de la liste des noisettes
	$p->code = "$conteneur ? noisette_repertorier($plugin, $conteneur, 'rang_noisette', array(), $stockage) : array()";

	return $p;
}
