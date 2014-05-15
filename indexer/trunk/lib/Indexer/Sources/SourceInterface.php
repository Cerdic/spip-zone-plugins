<?php

namespace Indexer\Sources;


interface SourceInterface {
    public function getDocuments();
    public function getAllDocuments();

    public function __toString();
}
