<?php

if (!defined('_ECRIRE_INC_VERSION')) return;


function inc_sphinxql_to_array_dist($u, $debut=''){
   include_spip('inc/indexer');

   // hacks pour la pagination
   // attention ce preg_replace est vraiment tres fragile revoir TODO.
   if ($debut = intval($debut)) {
       $u = preg_replace('/ FACET /', " LIMIT $debut,20 FACET ", $u, 1);
   }

	$sphinx = new Sphinx\SphinxQL\SphinxQL(SPHINX_SERVER_HOST, SPHINX_SERVER_PORT);

	$all = $sphinx->allfetsel($u);

	$total = intval($all['query']['meta']['total']);

	// pagination : rajouter $debut elements vides
	if ($debut) $all['query']['docs'] = array_pad($all['query']['docs'], -$debut - count($all['query']['docs']), 0);

	if ($total > count($all['query']['docs'])) {
		$all['query']['docs'] = array_pad($all['query']['docs'], $total, 0);
	}

	return $all;
}

