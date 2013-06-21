<?php
/**
 * Utilisations de pipelines par Dépublication 2
 *
 * @plugin     Dépublication 2
 * @copyright  2013
 * @author     Web
 * @licence    GNU/GPL
 * @package    SPIP\Depublication2\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) return;
	

/*
 * Un fichier de pipelines permet de regrouper
 * les fonctions de branchement de votre plugin
 * sur des pipelines existants.
 */



/**
 * Ajout de contenu sur certaines pages,
 * notamment des formulaires de liaisons entre objets
 *
 * @pipeline affiche_milieu
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
 function depublication2_taches_generales_cron($taches) {

	$taches['depublicationer'] = 600; // par exemple toutes les 10 minutes, ne pas descendre en dessous de 30 secondes !
	spip_log('execution tache cron dépublication','depublication');
	return $taches;
}
 
function depublication2_affiche_milieu($flux) {
	$texte = "";
	$e = trouver_objet_exec($flux['args']['exec']);


	// articles_depublication sur les articles
	if (!$e['edition'] AND in_array($e['type'], array('article'))) {
		$texte .= recuperer_fond('prive/objets/editer/liens', array(
			'table_source' => 'articles_depublication',
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
 * Optimiser la base de données en supprimant les liens orphelins
 * de l'objet vers quelqu'un et de quelqu'un vers l'objet.
 *
 * @pipeline optimiser_base_disparus
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function depublication2_optimiser_base_disparus($flux){
	include_spip('action/editer_liens');
	$flux['data'] += objet_optimiser_liens(array('art_depub'=>'*'),'*');
	return $flux;
}

?>