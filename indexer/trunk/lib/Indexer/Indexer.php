<?php

namespace Indexer;

use Indexer\Storage\StorageInterface;
use Indexer\Sources\Document;

class Indexer {

    /** @var Indexer\StorageEngineInterface|null */
    private $storage = null;


    function __construct() {

    }

    function registerStorage(StorageInterface $storageEngine) {
        $this->storage = $storageEngine;
    }

    function replaceDocument(Document $document) {
        return $this->storage->replaceDocument($document);
    }

    function replaceDocuments($documents) {
        return $this->storage->replaceDocuments($documents);
    }
}






