<?php

namespace Sphinx\SphinxQL;

/**
 * Classe pour créer des requêtes de sélection Sphinx
 */
class Query{
	private $select  = array();
	private $from    = array();
	private $match   = null;
	private $where   = array();
	private $groupby = array();
	private $orderby = array();
	private $limit   = '';
	private $option  = array();
	private $facet   = array();

	public function __construct() {}

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

	public function match($match) {
		$this->match = $match;
		return $this;
	}

	public function getMatch() {
		return $this->match;
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
	
	public function option($option) {
		$this->option[] = $option;
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


	public function get() {
		$query = array();
		$this->removeEmpty();
		if ($this->select)   $query[] = 'SELECT '   . implode(', ', array_unique($this->select));
		if ($this->from)     $query[] = 'FROM '     . implode(', ', $this->from);

		// WHERE et MATCH
		$where = $this->where;
		if ($this->match) $where[] = 'MATCH('. $this->quote($this->match).')';
		if ($where)    $query[] = 'WHERE ('   . implode(') AND (', $where) . ')';

		if ($this->groupby)  $query[] = 'GROUP BY ' . implode(', ', $this->groupby);
		if ($this->orderby)  $query[] = 'ORDER BY ' . implode(', ', $this->orderby);
		if ($this->limit)    $query[] = 'LIMIT '    . $this->limit;
		if ($this->option)   $query[] = 'OPTION '   . implode(', ', array_unique($this->option));
		if ($this->facet)    $query[] = 'FACET '    . implode(' FACET ', $this->facet);
		return implode(' ', $query);
	}


	private function removeEmpty() {
		foreach (array('select', 'from', 'where', 'groupby', 'orderby', 'facet') as $key) {
			$this->$key = array_filter($this->$key);
			#$this->$key = array_filter($this->key, 'strlen'); // leaves 0
		}
	}

	public function __tostring() {
		return $this->get();
	}
}


