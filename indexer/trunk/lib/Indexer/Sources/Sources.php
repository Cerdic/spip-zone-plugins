<?php

namespace Indexer\Sources;


class Sources implements \IteratorAggregate {
	/** @var Indexer\Sources\SourceInterface[] */
	private $sources = array();
	
	public function __construct() {
		
	}
	
	public function register($cle, SourceInterface $source) {
		$this->sources[$cle] = $source;
	}
	
	public function unregister($cle) {
		unset($this->sources[$cle]);
	}
	
	public function getSource($cle) {
		if (isset($this->sources[$cle])){
			return $this->sources[$cle];
		}
	
		return null;
	}
	
	public function getIterator() {
		return new \ArrayIterator($this->sources);
	}
}
