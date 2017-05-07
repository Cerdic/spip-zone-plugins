<?php


if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}
include_spip('inc/config');
include_spip('inc/charsets');


function formulaires_configurer_elasticsearchindex_charger() {
	


	$url_serveur = lire_config('elasticsearch_config/url_serveur');
	if(!$url_serveur)
		return;
	$nom_index = lire_config('elasticsearch_config/nom_index');
	$valeurs = array();
	$valeurs['editable'] = true;
	if (isset($nom_index)) {
		$valeurs['nom_index'] = $nom_index;
		$valeurs['editable'] = false;
	}
	else {
		$valeurs['nom_index'] = '';
	}
		
	
	return $valeurs;
}

function formulaires_configurer_elasticsearchindex_verifier() {
	$erreurs=array();
	

	$serveur = lire_config('elasticsearch_config/url_serveur');

	$nom_index = translitteration(_request('nom_index'));
	$nom_index = preg_replace(',[[:punct:][:space:]]+,u', '_', $nom_index);
	$nom_index = preg_replace(',\.([^.]+)$,', '', $nom_index);
	
	if($nom_index != _request('nom_index')) {
		$erreurs['message_erreur'] = 'Le nom n\'est pas valide, nom proposé : ' . $nom_index;
		set_request('nom_index', $nom_index);
		return $erreurs;
	}
	$test_url = $serveur. '/' . $nom_index;
	include_spip('phpcurl_fonctions');
	$index = phpcurl_get($test_url);
	$index = json_decode($index, true);
	if($index[$nom_index]['settings']['index']['creation_date'])
		$erreurs['message_erreur'] = 'Cet index est déjà crée';

	return $erreurs;
}

function formulaires_configurer_elasticsearchindex_traiter($retours='') {
	$retours = array('message_ok'=>'');
	

	$serveur = lire_config('elasticsearch_config/url_serveur');
	$nom_index = translitteration(_request('nom_index'));
	$nom_index = preg_replace(',[[:punct:][:space:]]+,u', '_', $nom_index);
	$nom_index = preg_replace(',\.([^.]+)$,', '', $nom_index);
	$nom_index_lecture = $nom_index_ecriture = $nom_index.'-'.time();
	$test_url = $serveur. '/' . $nom_index_lecture;

	include_spip('public/assembler');
	$json = recuperer_fond('json/create_index.json');
	include_spip('phpcurl_fonctions');
	$create_index = phpcurl_put($test_url, $json);

	$ok = json_decode($create_index, true);
	if ($ok['acknowledged'] == true) {
		$nom_alias = $nom_index . "alias";

		$json = recuperer_fond('json/create_alias.json', array('nom_index'=> $nom_index, 'nom_index_lecture'=> $nom_index_lecture));
		$url = $serveur. '/_aliases';
		include_spip('phpcurl_fonctions');
		$create_alias = phpcurl_post($url, $json);
		ecrire_config('elasticsearch_config/nom_index', $nom_index);
		ecrire_config('elasticsearch_config/nom_index_lecture', $nom_index_lecture);
		ecrire_config('elasticsearch_config/nom_index_ecriture', $nom_index_ecriture);
		ecrire_config('elasticsearch_config/nom_alias', $nom_alias);
		ecrire_config('elasticsearch_config/initialisation', 'oui');
		$retours['message_ok'] = $nom_index . ' a été crée';

		return $retours;

	}
	else
		spip_log("erreur $ok",'index_create'._LOG_INFO_IMPORTANTE);
	
}