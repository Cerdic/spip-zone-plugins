<?php

namespace Indexer\Sources;


class Sources implements \IteratorAggregate {

    /** @var Indexer\Sources\SourceInterface[] */
    private $sources = [];

    public function __construct() {

    }

    public function register(SourceInterface $source) {
        $this->sources[] = $source;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->sources);
    }
}
