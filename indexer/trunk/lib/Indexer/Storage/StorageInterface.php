<?php


namespace Indexer\Storage;

use Indexer\Sources\Document;


interface StorageInterface {
    /**
     * @param Document $document
     */
    public function replaceDocument(Document $document);

    /**
     * @param Document[] $documents
     */
    public function replaceDocuments($documents);
}
