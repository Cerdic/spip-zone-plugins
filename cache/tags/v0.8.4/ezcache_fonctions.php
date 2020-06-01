<?php
/**
 * Ce fichier contient les balises de Cache Factory.
 */
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Compile la balise `#CACHE_LISTE` qui fournit la liste des caches pour un plugin utilisateur donné
 * et conformes aux filtres éventuellement fournis.
 * La signature de la balise est : `#CACHE_LISTE{plugin[, filtres]}`.
 *
 * @balise
 *
 * @example
 *     ```
 *     #CACHE_LISTE{ezcheck}, renvoie tous les caches du plugin ezcheck
 *     #CACHE_LISTE{ezcheck, #ARRAY{objet, repo}}, renvoie les caches du plugin ezcheck dont l'objet est 'repo'
 *     ```
 *
 * @param Champ $p Pile au niveau de la balise.
 *
 * @return Champ Pile complétée par le code à générer.
 */
function balise_CACHE_LISTE_dist($p) {

	// Récupération des arguments.
	// -- le plugin est toujours nécessaire
	$plugin = interprete_argument_balise(1, $p);
	$plugin = str_replace('\'', '"', $plugin);
	// -- les filtres sont optionnels
	$filtres = interprete_argument_balise(2, $p);
	$filtres = isset($filtres) ? str_replace('\'', '"', $filtres) : 'array()';

	// Appel de la fonction de listage (cache_repertorier).
	$p->code = "calculer_liste_cache(${plugin}, ${filtres})";

	return $p;
}

/**
 * @internal
 *
 * @param string $plugin
 * @param array  $filtres
 *
 * @return array
 */
function calculer_liste_cache($plugin, $filtres = array()) {
	include_spip('inc/ezcheck_cache');

	return cache_repertorier($plugin, $filtres);
}
