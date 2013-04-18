<?php
/**
 * Utilisations de pipelines par Amap
 *
 * @plugin     Amap
 * @copyright  2013
 * @author     Stephane Moulinet
 * @author     E-cosystems
 * @author     Pierre KUHN
 * @licence    GPL v3
 * @package    SPIP\Amap\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Optimiser la base de données en supprimant les liens orphelins
 * de l'objet vers quelqu'un et de quelqu'un vers l'objet.
 *
 * @pipeline optimiser_base_disparus
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function amap_optimiser_base_disparus($flux){
	include_spip('action/editer_liens');
	$flux['data'] += objet_optimiser_liens(array('amap_responsable'=>'*', 'amap_livraison'=>'*'),'*');
	return $flux;
}

/**
 * Ajout de contenu sur les pages auteurs,
 * sur infos_perso principalement
 *
 * @pipeline affiche_gauche
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function amap_affiche_gauche($flux){
	include_spip('inc/presentation');
	if ($flux['args']['exec'] == 'infos_perso'){
		$flux['data'] .= recuperer_fond('prive/squelettes/inclure/affiche_boite', array('id_auteur'=>$flux['args']['id_auteur']));
	}
return $flux;
}

?>