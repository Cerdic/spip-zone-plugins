<?php
/**
 * JAZ - Joindre Automatiquement une Zone
 * Cyril MARION (c)2012 GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;


/**
 * Détecter la création d'un nouvel auteur l'ajouter aux zones restreintes automatiques
 *
 * @param array $flux
 * @return array
 */
function jaz_post_insertion($flux)
{
	if ($flux['args']['table'] == 'spip_auteurs'
		and $id_auteur = $flux['args']['id_auteur'])
	{
		// On ajoute cet auteur aux zones
		jaz_ajouter_auteur_zones($id_auteur);
	}

	return $flux;
}



/**
 * Chercher les zones automatiques à joindre et ajouter l'auteur à ces zones
 *
 * Les zones peuvent être indiquées soit :
 * - par la constante `_ZONES_AUTO_JOINTES` (dans `mes_options.php` par exemple)
 *   tel que `define('_ZONES_AUTO_JOINTES', '2:4')` où 2 et 4 sont des identifiants de zone
 * - soit, à défaut de constante, par les zones indiquées dans le formulaire de configuration
 *   du plugin (meta `jaz/zones_automatiques`).
 * 
 * @param int $id_auteur Identifiant d'auteur
 */
function jaz_ajouter_auteur_zones($id_auteur)
{

	// On cherche les Zones Auto Jointes :
	if (defined('_ZONES_AUTO_JOINTES')) {
		$zones = explode(':', _ZONES_AUTO_JOINTES);
	} else {
		include_spip('inc/config');
		$zones = explode(',', lire_config('jaz/zones_automatiques'));
	}

	if ($zones) {
		include_spip('action/editer_zone');
		zone_lier($zones, 'auteur', $id_auteur);
		spip_log('Auteur ' . $id_auteur . ' ajouté aux zones : ' . implode(', ', $zones), 'jaz');
	}

}

?>
