<?php
/**
 * Utilisations de pipelines par DayFill
 *
 * @plugin     DayFill
 * @copyright  2013
 * @author     Cyril Marion
 * @licence    GNU/GPL
 * @package    SPIP\Dayfill\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Ajouter les objets sur les vues d'un projet
 *
 * @pipeline affiche_enfants
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
**/

function dayfill_affiche_enfants($flux) {

	if ($e = trouver_objet_exec($flux['args']['exec'])
		AND $e['type'] == 'projet'
		AND $e['edition'] == false) {

		$id_projet = $flux['args']['id_projet'];
		$lister_objets = charger_fonction('lister_objets', 'inc');

		$bouton = '';
		if (autoriser('creerprojetsactivitedans', 'projet', $id_projet)) {
			include_spip('inc/presentation');
			$bouton .= icone_verticale(_T("projets_activite:icone_creer_projets_activite"), generer_url_ecrire("projets_activite_edit", "id_projet=$id_projet"), "projets_activite-24.png", "new", "right")
					. "<br class='nettoyeur' />";
		}

		$flux['data'] .= $lister_objets('projets_activites', array('titre'=>_T('projets_activite:titre_projets_activite_projet') , 'id_projet'=>$id_projet, 'par'=>'date_debut'));
		$flux['data'] .= $bouton;

	}
	return $flux;
}


?>
