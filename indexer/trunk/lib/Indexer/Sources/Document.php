<?php

namespace Indexer\Sources;


class Document {

    public $id = null;
    public $title = null;
    public $summary = null;
    public $content = null;
    public $date = null;
    public $uri = null;
    public $properties = null;

    public function __construct($data) {
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
