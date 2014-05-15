<?php

namespace Indexer\Sources;


interface SourceInterface {
    public function getDocuments();


    public function getAllDocuments($start = 0, $end = 0);

    /**
     * Indique le nombre de découpages pour indexer, en prenant $count éléments à chaque fois
     * @param int $count */
    public function getParts($count);

    public function __toString();
}
