<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

// Constantes pour connexion à Sphinx
defined('SPHINX_SERVER_HOST') || define('SPHINX_SERVER_HOST', '127.0.0.1');
defined('SPHINX_SERVER_PORT') || define('SPHINX_SERVER_PORT', 9306);


// Charge les classes possibles de l'indexer
require_once _DIR_PLUGIN_INDEXER . 'lib/Composer/Autoload/ClassLoader.php';

$loader = new \Composer\Autoload\ClassLoader();

// register classes with namespaces
$loader->add('Indexer', _DIR_PLUGIN_INDEXER . 'lib');
$loader->add('Sphinx',  _DIR_PLUGIN_INDEXER . 'lib');
$loader->addPsr4('Spip\\Indexer\\Sources\\',  _DIR_PLUGIN_INDEXER . 'Sources');


$loader->register();
