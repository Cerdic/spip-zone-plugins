<?php
/**
 * Utilisations de pipelines par Évaluations
 *
 * @plugin     Évaluations
 * @copyright  2013
 * @author     Matthieu Marcillaud
 * @licence    GNU/GPL
 * @package    SPIP\Evaluations\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Inserer les css d'évaluations
 * @param string $flux
 * @return string
 */
function evaluations_insert_head_css($flux){
	$flux .= '<link rel="stylesheet" href="'.find_in_path('css/evaluations.css').'" type="text/css" media="all" />';
	return $flux;
}



/**
 * Ajout de contenu sur certaines pages,
 * notamment des formulaires de liaisons entre objets
 *
 * @pipeline affiche_milieu
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function evaluations_affiche_milieu($flux) {
	$texte = "";
	$e = trouver_objet_exec($flux['args']['exec']);

	// auteurs sur les evaluations, evaluations_syntheses
	if (!$e['edition'] AND in_array($e['type'], array('evaluation', 'evaluations_synthese'))) {
		$texte .= recuperer_fond('prive/objets/editer/liens', array(
			'table_source' => 'auteurs',
			'objet' => $e['type'],
			'id_objet' => $flux['args'][$e['id_table_objet']]
		));
	}


	// evaluations sur les articles, documents, syndic
	elseif (!$e['edition'] AND $e['type']) {
		$objets = sql_allfetsel("DISTINCT(objet) AS object", "spip_evaluations_critiques");
		$objets = array_map('reset', $objets);
		if (in_array($e['type'], $objets)) {
			$texte .= recuperer_fond('prive/objets/liste/evaluations_objet', array(
				'objet' => $e['type'],
				'id_objet' => $flux['args'][$e['id_table_objet']]
			));
		}
	}

	if ($texte) {
		if ($p=strpos($flux['data'],"<!--affiche_milieu-->"))
			$flux['data'] = substr_replace($flux['data'],$texte,$p,0);
		else
			$flux['data'] .= $texte;
	}

	return $flux;
}


/**
 * Ajout de liste sur la vue d'un auteur
 *
 * @pipeline affiche_auteurs_interventions
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function evaluations_affiche_auteurs_interventions($flux) {
	if ($id_auteur = intval($flux['args']['id_auteur'])) {

		$flux['data'] .= recuperer_fond('prive/objets/liste/evaluations', array(
			'id_auteur' => $id_auteur,
			'titre' => _T('evaluation:info_evaluations_auteur')
		), array('ajax' => true));

	}
	return $flux;
}


/**
 * Optimiser la base de données en supprimant les liens orphelins
 * de l'objet vers quelqu'un et de quelqu'un vers l'objet.
 *
 * @pipeline optimiser_base_disparus
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function evaluations_optimiser_base_disparus($flux){
	include_spip('action/editer_liens');
	$flux['data'] += objet_optimiser_liens(array('evaluation'=>'*'),'*');
	return $flux;
}

?>
