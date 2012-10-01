<?php
/**
 * Plugin Annonces
 * (c) 2012 apéro spip
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/*
 * Un fichier de pipelines permet de regrouper
 * les fonctions de branchement de votre plugin
 * sur des pipelines existants.
 */


/**
 * Ajouter les objets sur les vues de rubriques
**/
function spipad_affiche_enfants($flux) {
	if ($e = trouver_objet_exec($flux['args']['exec'])
		AND $e['type'] == 'rubrique'
		AND $e['edition'] == false) {

		$id_rubrique = $flux['args']['id_rubrique'];
		$lister_objets = charger_fonction('lister_objets', 'inc');

		$bouton = '';
		if (autoriser('creeraddans', 'rubrique', $id_rubrique)) {
			$bouton .= icone_verticale(_T("ad:icone_creer_ad"), generer_url_ecrire("ad_edit", "id_rubrique=$id_rubrique"), "ad-24.png", "new", "right")
					. "<br class='nettoyeur' />";
		}

		$flux['data'] .= $lister_objets('ads', array('titre'=>_T('ad:titre_ads_rubrique') , 'id_rubrique'=>$id_rubrique, 'par'=>'titre'));
		$flux['data'] .= $bouton;

	}
	return $flux;
}


/**
 * Ajout de contenu sur certaines pages,
 * notamment des formulaires de liaisons entre objets
 */
function spipad_affiche_milieu($flux) {
	$texte = "";
	$e = trouver_objet_exec($flux['args']['exec']);

	// auteurs sur les ads
	if (!$e['edition'] AND in_array($e['type'], array('ad'))) {
		$texte .= recuperer_fond('prive/objets/editer/liens', array(
			'table_source' => 'auteurs',
			'objet' => $e['type'],
			'id_objet' => $flux['args'][$e['id_table_objet']]
		));
	}


	// ads sur les articles, auteurs, documents, groupes_mots, messages, mots, rubriques
	if (!$e['edition'] AND in_array($e['type'], array('article', 'auteur', 'document', 'groupe_mots', 'message', 'mot', 'rubrique'))) {
		$texte .= recuperer_fond('prive/objets/editer/liens', array(
			'table_source' => 'ads',
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
function spipad_affiche_auteurs_interventions($flux) {
	if ($id_auteur = intval($flux['args']['id_auteur'])) {

		$flux['data'] .= recuperer_fond('prive/objets/liste/ads', array(
			'id_auteur' => $id_auteur,
			'titre' => _T('ad:info_ads_auteur')
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
function spipad_optimiser_base_disparus($flux){
	include_spip('action/editer_liens');
	$flux['data'] += objet_optimiser_liens(array('ad'=>'*'),'*');
	return $flux;
}

?>