<?php
/**
 * Utilisations de pipelines par Polyhiérarchie configurable
 *
 * @plugin     Polyhiérarchie configurable
 * @copyright  2013
 * @author     Les Développements Durables
 * @licence    GNU/GPL v3
 * @package    SPIP\Polyconf\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) return;
	
/**
 * Utilisation du pipeline afficher_contenu_objet
 * 
 * - Insertion d'un sélecteur de rubriques dans les objets configurés pour ça
 *
 * @pipeline afficher_contenu_objet
 * 
 * @param array $flux
 *     Données du pipeline
 * @return array
 *     Données du pipeline
 */
function polyconf_afficher_complement_objet($flux) {
	include_spip('inc/config');
	
	// Ajouter un bloc de liaison avec les rubriques sur les objets configurés pour ça
	if (
		$table = table_objet_sql($flux['args']['type'])
		and in_array($table, lire_config('polyhier/lier_objets', array()))
	) {
		$id = $flux['args']['id'];
		$infos = recuperer_fond('prive/objets/editer/polyhierarchie', array(
			'objet'=>$flux['args']['type'],
			'id_objet'=>$id,
			'editable'=>autoriser('associerrubrique', $flux['args']['type'], $id) ? 'oui':'non'
		));
		$flux['data'] .= $infos;
	}
	
	return $flux;
}

?>
