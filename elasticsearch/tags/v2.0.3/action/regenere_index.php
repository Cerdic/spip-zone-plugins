<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_regenere_index_dist(){
	
	$securiser_action = charger_fonction('securiser_action','inc');
	$arg = $securiser_action();
	$index = lire_config('elasticsearch_config/nom_index');
	$serveur = lire_config('elasticsearch_config/url_serveur');
	$new_index = $index.'-'.time();
	$test_url = $serveur. '/' . $new_index;

	include_spip('public/assembler');
	$json = recuperer_fond('json/create_index.json');
	include_spip('phpcurl_fonctions');
	$create_index = phpcurl_put($test_url, $json);
	ecrire_config('elasticsearch_config/nom_index_ecriture', $new_index);
	include_spip('inc/indexer_index');
	indexer_index_elasticsearch('articles');
	indexer_index_elasticsearch('documents');
}