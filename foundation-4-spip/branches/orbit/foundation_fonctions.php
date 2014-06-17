<?php
/**
 * Fonction d'upgrade/installation du plugin foundation-4-spip
 *
 * @plugin     foundation-4-spip
 * @copyright  2013
 * @author     Phenix
 * @licence    GNU/GPL
 */
if (!defined('_ECRIRE_INC_VERSION')) return;


/*
*   Rendre les iframes responsive via un filtre.
*/
function iframe_responsive($texte) {
    return preg_replace('/<iframe(.+)><\/iframe>/', '<div class="iframe_container"><iframe $1></iframe></div>', $texte);
}