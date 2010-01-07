<?php

if (!defined('_DIR_LIB')) define('_DIR_LIB', _DIR_RACINE . 'lib/');

// pour tests, le chemin vers les plugins est _DIR_PLUGIN2
// il faudra passer ensuite toutes les occurrences de _DIR_PLUGINS2
// a _DIR_PLUGINS tout court
define('_DIR_PLUGINS2', _DIR_PLUGINS . 'auto/');
define('_DIR_PLUGINS_AUTO', _DIR_PLUGINS . 'auto/');
// ajoute dans 2.0.11 dans spip.
define('_DIR_EXTENSIONS', _DIR_RACINE . "extensions/");

// autoriser ou non le telechargement de paquets
// un define plutot qu'un autoriser pour pouvoir le definir dans la conf d'une mutualisation
define('_AUTORISER_TELECHARGER_PLUGINS',true);

?>
