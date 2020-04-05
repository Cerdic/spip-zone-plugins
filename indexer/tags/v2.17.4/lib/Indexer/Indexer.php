<?php

namespace Indexer;

use Indexer\Storage\StorageInterface;
use Indexer\Sources\Document;

class Indexer {
	/** @var StorageInterface|null */
	private $storage = null;

	function __construct() {}

	function registerStorage(StorageInterface $storageEngine) {
		$this->storage = $storageEngine;
	}

	function replaceDocument(Document $document) {
		return $this->storage->replaceDocument($document);
	}

	function replaceDocuments($documents) {
		return $this->storage->replaceDocuments($documents);
	}

	function purgeDocuments($source = null) {
		return $this->storage->purgeDocuments($source);
	}
}






