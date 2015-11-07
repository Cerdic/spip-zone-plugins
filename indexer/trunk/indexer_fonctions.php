<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Générer un identifiant unique suivant une hiérarchie de titres
 **/
function indexer_id_hierarchie($hierarchie=array(), $titre='') {
	// On ajoute le titre du contenu lui-même à la fin de la hiérarchie
	if (!is_array($hierarchie)) {
		$hierarchie = array();
	}
	$hierarchie[] = $titre;
	$id = md5(serialize($hierarchie));
	
	return $id;
}

/**
 * Lister les jointures de recherche possibles pour un objet
 * 
 * On liste les jointures déclarées et on ne garde que celles qui ont une fonction "indexer" dédiée.
 * 
 * @param string $objet
 * 		Type ou table de l'objet voulu
 * @return array
 * 		Retourne la liste des jointures possibles
 **/
function indexer_lister_jointures($objet) {
	include_spip('base/objets');
	
	$jointures = array();
	$jointures_declarees = array_keys(objet_info($objet, 'rechercher_jointures'));
	
	// On ne garde que celles qui ont une fonction "indexer" dédiée
	foreach ($jointures_declarees as $jointure) {
		$table = table_objet($jointure); // article => articles
		if (charger_fonction('jointure_'.$table, 'indexer', true)) {
			$jointures[] = $table;
		}
	}
	
	return $jointures;
}

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
        ->select("SNIPPET(content, " . $sq->quote($recherche . ($tag ? " $tag" : '')) . ", 'html_strip_mode=strip') AS snippet")
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
        // permettre un order by (formule compliquee AS tseg) dans la boucle DATA
        if (preg_match(',^(.*) AS (\w+),i', $orderby, $r)) {
            $sq->select($r[0]);
            $orderby = str_replace($r[0], $r[2], $orderby);
        }
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


/**
 * Compile la balise `#PROPERTIES`
 *
 * Utile dans une boucle SPHINX pour retourner une valeur.
 * 
 * @balise
 * @see table_valeur()
 * @example
 *     ```
 *     #PROPERTIES renvoie le champ properties au format array
 *     #PROPERTIES{x} renvoie #PROPERTIES|table_valeur{x},
 *     ```
 *
 * @param Champ $p
 *     Pile au niveau de la balise
 * @return Champ
 *     Pile complétée par le code à générer
**/
function balise_PROPERTIES_dist($p) {
	// cle du tableau desiree
	$_nom = interprete_argument_balise(1,$p);
	// valeur par defaut
	$_sinon = interprete_argument_balise(2,$p);

	$b = $p->nom_boucle ? $p->nom_boucle : $p->id_boucle;
	$p->code = index_pile($p->id_boucle, 'properties', $p->boucles, $b);
	
	// deserialiser le champ
	$_prop = 'json_decode('. $p->code . ',true)';
	
	if ($p->etoile === '') {
		$p->code = $_prop;
	}
	if ($_nom !== NULL){
		$p->code = 'table_valeur('. $_prop .', '.$_nom.')';
	}
	if ($_sinon !== NULL){
		$p->code = 'table_valeur('. $_prop .', '.$_nom.', '.$_sinon.')';
	}

	$p->interdire_scripts = true;
	return $p;
}

