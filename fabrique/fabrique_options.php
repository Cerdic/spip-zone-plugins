<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

define('FABRIQUE_VERSION', 5);
define('FABRIQUE_ID', 'mom_plugin');
define('FABRIQUE_ID_IMAGES', 'mom_plugin_images');

define('FABRIQUE_SKEL_SOURCE', 'fabrique/');
define('FABRIQUE_VAR_SOURCE', 'fabrique/');
define('FABRIQUE_DESTINATION_PLUGINS', 'fabrique_auto/'); // plugins/fabrique_auto 
define('FABRIQUE_DESTINATION_CACHE', 'fabrique/'); // ou tmp/cache/fabrique_auto

// constantes pouvant etre modifiees.

// espacement des tabulations dans les array tabulaires
// (en nb de caracteres).
if (!defined('_FABRIQUE_ESPACER')) {
	define('_FABRIQUE_ESPACER', 20);
}

?>
