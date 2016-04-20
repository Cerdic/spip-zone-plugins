<?php
/**
 * Utilisations de pipelines par Création de cartes
 *
 * @plugin     Création de cartes
 * @copyright  2016
 * @author     kent1
 * @licence    GNU/GPL
 * @package    SPIP\Cartes\Pipelines
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
function cartes_affiche_milieu($flux) {
	$texte = "";
	$e = trouver_objet_exec($flux['args']['exec']);

	// auteurs sur les cartes
	if (!$e['edition'] AND in_array($e['type'], array('carte'))) {
		$texte .= recuperer_fond('prive/objets/editer/liens', array(
			'table_source' => 'auteurs',
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
 *
 * @pipeline affiche_auteurs_interventions
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function cartes_affiche_auteurs_interventions($flux) {
	if ($id_auteur = intval($flux['args']['id_auteur'])) {

		$flux['data'] .= recuperer_fond('prive/objets/liste/cartes', array(
			'id_auteur' => $id_auteur,
			'titre' => _T('carte:info_cartes_auteur')
		), array('ajax' => true));

	}
	return $flux;
}



/**
 * Optimiser la base de données 
 * 
 * Supprime les objets à la poubelle.
 *
 * @pipeline optimiser_base_disparus
 * @param  array $flux Données du pipeline
 * @return array       Données du pipeline
 */
function cartes_optimiser_base_disparus($flux){

	sql_delete("spip_cartes", "statut='poubelle' AND maj < " . $flux['args']['date']);

	return $flux;
}

function cartes_pre_boucle($boucle){
	if ($boucle->type_requete == 'cartes') {
		$boucle->select[]= 'AsText(cartes.bounds) AS geometry_map';
	}
	return $boucle;
}