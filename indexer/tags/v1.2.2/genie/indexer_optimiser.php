<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

function genie_indexer_optimiser_dist($date) {
	include_spip('inc/indexer');
	
	$sphinxql = new Sphinx\SphinxQL\SphinxQL(SPHINX_SERVER_HOST, SPHINX_SERVER_PORT);
	$sphinxql->query('optimize index '.SPHINX_DEFAULT_INDEX);
	
	return 1;
}
