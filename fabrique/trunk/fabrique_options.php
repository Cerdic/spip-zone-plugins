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

/**
 * Clé de stockage des infos du plugin en construction dans la session
 * @var string */
define('FABRIQUE_ID', 'mom_plugin');

/**
 * Clé de stockage des infos d'image du plugin en construction dans la session
 * @var string */
define('FABRIQUE_ID_IMAGES', 'mom_plugin_images');

/**
 * Répertoire des sources des squelettes fabriquant le plugin à générer
 * @var string */
define('FABRIQUE_SKEL_SOURCE', 'fabrique/');

/**
 * Répertoire des fichiers temporaires d'images servant pendant la fabrication
 * du plugin à générer
 * @var string */
define('FABRIQUE_VAR_SOURCE', 'fabrique/');

/**
 * Répertoire recevant le plugin généré s'il est accessible dans le répertoire plugins
 * @var string */
define('FABRIQUE_DESTINATION_PLUGINS', 'fabrique_auto/'); // plugins/fabrique_auto

/**
 * Répertoire recevant le plugin généré dans tmp/cache s'il n'était pas accessible
 * dans le répertoire plugins
 * @var string */
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
