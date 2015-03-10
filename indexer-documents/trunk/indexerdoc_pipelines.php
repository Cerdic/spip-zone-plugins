<?php

// Charge les classes possibles de l'indexer
require_once _DIR_PLUGIN_INDEXER . 'lib/Composer/Autoload/ClassLoader.php';

$loader = new \Composer\Autoload\ClassLoader();

// register classes with namespaces
$loader->add('Indexer', _DIR_PLUGIN_INDEXER . 'lib');
$loader->add('Sphinx',  _DIR_PLUGIN_INDEXER . 'lib');
$loader->addPsr4('Spip\\Indexer\\Sources\\',  _DIR_PLUGIN_INDEXERDOC . 'Sources');

$loader->register();


/**
 * Ajouter la source de données documents
 *
 *
 * @param $sources les sources déjà déclarées pour indexer
 * @return Sources Retourne le flux du pipeline complété
 */
function indexerdoc_indexer_sources($sources) {

    if (is_null($sources)){
		// On crée la liste des sources
		$sources = new Indexer\Sources\Sources();
    }
    // Par défaut on enregistre les articles du SPIP
    $sources->register('documents', new Spip\Indexer\Sources\Documents());

    return $sources;
}