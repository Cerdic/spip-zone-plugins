<?php
/**
 * Utilisations de pipelines par Elasticsearch
 *
 * @plugin     Elasticsearch
 * @copyright  2016
 * @author     Guy Cesaro
 * @licence    GNU/GPL
 * @package    SPIP\Elasticsearch\Pipelines
 */

if (!defined('_ECRIRE_INC_VERSION')) return;



function elasticsearch_post_edition($flux){

	//si pas d'index configuré on sort
	include_spip('inc/config');
	$index = lire_config('elasticsearch_config/nom_index_ecriture');
	if(!$index)
		return $flux;
		
	include_spip('base/connect_sql'); // pour être sûr d'avoir objet_type()

	$objet = $id_objet = false;

	// On trouve le type de l'objet
	if (isset($flux['args']['type'])){
		$objet = $flux['args']['type'];
	}
	elseif (isset($flux['args']['table'])){
		$objet = objet_type($flux['args']['table']);
	}
	// On trouve son identifiant
	if (isset($flux['args']['id_objet'])){
		$id_objet = $flux['args']['id_objet'];
	}

	// Si on a ce qu'il faut, on continue
	if ($objet and $id_objet and ($objet=='article' or $objet=='document')){

		include_spip('elasticsearch_fonctions');
		// on reindexe l'objet
		$job_todo = 0;
		if (isset($flux['data']['date']) AND ($flux['data']['date'] > date("Y-m-d H:i:s"))) {
		// la date est supérieure à la date actuuelle, on est cas d'un article postdaté, on lance le job après sa publication réelle, sinon
			$job_todo = strtotime($flux['data']['date']) - time();
		}
		//on indexe tout à chaque modif pour tous les objets, sauf pour un document car le traitement est lourd, on vérifie que c'est un changement de fichier pour ce dernier
		if ($objet!='document' OR ($objet=='document' AND $flux['args']['action']=='ajouter_document')) {
			job_queue_add('indexer_un_objet', 'indexation elasticsearch '.$objet.$id_objet, array(0=>$id_objet, 1=>$objet), 'inc/indexer_index', true, time()+ $job_todo);
		}
		elseif ($objet=='document' AND objet_est_indexe($objet, $id_objet)) {
			job_queue_add('indexer_un_objet', 'indexation elasticsearch '.$objet.$id_objet, array(0=>$id_objet, 1=>$objet, 2=>'update'), 'inc/indexer_index', true, time()+ $job_todo);		
		}
		include_spip('action/editer_objet');
		//si l'objet n'est pas publié et qu'il est dans l'index on le vire !
		if ((!objet_test_si_publie($objet, $id_objet)) AND objet_est_indexe($objet, $id_objet)) {
			job_queue_add('desindexer_un_objet', 'desindexation elasticsearch '.$objet.$id_objet, array(0=>$id_objet, 1=>$objet), 'inc/indexer_index', true);		
		}
	}

	return $flux;
}


function elasticsearch_post_edition_lien($flux){

	//si pas d'index configuré on sort
	include_spip('inc/config');
	$index = lire_config('elasticsearch_config/nom_index_ecriture');
	if(!$index)
		return $flux;

	$objet = $id_objet = false;
	// On trouve le type de l'objet
	if (isset($flux['args']['objet'])){
		$objet = $flux['args']['objet'];
	}

	// On trouve son identifiant
	if (isset($flux['args']['id_objet'])){
		$id_objet = $flux['args']['id_objet'];
	}
	// Si on a ce qu'il faut, on continue
	if ($objet and $id_objet and ($objet=='article' or $objet=='document')){
		// on reindexe l'objet
		$job_todo = 0;
		if (isset($flux['data']['date']) AND ($flux['data']['date'] > date("Y-m-d H:i:s"))) {
		// la date est supérieure à la date actuuelle, on est cas d'un article postdaté, on lance le job après sa publication réelle, sinon
			$job_todo = strtotime($flux['data']['date']) - time();
		}
		job_queue_add('indexer_un_objet', 'indexation elasticsearch '.$objet.$id_objet, array(0=>$id_objet, 1=>$objet), 'inc/indexer_index', true, time()+ $job_todo);

	}

		
	
	return $flux;
}

function elasticsearch_boite_infos($flux){
	
	//si pas d'index configuré on sort
	include_spip('inc/config');
	$index = lire_config('elasticsearch_config/nom_index_ecriture');
	if(!$index)
		return $flux;
	
	$objet = $id_objet = false;

	if (
		($objet = $flux['args']['type']
		and $id_objet = intval($flux['args']['id']))
		and ($objet=='article' or $objet=='document')

	) {
	include_spip('elasticsearch_fonctions');
	$result = objet_est_indexe($objet, $id_objet);

		if($result) {
			$indexe = "est indexé";
		}
		else {
			$indexe = "n'est pas dans l'index";
		}
		$cherche = "/(<div[^>]*class=('|\")numero.*?<\/div>)/is";
		$remplace = '$1' . "$indexe\n";
		$flux['data'] = preg_replace($cherche, $remplace, $flux['data']);
	}

	return $flux;
}