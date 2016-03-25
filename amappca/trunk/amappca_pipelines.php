<?php
/**
 * Utilisations de pipelines par AMAP, Producteurs et Consommateurs associés
 *
 * @plugin     AMAP, Producteurs et Consommateurs associés
 * @copyright  2016
 * @author     Rien
 * @licence    GNU/GPL
 * @package    SPIP\Amappca\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) return;




/**
 * Optimiser la base de données 
 * 
 * Supprime les objets à la poubelle.
 *
 * @pipeline optimiser_base_disparus
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function amappca_optimiser_base_disparus($flux){

	sql_delete("spip_amap_periodes", "statut='poubelle' AND maj < " . $flux['args']['date']);

	return $flux;
}