<?php
/**
 * Options au chargement du plugin Spipr-Dane Config
 *
 * @plugin     Spipr-Dane Config
 * @copyright  2019
 * @author     Webmestre DANE
 * @licence    GNU/GPL
 * @package    SPIP\Sdc\Options
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/* est-ce qu'on est dans une mutualisation ? */
if (!defined('_DIR_SITE')) define('_DIR_SITE', _DIR_RACINE);

/* n'afficher que les pages ayant un page.xml dans le noizetier */
define('_NOIZETIER_LISTER_PAGES_SANS_XML',false);

