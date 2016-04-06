<?php
/**
 * fonctions utiles à ce squelette
 *
 * On vérifie que les objets éventuellement déclarés dans le pipeline
 * identifiants_utiles sont bien activés
 *
 * @plugin     Identifiants
 * @copyright  2016
 * @author     Tcharlss
 * @licence    GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/config');

if (
	$objets = lire_config('identifiants/objets', array())
	and is_array($identifiants_utiles = pipeline('identifiants_utiles'))
	and $objets_utiles = $objets_utiles = array_map('table_objet_sql', array_keys($identifiants_utiles))
	and array_diff($objets_utiles, $objets)
) {
	ecrire_config('identifiants/objets', array_merge($objets, $objets_utiles));
}
