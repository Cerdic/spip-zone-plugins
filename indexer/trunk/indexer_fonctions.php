<?php



function sphinx_get_array2query($api, $limit=''){
	include_spip('inc/indexer');
	$sq = new \Sphinx\SphinxQL\QueryApi($api);
	if ($limit){ $sq->limit($limit); }

	return $sq->get();
}


function sphinx_test_api() {
    $api = 	// exemple de description
	array(
		'index' => 'visites',
		'select' => array('date', 'properties', '*', 'etc'),
		'fulltext' => 'ma recherche',
		'snippet' => array(
			'words' => 'un mot',
			'field' => 'content',
			'limit' => 200,
		),
		'filters' => array(
			array(
				'type' => 'mono',
				'field' => 'properties.lang',
				'values' => array('fr'),
				'comparison' => '!=', // default : =
			),
			array(
				'type' => 'multi_json',
				'field' => 'properties.tags',
				'values' => array('pouet', 'glop'),
			),
			array(
				'type' => 'distance',
				'center' => array(
					'lat' => 44.837862,
					'lon' => -0.580086,
				),
				'fields' => array(
					'lat' => 'properties.geo.lat',
					'lon' => 'properties.geo.lon',
				),
				'distance' => 10000,
				'comparison' => '>', // default : <=
			),
			array(
				'type' => 'interval',
				'expression' => 'uint(properties.truc)',
				'intervals' => array(1,2,3,4,5),
				'field' => 'truc',
				'test' => 'truc = 2',
				'select' => 'interval(uint(properties.truc),1,2,3,4)',
				'where' => 'test = 2',
			),
		),
		'orders' => array(
			array(
				'field' => 'score',
				'direction' => 'asc', // default : desc
			),
			array(
				'field' => 'distance',
				'center' => array(
					'lat' => 44.837862,
					'lon' => -0.580086,
				),
				'fields' => array(
					'lat' => 'properties.geo.lat',
					'lon' => 'properties.geo.lon',
				),
			),
		),
		'facet' => array(
			'field' => 'properties.tags',
			'group_name' => 'tag',
			'order' => 'tag asc', // default : count desc
		),
	);
    echo "\n<pre>"; print_r(sphinx_get_array2query($api)); echo "</pre>";
}


/**
 *
 */
function sphinx_get_query_documents($index, $recherche, $tag = '', $auteur = '', $annee='', $orderby = '') {
    include_spip('inc/indexer');

    if (!$index) $index = SPHINX_DEFAULT_INDEX;

    $sq = new \Sphinx\SphinxQL\Query();
    $sq
        ->select('*')
        ->select("SNIPPET(content, " . $sq->quote($recherche . ($tag ? " $tag" : '')) . ", 'limit=200') AS snippet")
        ->from($index)
        ->facet("properties.authors ORDER BY COUNT(*) DESC")
        ->facet("properties.tags ORDER BY COUNT(*) DESC")
        ->facet("YEAR(date) as annee ORDER BY date DESC")
        ;

    if (strlen($recherche)) {
        $sq->select('WEIGHT() AS score');
        $sq->where("MATCH(" . $sq->quote($recherche) . ")");
    }

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
    $sq = new \Sphinx\SphinxQL\Query();
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
    $sq = new \Sphinx\SphinxQL\Query();
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
