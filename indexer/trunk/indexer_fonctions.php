<?php



/**
 *
 */
function sphinx_get_query_documents($index, $recherche, $tag = '', $auteur = '', $orderby = '') {
    include_spip('inc/indexer');
    $sq = new \Sphinx\SphinxQLQuery();
    $sq
        ->select('WEIGHT() AS score')
        ->select('*')
        ->select("SNIPPET(content, " . $sq->quote($recherche . ($tag ? " $tag" : '')) . ", 'limit=200') AS snippet")
        ->from($index)
        ->where("MATCH(" . $sq->quote($recherche) . ")")
        ->facet("properties.authors")
        ;

    if ($orderby) {
        $sq->orderby($orderby);
    }

    if ($tag) {
        if ($tag == '-') {
            $sq->select("IN(properties.tags, " . $sq->quote($tag) . ") AS tag");
        } else {
            $sq->select("(LENGTH(properties.tags) = 0) AS tag");
        }
        $sq->where("tag = 1");
    }

    if ($auteur) {
        $sq->select("IN(properties.authors, " . $sq->quote($auteur) . ") AS auteur");
        $sq->where("auteur = 1");
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
