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
