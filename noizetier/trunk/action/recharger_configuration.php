<?php
/**
 * Ce fichier contient l'action `recharger_configuration` lancée par un utilisateur pour
 * recharger le fichier de configuration de chaque page ou chaque noisette de façon sécurisée.
 *
 * @package SPIP\NOIZETIER\ACTION
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Cette action permet à l'utilisateur de recharger en base de données, de façon sécurisée,
 * soit l'ensemble des pages à partir de leur fichier XML ou YAML, soit l'ensemble des types de noisette
 * à partir de leur fichier YAML.
 *
 * Cette action est réservée aux utilisateurs pouvant utiliser le noiZetier.
 * Elle nécessite l'objet concerné soit page ou type_noisette.
 *
 * @return void
 */
function action_recharger_configuration_dist() {

	// Securisation et autorisation.
	// L'action attend un seul argument:
	// - noisette, pour recharger toutes les noisettes
	// - page, pour recharger toutes les pages
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$argument = $securiser_action();

	// Verification des autorisations : pour recharger les pages ou les noisettes il suffit
	// d'avoir l'autorisation minimale d'accéder au noizetier.
	if (!autoriser('noizetier')) {
		include_spip('inc/minipres');
		echo minipres();
		exit();
	}

	include_spip('noizetier_fonctions');
	if ($argument == 'page') {
		include_spip('noizetier_fonctions');
		noizetier_page_charger();
	} elseif ($argument == 'type_noisette') {
		include_spip('inc/ncore_type_noisette');
		type_noisette_charger('noizetier');
	}
}
