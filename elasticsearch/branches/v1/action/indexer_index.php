<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function action_indexer_index_dist(){
	
	$securiser_action = charger_fonction('securiser_action','inc');
	$arg = $securiser_action();
	include_spip('inc/indexer_index');
	indexer_index_elasticsearch('articles');
	indexer_index_elasticsearch('documents');
}
