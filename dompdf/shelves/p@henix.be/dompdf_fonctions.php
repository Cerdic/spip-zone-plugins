<?php
/**
 * Fonctions utiles au plugin DOMPDF
 *
 * @plugin     DOMPDF
 * @copyright  2014
 * @author     vertige
 * @licence    GNU/GPL
 * @package    SPIP\Dompdf\Fonctions
 */

if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Simplifier la création de cadre avec l'icône PDF dans l'espace priver
 *
 * @param mixed $url_action
 * @param mixed $titre
 * @param mixed $titre_export
 * @access public
 * @return mixed
 */
function dompdf_cadre($url_action, $titre = null, $titre_export = null) {

   include_spip('inc/presentation');

   return
        debut_cadre_relief('',true,'', $titre).
        icone_horizontale(
            $titre_export,
            $url_action,
            'pdf-24.png',
            "export",
            false).
        fin_cadre_relief(true);
}