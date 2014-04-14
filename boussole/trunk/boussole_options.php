<?php
/**
 * Ce fichier contient les constantes générales du plugin.
 *
 * @package SPIP\BOUSSOLE\Options
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Dossiers et fichiers standardisés du plugin
 */
if (!defined('_BOUSSOLE_LOG'))
	/**
	 * Nom du fichier de log des actions du plugin */
	define('_BOUSSOLE_LOG', 'boussole');
if (!defined('_BOUSSOLE_NOMDIR_CACHE'))
	/**
	 * Nom du dossier contenant les fichiers caches des boussoles */
	define('_BOUSSOLE_NOMDIR_CACHE', 'cache-boussoles/');
if (!defined('_BOUSSOLE_DIR_CACHE'))
	/**
	 * Chemin du dossier contenant les fichiers caches des boussoles */
	define('_BOUSSOLE_DIR_CACHE', _DIR_VAR . _BOUSSOLE_NOMDIR_CACHE);
if (!defined('_BOUSSOLE_CACHE_LISTE'))
	/**
	 * Fichier cache de la liste des boussoles */
	define('_BOUSSOLE_CACHE_LISTE', 'boussoles.xml');
if (!defined('_BOUSSOLE_PREFIXE_CACHE'))
	/**
	 * Fichier cache d'une boussole */
	define('_BOUSSOLE_PREFIXE_CACHE', 'boussole-');


/**
 * Nom des balises de la DTD des boussoles
 */
if (!defined('_BOUSSOLE_NOMTAG_LISTE_BOUSSOLES'))
	/**
	 * Nom de la balise XML définissant une liste de boussoles */
	define('_BOUSSOLE_NOMTAG_LISTE_BOUSSOLES', 'boussoles');
if (!defined('_BOUSSOLE_NOMTAG_BOUSSOLE'))
	/**
	 * Nom de la balise XML définissant une boussole */
	define('_BOUSSOLE_NOMTAG_BOUSSOLE', 'boussole');
if (!defined('_BOUSSOLE_NOMTAG_GROUPE'))
	/**
	 * Nom de la balise XML définissant un groupe */
	define('_BOUSSOLE_NOMTAG_GROUPE', 'groupe');
if (!defined('_BOUSSOLE_NOMTAG_SITE'))
	/**
	 * Nom de la balise XML définissant un site */
	define('_BOUSSOLE_NOMTAG_SITE', 'site');
if (!defined('_BOUSSOLE_NOMTAG_ERREUR'))
	/**
	 * Nom de la balise XML définissant une erreur */
	define('_BOUSSOLE_NOMTAG_ERREUR', 'erreur');


/**
 * Types d'objet manipulés par le plugin
 */
if (!defined('_BOUSSOLE_OBJET_BOUSSOLE'))
	define('_BOUSSOLE_OBJET_BOUSSOLE', 'boussole');
if (!defined('_BOUSSOLE_OBJET_GROUPE'))
	define('_BOUSSOLE_OBJET_GROUPE', 'groupe');
if (!defined('_BOUSSOLE_OBJET_SITE'))
	define('_BOUSSOLE_OBJET_SITE', 'site');

?>
