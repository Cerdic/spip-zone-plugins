<?php
/**
 * Options au chargement du plugin SPIPr-Dane-Noisettes
 *
 * @plugin     SPIPr-Dane-Noisettes
 * @copyright  2019
 * @author     Dominique Lepaisant
 * @licence    GNU/GPL
 * @package    SPIP\Sdn\Options
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

#$GLOBALS['z_blocs'] = array('content','aside','extra');

/* n'afficher que les pages ayant un page.xml dans le noizetier */
define('_NOIZETIER_LISTER_PAGES_SANS_XML',false);



