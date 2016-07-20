<?php
/**
 * Options au chargement du plugin cvtupload
 *
 * @plugin     cvtupload
 * @copyright  2015
 * @package    SPIP\cvtupload\Options
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Age maximum des fichiers dans le dossier temporaire
 **/
if (!defined('_CVTUPLOAD_AGE_MAX')) {
	define('_CVTUPLOAD_AGE_MAX', 6*3600);
}

/**
 * Nombre maximum de fichiers dans le dossier temporaire
 **/
if (!defined('_CVTUPLOAD_MAX_FILES')) {
	define('_CVTUPLOAD_MAX_FILES', 200);
}
