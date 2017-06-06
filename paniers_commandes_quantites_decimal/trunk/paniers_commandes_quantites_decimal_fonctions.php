<?php
/**
 * Fonctions du plugin Commandes
 *
 * @plugin     Commandes
 * @copyright  2014
 * @author     Ateliers CYM, Matthieu Marcillaud, Les Développements Durables
 * @licence    GPL 3
 * @package    SPIP\Commandes\Fonctions
 */

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Filtre pour utiliser la fonction d'arrondi des quantite
 * @param int|float $quantite
 * @param string $objet
 * @param int $id_objet
 * @return int|float
 */
function filtre_commandes_arrondir_quantite_dist($quantite, $objet='', $id_objet=0) {
	$commandes_arrondir_quantite = charger_fonction('commandes_arrondir_quantite', 'inc');
	return $commandes_arrondir_quantite($quantite, $objet, $id_objet);
}

/**
 * Afficher la quantite si differente de 1
 * @param int|float $quantite
 * @param string $objet
 * @param int $id_objet
 * @return string
 */
function filtre_commandes_afficher_quantite_descriptif_dist($quantite) {
	if (intval($quantite*1000) !== 1000) {
		$commandes_afficher_quantite = charger_filtre('commandes_afficher_quantite');
		return $commandes_afficher_quantite($quantite) . " &times;";
	}
	return '';
}


/**
 * Afficher la quantite, en arrondissant eventuellement
 * (par defaut fait juste l'arrondi int natif)
 * @param int|float $quantite
 * @param string $objet
 * @param int $id_objet
 * @return string
 */
function filtre_commandes_afficher_quantite_dist($quantite, $objet='', $id_objet=0) {
	if (intval($quantite*1000) === 1000 * intval($quantite)) {
		return intval($quantite);
	}

	$commandes_arrondir_quantite = charger_fonction('commandes_arrondir_quantite', 'inc');
	return $commandes_arrondir_quantite($quantite, $objet, $id_objet);
}
