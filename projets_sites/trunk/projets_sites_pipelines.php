<?php
/**
 * Utilisations de pipelines par Projets - Sites internet
 *
 * @plugin     Projets - Sites internet
 * @copyright  2013
 * @author     Teddy Payet
 * @licence    GNU/GPL
 * @package    SPIP\Projets_sites\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) return;
	


/**
 * Ajout de contenu sur certaines pages,
 * notamment des formulaires de liaisons entre objets
 *
 * @pipeline affiche_milieu
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function projets_sites_affiche_milieu($flux) {
	$texte = "";
	$e = trouver_objet_exec($flux['args']['exec']);



	// projets_sites sur les projets
	if (!$e['edition'] AND in_array($e['type'], array('projet'))) {
		$texte .= recuperer_fond('prive/objets/editer/liens', array(
			'table_source' => 'projets_sites',
			'objet' => $e['type'],
			'id_objet' => $flux['args'][$e['id_table_objet']]
		));
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
 * Insert header prive
 */
function projets_sites_header_prive($flux){
	$flux .= '<link rel="stylesheet" href="' . _DIR_PLUGIN_PROJETS_SITES  .'css/projets_sites_prive.css" type="text/css" media="all" />';
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
function projets_sites_optimiser_base_disparus($flux){
	include_spip('action/editer_liens');
	$flux['data'] += objet_optimiser_liens(array('projet_site'=>'*'),'*');
	return $flux;
}

?>