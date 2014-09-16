<?php
/**
 * Plugin projets
 * (c) 2012 Cyril Marion
 * Licence GNU/GPL
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
 */
function projets_affiche_milieu($flux) {
	$texte = "";
	$e = trouver_objet_exec($flux['args']['exec']);
	$objets_selectionnes = lire_config('projets/objets', array());
	if (count($objets_selectionnes) > 0) {
		include_spip('base/objets');
		foreach ($objets_selectionnes as $key => $value) {
			$objets_selectionnes[$key] = objet_type($value);
		}
	}
	$objets_selectionnes = array_filter($objets_selectionnes);

	// auteurs sur les projets et cadres de projet
	if (!$e['edition'] AND in_array($e['type'], array('projet', 'projets_cadre'))) {
		$texte .= recuperer_fond('prive/objets/editer/liens', array(
			'table_source' => 'auteurs',
			'objet' => $e['type'],
			'id_objet' => $flux['args'][$e['id_table_objet']]
		));
	}

	if (!$e['edition'] AND in_array($e['type'], $objets_selectionnes)) {
		$texte .= recuperer_fond('prive/objets/editer/liens', array(
			'table_source' => 'projets',
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
 * Ajout de liste sur la vue d'un auteur
 */
function projets_affiche_auteurs_interventions($flux) {
	if ($id_auteur = intval($flux['args']['id_auteur'])) {

		$flux['data'] .= recuperer_fond('prive/objets/liste/projets', array(
			'id_auteur' => $id_auteur,
			'titre' => _T('projet:info_projets_auteur')
		), array('ajax' => true));

		$flux['data'] .= recuperer_fond('prive/objets/liste/projets_cadres', array(
			'id_auteur' => $id_auteur,
			'titre' => _T('projets_cadre:info_projets_cadres_auteur')
		), array('ajax' => true));

	}
	return $flux;
}


/**
 * Optimiser la base de donnees en supprimant les liens orphelins
 * de l'objet vers quelqu'un et de quelqu'un vers l'objet.
 *
 * @param int $n
 * @return int
 */
function projets_optimiser_base_disparus($flux){
	include_spip('action/editer_liens');
	$flux['data'] += objet_optimiser_liens(array('projet'=>'*'),'*');
	return $flux;
}



?>
