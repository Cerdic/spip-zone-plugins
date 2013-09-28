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
*   Rendre les iframes responsive via un filtre et la classe flex-video de Foundation.
*/
function iframe_responsive($texte) {

    // Function de callback
    function responsive($matches) {
        // Dans le cas de vimeo, il faut ajouter une classe
        if (strpos($matches[0], 'vimeo')) $vimeo = ' vimeo';
        else $vimeo = '';

        // On revoie la bonne structure html d'iframe.
        return '<div class="flex-video'.$vimeo.'"><iframe '.$matches[0].'></iframe></div>';
    }
    
    // On détecte tout les iFrames et on les rends responsives.
    return preg_replace_callback('/<iframe(.+)><\/iframe>/', 'responsive', $texte);
}