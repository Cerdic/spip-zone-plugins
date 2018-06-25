<?php
/**
 * Ce fichier contient l'action `recharger_pages` lancée par un utilisateur pour
 * recharger le fichier de configuration de chaque page de façon sécurisée.
 *
 * @package SPIP\NOIZETIER\PAGE\ACTION
 */
if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Cette action permet à l'utilisateur de recharger en base de données, de façon sécurisée,
 * l'ensemble des pages à partir de leur fichier XML ou YAML.
 *
 * Cette action est réservée aux utilisateurs pouvant utiliser le noiZetier.
 * Elle ne nécessite aucun argument.
 *
 * @return void
 */
function action_recharger_pages_dist() {

	// Sécurisation.
	// -- Aucun argument attendu.

	// Vérification des autorisations : pour recharger les pages ou les noisettes il suffit
	// d'avoir l'autorisation minimale d'accéder au noizetier.
	if (!autoriser('noizetier')) {
		include_spip('inc/minipres');
		echo minipres();
		exit();
	}

	// Rechargement des pages : on force le recalcul complet, c'est le but.
	include_spip('inc/noizetier_page');
	noizetier_page_charger(true);
}
