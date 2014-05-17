<?php

namespace Sphinx;

/**
 * Classe pour créer des requêtes de sélection Sphinx
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

	public function generate_snippet($field, $words='', $limit=200){
		if ($words){
			$limit = intval($limit);
			$this->select('snippet(' . $field . ', ' . $this->quote($words) . ", 'limit=$limit') as snippet");
		}
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

			// Explicit select definition
			if (isset($query_description['select'])){
				// Always work with an array of values
				if (!is_array($query_description['select'])){
					$query_description['select'] = array($query_description['select']);
				}
				foreach ($query_description['select'] as $select){
					$this->select($select);
				}
			}
			
			// Fulltext search string (optional)
			if (isset($query_description['fulltext']) and is_string($query_description['fulltext'])){
				$this->where('match(' . $this->quote($query_description['fulltext']) . ')');
				// add the score
				$this->select('weight() as score');
				// add to snippet
				$snippet_words = $query_description['fulltext'];
			}

			// If there is fulltext and/or an other words declaration, generate a snippet
			if (isset($query_description['snippet']['words']) and is_string($query_description['snippet']['words'])){
				$snippet_words .= ' ' . $query_description['snippet']['words'];
				$snippet_words = trim($snippet_words);
			}
			if ($snippet_words){
				$field = isset($query_description['snippet']['field']) ? $query_description['snippet']['field'] : 'content';
				$limit = isset($query_description['snippet']['limit']) ? $query_description['snippet']['limit'] : 200;
				$this->generate_snippet($field, $snippet_words, $limit);
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
						// Default comparison : =
						if (!isset($filter['comparison'])){
							$filter['comparison'] = '=';
						}

						// Always work with an array of values
						if (!is_array($filter['values'])){
							$filter['values'] = array($filter['values']);
						}

						// For each values, we build a comparison
						$comparisons = array();
						foreach ($filter['values'] as $value){
							$comparison = $filter['field'] . $filter['comparison'] . $this->quote($value);
							if ($filter['not']){
								$comparison = "!($comparison)";
							}
							$comparisons[] = $comparison;
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
							$filter['values'] = array(array($filter['values']));
						}

						// At depth 1, generate AND
						$ins = array();
						foreach ($filter['values'] as $values_in){
							// Always work with an array of values
							if (!is_array($values_in)){
								$values_in = array($values_in);
							}
							$ins[] = 'IN(' . $filter['field'] . ', ' . join(', ', array_map(array($this, 'quote'), array_filter($values_in))) . ')';
						}
						if ($ins){
							$this->select('(' . join(' AND ', $ins) . ') as select_'.$as_count);
							$this->where('select_'.$as_count . '=' . ($filter['not'] ? '0' : '1'));
							$as_count++;
						}
					}
				}
			}
		}

		/**
		// exemple de description
		array(
			'index' => 'visites',
			'select' => array('date', 'properties', '*', 'etc'),
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
        if ($this->select)   $query[] = 'SELECT '   . implode(', ', $this->select);
        if ($this->from)     $query[] = 'FROM '     . implode(', ', $this->from);
        if ($this->where)    $query[] = 'WHERE ('   . implode(') AND (', $this->where) . ')';
        if ($this->groupby)  $query[] = 'GROUP BY ' . implode(',', $this->groupby);
        if ($this->orderby)  $query[] = 'ORDER BY ' . implode(', ', $this->orderby);
        if ($this->limit)    $query[] = 'LIMIT '    . $this->limit;
        if ($this->facet)    $query[] = 'FACET '    . implode(' FACET ', $this->facet);
        return implode(' ', $query);
    }

    private function removeEmpty() {
        foreach (['select', 'from', 'where', 'groupby', 'orderby', 'facet'] as $key) {
            $this->$key = array_filter($this->$key);
            #$this->$key = array_filter($this->key, 'strlen'); // leaves 0
        }
    }

    public function __tostring() {
        return $this->get();
    }
}


