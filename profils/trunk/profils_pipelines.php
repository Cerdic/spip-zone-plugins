<?php
/**
 * Utilisations de pipelines par Profils
 *
 * @plugin     Profils
 * @copyright  2018
 * @author     Les Développements Durables
 * @licence    GNU/GPL
 * @package    SPIP\Profils\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Optimiser la base de données
 *
 * Supprime les objets à la poubelle.
 *
 * @pipeline optimiser_base_disparus
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function profils_optimiser_base_disparus($flux) {
	sql_delete('spip_profils', "statut='poubelle' AND maj < " . $flux['args']['date']);

	return $flux;
}
