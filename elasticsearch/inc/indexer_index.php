<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function indexer_index_elasticsearch($objets = 'articles'){

	$pas = $fin = $suivant = '';
	$pas = _ELASTICSEARCH_PAS_INDEXATION;
	if($objets != 'articles')
		$pas = 20;
	include_spip('public/assembler');
	$fin = recuperer_fond('inclure/put_'.$objets, array('debut'=> 0, 'pas' => $pas, 'total' => 'oui'));
	$fin = intval($fin);

	job_queue_add('indexer_bloc', 'indexation elasticsearch en cours', array(0=>0, 1=>$pas, 2=>$fin, 3=>$objets), 'inc/indexer_index', true, time()+20);
	ecrire_config('elasticsearch_config/index_genere', 'en_cours');

}


function indexer_bloc($debut, $pas, $fin, $objets) {

	if($debut<=$fin) {
	$done = '';
	include_spip('public/assembler');

	$done = recuperer_fond('inclure/put_'.$objets, array('debut'=> $debut, 'pas' => $pas));

		if($done){
		$suivant = $debut+$pas;
		job_queue_add('indexer_bloc', 'indexation elasticsearch en cours', array(0=>$suivant, 1=>$pas, 2=>$fin, 3=>$objets), 'inc/indexer_index', true, time()+20);
		}
		else {
		spip_log('erreur '.$debut.' à '.$suivant, 'erreur_indexation'.$objets._LOG_ERREUR);
		$suivant = $suivant+$pas;
		job_queue_add('indexer_bloc', 'indexation elasticsearch en cours', array(0=>$suivant, 1=>$pas, 2=>$fin, 3=>$objets), 'inc/indexer_index', true, time()+20);
		}
	}
	else {
		// s'il n'y a plus qu'un type d'objet à indexer on lance le job pour changer d'alias à la fin des travaux
		$res = sql_select("id_job", "spip_jobs", "fonction='indexer_bloc'");
			if (($res and sql_count($res)==1) OR !$res) {
			job_queue_add('changer_alias_watch', 'changement d\'alias de recherche à venir', '', 'inc/indexer_index', true);
		}
	}
}

function changer_alias_watch() {
		//s'il y a encore des indexer_bloc, l'indexation est en cours
		include_spip("base/abstract_sql");
		$pas_fini = sql_getfetsel("id_job", "spip_jobs", "fonction='indexer_bloc'");

		if (!$pas_fini) {
			ecrire_config('elasticsearch_config/index_genere', time());
			// la premiere indexation, pas de switch
			if (lire_config('elasticsearch_config/initialisation') == 'non') {
			ecrire_config('elasticsearch_config/nom_index_old',lire_config('elasticsearch_config/nom_index_lecture'));
			ecrire_config('elasticsearch_config/nom_index_lecture', lire_config('elasticsearch_config/nom_index_ecriture'));
			
			//on switch l'alias sur le nouvel index
			$serveur = lire_config('elasticsearch_config/url_serveur');
			$url_alias = $serveur. '/_aliases';
			$switch_index = recuperer_fond('inclure/switch_index');
			include_spip('phpcurl_fonctions');
			$create_alias = phpcurl_post($url_alias, $switch_index);
	
			//on supprime l'ancien index
			$url_delete = $serveur.'/'.lire_config('elasticsearch_config/nom_index_old');
			include_spip('phpcurl_fonctions');
			$delete_index = phpcurl_delete($url_delete);
			}
			else {
				ecrire_config('elasticsearch_config/initialisation', 'non');
			}

		}
		else {
		// relance dans 2 minutes
			job_queue_add('changer_alias_watch', 'changement d\'alias de recherche à venir', '', 'inc/indexer_index', true, time()+120);	
		}
	
}

function indexer_un_objet($id_objet, $objet, $fond='put') {

	include_spip('public/assembler');
	recuperer_fond('inclure/'. $fond .'_'.$objet, array('id_objet'=> $id_objet));


}

function desindexer_un_objet($id_objet, $objet) {
	include_spip('public/assembler');
	recuperer_fond('inclure/delete_objet', array('id_objet'=> $id_objet, 'objet'=> $objet));
}