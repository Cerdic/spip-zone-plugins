<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

// Charge les classes possibles de l'indexer
require_once _DIR_PLUGIN_INDEXER . 'lib/Composer/Autoload/ClassLoader.php';

$loader = new \Composer\Autoload\ClassLoader();

// register classes with namespaces
$loader->add('Indexer', _DIR_PLUGIN_INDEXER . 'lib');
$loader->add('Sphinx',  _DIR_PLUGIN_INDEXER . 'lib');
$loader->addPsr4('Spip\\Indexer\\Sources\\',  _DIR_PLUGIN_INDEXER . 'Sources');


$loader->register();
