<?php
/**
 * Options du plugin URLs Pages Personnalisées
 *
 * @plugin     URLs Pages Personnalisées
 * @copyright  2016
 * @author     tcharlss
 * @licence    GNU/GPL
 * @package    SPIP\Urls_pages_personnalisees\Options
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

// Éviter une erreur `undefined function url_page_personnalisee()` lors de la désactivation du plugin
$GLOBALS['marqueur_skel'] = (isset($GLOBALS['marqueur_skel']) ? $GLOBALS['marqueur_skel'] : '').':urls_pages';
