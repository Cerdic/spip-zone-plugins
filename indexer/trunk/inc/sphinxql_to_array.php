<?php

if (!defined('_ECRIRE_INC_VERSION')) return;


function inc_sphinxql_to_array_dist($u, $debut=''){
   include_spip('inc/indexer');

   // hacks pour la pagination
   // attention ce preg_replace est vraiment tres fragileÉÉÉ ˆ revoir TODO.
   if ($debut = intval($debut)) {
       $u = preg_replace('/ FACET /', " LIMIT $debut,20 FACET ", $u, 1);
   }

	$sphinx = new Sphinx\SphinxQL(SPHINX_SERVER_HOST, SPHINX_SERVER_PORT);

	$all = $sphinx->allfetsel($u);

	// pagination : rajouter $debut elements videsÉ
	if ($debut) $all['docs'] = array_pad($all['docs'], -$debut - count($all['docs']), 0);

	$total = $all['meta']['total'];
	if ($total > count($all['docs']))
		$all['docs'] = array_pad($all['docs'], $total - count($all['docs']), 0);

	return $all;
}

