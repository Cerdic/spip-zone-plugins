<?php

if (!defined('_ECRIRE_INC_VERSION')) return;


function inc_sphinxql_to_array_dist($u){
	include_spip('inc/indexer');

	$sphinx = new Sphinx\SphinxQL(SPHINX_SERVER_HOST, SPHINX_SERVER_PORT);

	return $sphinx->allfetsel($u);
}

