<?php
/**
 * Utilisations de pipelines par Itinéraires
 *
 * @plugin     Itinéraires
 * @copyright  2013
 * @author     Les Développements Durables
 * @licence    GNU/GPL v3
 * @package    SPIP\Itineraires\Pipelines
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
function itineraires_affiche_milieu($flux) {
	$texte = "";
	$e = trouver_objet_exec($flux['args']['exec']);

	// auteurs sur les itineraires
	if (!$e['edition'] AND in_array($e['type'], array('itineraire'))) {
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
function itineraires_affiche_auteurs_interventions($flux) {
	if ($id_auteur = intval($flux['args']['id_auteur'])) {

		$flux['data'] .= recuperer_fond('prive/objets/liste/itineraires', array(
			'id_auteur' => $id_auteur,
			'titre' => _T('itineraire:info_itineraires_auteur')
		), array('ajax' => true));

	}
	return $flux;
}

/**
 * Optimiser la base de donnees en supprimant les trucs à la poubelle 
 * et les liens orphelins
 * et les locomotions
 *
 * @param array $flux
 * @return array
 */
function itineraires_optimiser_base_disparus($flux){
	include_spip('action/editer_liens');
	
	// Les itinéraires à la poubelle
	$flux['data'] +=  sql_delete("spip_itineraires", "statut='poubelle' AND maj < ".$flux['args']['date']);
	
	// Les locomotions d'itinéraires qui n'existent plus
	$flux['data'] += sql_delete(
		'spip_itineraires_locomotions as L left join spip_itineraires as I on L.id_itineraire=I.id_itineraire', 
		'I.id_itineraire is null'
	); 
	
	// Les liens
	$flux['data'] += objet_optimiser_liens(array('itineraire'=>'*'),'*');
	
	return $flux;
}


?>
