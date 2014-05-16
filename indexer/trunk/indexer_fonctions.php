<?php

function sphinx_get_array2query($query_description, $limit=''){
	include_spip('inc/indexer');
	$sq = new \Sphinx\SphinxQLQuery($query_description);
	if ($limit){ $sq->limit($limit); }
	
	return $sq->get();
}

/**
 *
 */
function sphinx_get_query_documents($index, $recherche, $tag = '', $auteur = '', $annee='', $orderby = '') {
    include_spip('inc/indexer');
    $sq = new \Sphinx\SphinxQLQuery();
    $sq
        ->select('WEIGHT() AS score')
        ->select('*')
        ->select("SNIPPET(content, " . $sq->quote($recherche . ($tag ? " $tag" : '')) . ", 'limit=200') AS snippet")
        ->from($index)
        ->where("MATCH(" . $sq->quote($recherche) . ")")
        ->facet("properties.authors ORDER BY COUNT(*) DESC")
        ->facet("properties.tags ORDER BY COUNT(*) DESC")
        ->facet("YEAR(date) ORDER BY date DESC")
        ;

    if ($orderby) {
        $sq->orderby($orderby);
    }

    if ($tag) {
        if ($tag == '-') {
            $sq->select("(LENGTH(properties.tags) = 0) AS tag");
        } else {
            $sq->select("IN(properties.tags, " . $sq->quote($tag) . ") AS tag");
        }
        $sq->where("tag = 1");
    }

    if ($auteur) {
        $sq->select("IN(properties.authors, " . $sq->quote($auteur) . ") AS auteur");
        $sq->where("auteur = 1");
    }

    if ($annee) {
        $sq->select("(YEAR(date) = " . $sq->quote($annee) . ") AS annee");
        $sq->where("annee = 1");
    }

    return $sq->get();
}

/**
 *
 */
function sphinx_get_query_facette_auteurs($index, $recherche, $tag = '', $auteur = '', $orderby = '') {

    include_spip('inc/indexer');
    $sq = new \Sphinx\SphinxQLQuery();
    $sq
        ->select('COUNT(*) AS c')
        ->select('GROUPBY() AS facette')
        ->from($index)
        ->where("MATCH(" . $sq->quote($recherche) . ")")
        ->groupby("properties.authors")
        ->orderby("c DESC")
        ->limit("30")
        ;

    return $sq->get();
}


function sphinx_get_query_facette($index, $facette, $cle, $recherche, $orderby = '', $limit = 0) {

    if (!$orderby) $orderby = 'c DESC';
    if (!$limit)   $limit   = 30;

    include_spip('inc/indexer');
    $sq = new \Sphinx\SphinxQLQuery();
    $sq
        ->select('COUNT(*) AS c')
        ->select('GROUPBY() AS facette')
        ->from($index)
        ->where("MATCH(" . $sq->quote($recherche) . ")")
        ->orderby($orderby)
        ->limit($limit)
        ;
    // facette simple 'properties.tags'
    if (strpos($facette, '(') === false) {
        $sq->groupby($facette);
    }
    // facette avec calcul 'YEAR(properties.dates.publication)'
    else {
        $sq->select("$facette AS data");
        $sq->groupby("data");
    }

    return $sq->get();
}
