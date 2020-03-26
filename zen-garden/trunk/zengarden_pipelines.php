<?php
/**
 * Plugin Zen-Garden pour Spip 3.0
 * Licence GPL (c) 2006-2013 Cedric Morin
 * 
 * Fichier des utilisations des pipelines du plugin
 * 
 * @package SPIP\Zen-Garden\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Insertion dans le pipeline header_prive
 * 
 * Insertion des deux scripts javascript pour les tooltips dans l'espace privé
 * 
 * @param string $flux
 * 		Le code html du head de l'espace privé
 * @return string $flux
 * 		Le code html du head complété
 */
function zengarden_header_prive($flux){

	return $flux;
}
