<?php

namespace Indexer\Sources;

class Document {
	public $id = 0;
	public $title = '';
	public $summary = '';
	public $content = '';
	public $date = 0;
	public $date_indexation = 0;
	public $uri = '';
	public $properties = array();

	public function __construct($data) {
		$this->date_indexation = time();
		$this->setAll($data);
	}

	public function setAll($data) {
		foreach ($data as $key => $val) {
			if (property_exists($this, $key)) {
				$this->$key = $val;
			} else {
				throw new \Exception("Property $key does not exist");
			}
		}

		if (!isset($data['id'])) {
			throw new \Exception("Property 'id' is required");
		}
	}
}
