<?php

namespace Sphinx;

/**
 * Classe pour créer des requêtes de sélection Sphinx
 *
 * Quelques exemples :


SPIP

[(#SET{sql, [(#ENV{source,spip}|sphinx_get_query_documents{#ENV*{recherche},#ENV*{tag},#ENV*{auteur}})]})]

PHP

function sphinx_get_query_documents($source, $recherche, $tag = '', $auteur = '', $orderby = '') {
    include_spip('inc/indexer');
    $sq = new \Sphinx\SphinxQLQuery();
    $sq
        ->select('WEIGHT() AS score')
        ->select('*')
        ->select("SNIPPET(content, " . $sq->quote($recherche . ($tag ? " $tag" : '')) . ", 'limit=200') AS snippet")
        ->from($source)
        ->where("MATCH(" . $sq->quote($recherche) . ")");

    if ($orderby) {
        $sq->orderby($orderby);
    }

    if ($tag) {
        if ($tag == '-') {
            $sq->select("IN(properties.tags.fr, " . $sq->quote($tag) . ") AS tag");
        } else {
            $sq->select("(LENGTH(properties.tags.fr) = 0) AS tag");
        }
        $sq->where("tag = 1");
    }

    if ($auteur) {
        $sq->select("IN(properties.authors, " . $sq->quote($auteur) . ") AS auteur");
        $sq->where("auteur = 1");
    }

    return $sq->get();
}

SPIP

[(#SET{sqlf, [(#ENV{source,spip}|sphinx_get_query_facette_auteurs{#ENV*{recherche},#ENV*{tag},#ENV*{auteur}})]})]

PHP

function sphinx_get_query_facette_auteurs($source, $recherche, $tag = '', $auteur = '', $orderby = '') {

    include_spip('inc/indexer');
    $sq = new \Sphinx\SphinxQLQuery();
    $sq
        ->select('COUNT(*) AS c')
        ->select('GROUPBY() AS facette')
        ->from($source)
        ->where("MATCH(" . $sq->quote($recherche) . ")")
        ->groupby("properties.authors")
        ->orderby("c DESC")
        ->limit("30")
        ;

    return $sq->get();
}



 */
class SphinxQLQuery{
    private $select  = [];
    private $from    = [];
    private $where   = [];
    private $groupby = [];
    private $orderby = [];
    private $limit   = '';
    private $facet   = [];
	
	public function __construct($query_description=array()) {
		if (!empty($query_description)){
			$this->array2query($query_description);
		}
	}
	
    public function select($select) {
        $this->select[] = $select;
        return $this;
    }

    public function from($from) {
        $this->from[] = $from;
        return $this;
    }


    public function where($where) {
        $this->where[] = $where;
        return $this;
    }

    public function orderby($orderby) {
        $this->orderby[] = $orderby;
        return $this;
    }

    public function groupby($groupby) {
        $this->groupby[] = $groupby;
        return $this;
    }

    public function limit($limit) {
        $this->limit = $limit;
        return $this;
    }

    public function facet($facet) {
        $this->facet[] = $facet;
        return $this;
    }

    function quote($value, $type='') {
		return
			(is_numeric($value)) ? strval($value) :
				(!is_array($value) ? ("'" . addslashes($value) . "'") :
					join(",", array_map(array($this, 'quote'), $value))
				);
	}
	
	public function array2query($query_description){
		if (is_array($query_description)){
			// Index (mandatory)
			if (isset($query_description['index'])){
				if (!is_array($query_description['index'])){
					$query_description['index'] = array($query_description['index']);
				}
				foreach ($query_description['index'] as $index){
					$this->from($index);
				}
			}
			
			// Fulltext search string (optional)
			if (isset($query_description['fulltext']) and is_string($query_description['fulltext'])){
				$this->where("match('".$query_description['fulltext']."')");
			}
			
			// All filters
			$as_count = 0;
			if (isset($query_description['filters']) and is_array($query_description['filters'])){
				foreach ($query_description['filters'] as $filter){
					// Mono value
					if (
						$filter['type'] == 'mono'
						and isset($filter['field']) and is_string($filter['field']) // mandatory
						and isset($filter['values']) // mandatory
					){
						// Default comparision : =
						if (!isset($filter['comparison'])){
							$filter['comparison'] = '=';
						}
						
						// Always work with an array of values
						if (!is_array($filter['values'])){
							$filter['values'] = array($filter['values']);
						}
						
						// For each values, we build a comparision
						$comparisons = array();
						foreach ($filter['values'] as $value){
							$comparisons[] = $filter['not'] ? '!':'' . '(' . $filter['field'] . $filter['comparison'] . $this->quote($value) . ')';
						}
						if ($comparisons){
							$comparisons = join(' OR ', $comparisons);
							$this->where($comparisons);
						}
					}
					
					// Multi value JSON
					if (
						$filter['type'] == 'multi_json'
						and isset($filter['field']) and is_string($filter['field']) // mandatory
						and isset($filter['values']) // mandatory
					){
						// Always work with an array of values
						if (!is_array($filter['values'])){
							$filter['values'] = array($filter['values']);
						}
						
						// For each values, we build an "in" select
						$where_ins = array();
						foreach ($filter['values'] as $value){
							$this->select(
								'IN(' . $filter['field'] . $this->quote($value) . ') as multi_json'.$as_count
							);
							$where_ins[] = 'multi_json'.$as_count . '=' . ($filter['not'] ? '0' : '1');
						}
						if ($where_ins){
							$where_ins = join(' OR ', $where_ins);
							$this->where($where_ins);
						}
					}
				}
			}
		}
		
		/**
		// exemple de description
		array(
			'index' => 'visites',
			'fulltext' => 'ma recherche',
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
		**/
	}
	
    public function get() {
        $query = [];
        if ($this->select)   $query[] = 'SELECT '   . implode(',', $this->select);
        if ($this->from)     $query[] = 'FROM '     . implode(',', $this->from);
        if ($this->where)    $query[] = 'WHERE ('   . implode(') AND (', $this->where) . ')';
        if ($this->groupby)  $query[] = 'GROUP BY ' . implode(',', $this->groupby);
        if ($this->orderby)  $query[] = 'ORDER BY ' . implode(',', $this->orderby);
        if ($this->limit)    $query[] = 'LIMIT '    . $this->limit;
        if ($this->facet)    $query[] = 'FACET '    . implode(' FACET ', $this->facet);
        return implode(' ', $query);
    }

    public function __tostring() {
        return $this->get();
    }
}


