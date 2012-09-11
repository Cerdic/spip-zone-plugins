<?php
/**
 * Utilisations des pipelines
 *
 * @package SPIP\Elements\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Afficher la liste des éléments choisis pour un objet
 * Et permettre leur sélection et modification 
 *
 * @param array $flux Données du pipeline
 * @return array Données du pipeline
**/
function elements_afficher_fiche_objet($flux) {
	include_spip('inc/config');
	$tables_elements = lire_config('elements/objets', array());
	if (in_array(table_objet_sql($flux['args']['type']), $tables_elements)) {
		$flux['data'] .= recuperer_fond('inclure/gerer_elements', array(
			'objet'    => $flux['args']['type'],
			'id_objet' => $flux['args']['id'],
			'bloc'     => 'extra'
		), array('ajax'=>true));
	}
	return $flux;
}

?>
