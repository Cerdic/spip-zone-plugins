<?php

/**
 * Options globales chargées à chaque hit
 *
 * @package SPIP\Fabrique\Options
**/

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Version de la structure des données de sauvegarde de la fabrique
 * @var int */
define('FABRIQUE_VERSION', 5);
define('FABRIQUE_ID', 'mom_plugin');
define('FABRIQUE_ID_IMAGES', 'mom_plugin_images');

define('FABRIQUE_SKEL_SOURCE', 'fabrique/');
define('FABRIQUE_VAR_SOURCE', 'fabrique/');
define('FABRIQUE_DESTINATION_PLUGINS', 'fabrique_auto/'); // plugins/fabrique_auto 
define('FABRIQUE_DESTINATION_CACHE', 'fabrique/'); // ou tmp/cache/fabrique_auto

// constantes pouvant etre modifiees.

if (!defined('_FABRIQUE_ESPACER')) {
/**
 * Espacement des tabulations dans les array tabulaires
 * (en nb de caracteres).
 * @var int */
	define('_FABRIQUE_ESPACER', 20);
}

?>
