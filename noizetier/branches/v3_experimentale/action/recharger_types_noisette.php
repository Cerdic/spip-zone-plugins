<?php
/**
 * Ce fichier contient l'action `recharger_types_noisette` lancée par un utilisateur pour
 * recharger le fichier de configuration de chaque noisette de façon sécurisée.
 *
 * @package SPIP\NOIZETIER\TYPE_NOISETTE\ACTION
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Cette action permet à l'utilisateur de recharger en base de données, de façon sécurisée,
 * les types de noisette à partir de leur fichier YAML.
 *
 * Cette action est réservée aux utilisateurs pouvant utiliser le noiZetier.
 * Elle ne nécessite aucun argument.
 *
 * @return void
 */
function action_recharger_types_noisette_dist() {

	// Sécurisation.
	// -- Aucun argument attendu.

	// Verification des autorisations : pour recharger les noisettes il suffit
	// d'avoir l'autorisation minimale d'accéder au noizetier.
	if (!autoriser('noizetier')) {
		include_spip('inc/minipres');
		echo minipres();
		exit();
	}

	// Rechargement des types de noisette : on force le recalcul complet, c'est le but.
	include_spip('inc/ncore_type_noisette');
	type_noisette_charger('noizetier', true);
}
