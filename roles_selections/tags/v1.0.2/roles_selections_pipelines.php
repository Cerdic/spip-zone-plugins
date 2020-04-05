<?php
/**
 * Utitlisation des pipelines par le plugin Rôles de sélections éditoriales
 *
 * @plugin     Rôles de sélections éditoriales
 * @copyright  2018
 * @author     Rôles de sélections éditoriales
 * @licence    GNU/GPL
 * @package    SPIP\Roles_selections\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}


/**
 * Affichage des boutons d'action d'une sélection
 *
 * => Ajout du formulaire d'édition des rôles
 *
 * @param array $flux
 *     Tableau avec le contexte et le HTML initial
 * @return array
 *     Flux modifié
 */
function roles_selections_afficher_actions_selection($flux){

	if (
		!empty($flux['args']['id_selection'])
		and !empty($flux['args']['objet'])
		and !empty($flux['args']['id_objet'])
	) {
		$contexte = array(
			'id_selection' => $flux['args']['id_selection'],
			'objet' => $flux['args']['objet'],
			'id_objet' => $flux['args']['id_objet'],
		);
		if ($editer_roles = recuperer_fond('prive/objets/editer/roles_selections', $contexte)) {
			$flux['data'] .= $editer_roles;
		}
	}

	return $flux;
}