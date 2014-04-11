<?php
/**
 * @plugin     Les mots-clefs des articles
 * @copyright  2013
 * @author     chankalan
 * @licence    GNU/GPL
 * @package    SPIP\lesmotsdesarticles\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Ajout d'un bouton sur la vue des rubrique
 * pour accéder à la page de gestion
 * des mots-clefs des articles de cette rubrique
 *
 * @pipeline affiche_enfants
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function lesmotsdesarticles_affiche_enfants($flux) {
	if ($e = trouver_objet_exec($flux['args']['exec'])
		AND $e['type'] == 'rubrique'
		AND $e['edition'] == false) {

		$id_rubrique = $flux['args']['id_rubrique'];

		$bouton = '';
		if (autoriser('creerarticledans', 'rubrique', $id_rubrique)) {
			$bouton .= icone_verticale(_T("lesmotsdesarticles:gerer_lesmotsdesarticles"), generer_url_ecrire("lesmotsdesarticles", "id_rubrique=$id_rubrique"), "lesmotsdesarticles-24.png", "edit", "right")
					. "<br class='nettoyeur' />";
		}

		$flux['data'] .= $bouton;

	}
	return $flux;
}

?>