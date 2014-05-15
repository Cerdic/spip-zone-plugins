<?php

namespace Indexer\Sources;


interface SourceInterface {

    public function getDocuments($start = 0, $end = 0, $column = '');

    public function getAllDocuments();

    /**
     * Indique le nombre de découpages pour indexer, en prenant $count éléments à chaque fois
     * @param int $count */
    public function getParts($count);

    public function __toString();
}
