<?php

namespace Indexer\Sources;


class Sources implements \IteratorAggregate {

    /** @var Indexer\Sources\SourceInterface[] */
    private $sources = [];

    public function __construct() {

    }

    public function register($cle, SourceInterface $source) {
        $this->sources[$cle] = $source;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->sources);
    }
}
