<?php
/**
 * Utilisations de pipelines par Material Icônes
 *
 * @plugin     Material Icônes
 * @copyright  2019
 * @author     chankalan
 * @licence    GNU/GPL
 * @package    SPIP\Materialicons\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}



// quelques styles pour commencer, en prive et en public
function materialicons_header_prive($flux){
    $flux .= '<link rel="stylesheet" href="'. _DIR_PLUGIN_MATERIALICONS .'css/materialicons.css" type="text/css" media="all" />';
    return $flux;
}
function materialicons_insert_head_css($flux){
    $flux .= '<link rel="stylesheet" href="'. _DIR_PLUGIN_MATERIALICONS .'css/materialicons.css" type="text/css" media="all" />';
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
function materialicons_affiche_milieu($flux) {
	$texte = '';
	$e = trouver_objet_exec($flux['args']['exec']);



	// materialicons sur les articles, auteurs, rubriques
	if (!$e['edition'] and in_array($e['type'], array('article', 'auteur', 'rubrique'))) {
		$texte .= recuperer_fond('prive/objets/editer/liens', array(
			'table_source' => 'materialicons',
			'objet' => $e['type'],
			'id_objet' => $flux['args'][$e['id_table_objet']]
		));
	}

	if ($texte) {
		if ($p = strpos($flux['data'], '<!--affiche_milieu-->')) {
			$flux['data'] = substr_replace($flux['data'], $texte, $p, 0);
		} else {
			$flux['data'] .= $texte;
		}
	}

	return $flux;
}




/**
 * Optimiser la base de données
 *
 * Supprime les liens orphelins de l'objet vers quelqu'un et de quelqu'un vers l'objet.
 *
 * @pipeline optimiser_base_disparus
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function materialicons_optimiser_base_disparus($flux) {

	include_spip('action/editer_liens');
	$flux['data'] += objet_optimiser_liens(array('materialicon'=>'*'), '*');

	return $flux;
}
