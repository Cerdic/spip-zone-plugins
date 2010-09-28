<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 ff=unix fenc=utf8: */

/**
 * Génération d'article spip au format pdf
 *
 * @package      spipdf
 * @author       Yves Tannier [grafactory.net]
 * @copyright    2010 Yves Tannier
 * @link         http://www.grafactory.net/
 * @link         http://github.com/yvestan/spipdf
 * @license      GPL Gnu Public Licence
 * @version      0.1
 */

// pour les function unicode2charset
include_spip('inc/charsets') ;

// repris dans le plugin article_pdf => a modifier
// http://zone.spip.org/trac/spip-zone/browser/_plugins_/article_pdf
function spipdf_first_clean($texte) {

        //Translation des codes iso PB avec l'utilisation de <code>
        $trans = get_html_translation_table(HTML_ENTITIES);

        // supprimer les remarques HTML (du Couteau Suisse ?)
        $texte = preg_replace(',<!-- .* -->,msU', '', $texte);
        
        $trans = array_flip($trans);
        $trans["<br />\n"] = '<BR>'; // Pour éviter que le \n ne se tranforme en espace dans les <DIV class=spip_code> (TT, tag SPIP : code)
        $trans['&#176;'] = '°';
        $trans["&#339;"] = 'oe';
        $trans["&#8211;"] = '-';
        $trans["&#8216;"] = '\'';
        $trans["&#8217;"] = '\'';		
        $trans["&#8220;"] = '"';
        $trans["&#8221;"] = '"';
        $trans["&#8230;"] = '...';
        $trans["&#8364;"] = 'Euros';
        //$trans["&ucirc;"] = "û";
        $trans['->'] = '-»';
        $trans['<-'] = '«-';
        $trans['&nbsp;'] = ' ';
        $trans['&mdash;'] = '-';

        // certains titles font paniquer l'analyse
        $texte = preg_replace(',title=".*",msU', 'title=""', $texte);

        $texte = unicode2charset(charset2unicode($texte), 'ISO-8859-1'); // repasser tout dans un charset acceptable par export PDF
        //$texte = utf8_decode($texte); 
        $texte = strtr($texte, $trans);

        return $texte;
}

// generation de pdf
function spipdf_affichage_final($flux) {

    // on matche les pages qui contiennent la déclaration <page de HTML2PDF
    if(strpos($flux, '<page')!==false) {

        // supprimer barre admin Non fonctionnel
        if(defined('SPIPDF_MASQUE_FORMULAIRE_ADMIN')) {
            $patterns = array(
                '/<link(.*)spip_admin\.css(.*)\/>/iUsm',
                '/<link(.*)spip_admin_perso\.css(.*)\/>/iUsm',
                //'/<div(.*)spip-admin-bloc(.*)<\/div>/iUsm',
                '/<div(.*)spip-admin(.*)\/div>/iUsm',
            );
            $replaces = array(
                '',
                '',
                '',
            );
            $flux = preg_replace($patterns, $replaces, $flux);
        }

        // supprimer les spans autour des images et récupérer le placement
        $patterns_float = '/<span class=\'spip_document_.*spip_documents.*float:(.*);.*>(.*)<\/span>/iUms';
        function remplaceSpan($matches) { return str_replace('img', 'img style="padding:5px;" align="'.$matches[1].'"', $matches[2]); }
        $flux = preg_replace_callback($patterns_float, 'remplaceSpan', $flux);

        // supprimer les spans autour des images
        $patterns_float = '/<span class=\'spip_document_.*spip_documents.*>(.*)<\/span>/iUms';
        function remplaceSpanCenter($matches) { return $matches[1]; }
        $flux = preg_replace_callback($patterns_float, 'remplaceSpanCenter', $flux);

        // supprimer les dl autour des images et récupérer le placement
        $patterns_float = '/<dl class=\'spip_document_.*spip_documents.*float:(.*);.*<dt>(.*)<\/dt>.*<\/dl>/iUms';
        function remplaceDt($matches) { return str_replace('img', 'img style="padding:5px;" align="'.$matches[1].'"', $matches[2]); }
        $flux = preg_replace_callback($patterns_float, 'remplaceDt', $flux);

        // supprimer les dl autour des images
        $patterns_float = '/<dl class=\'spip_document_.*spip_documents.*<dt>(.*)<\/dt>.*<\/dl>/iUms';
        function remplaceDtCenter($matches) { return $matches[1]; }
        $flux = preg_replace_callback($patterns_float, 'remplaceDtCenter', $flux);

        // remplacer les captions
        $patterns_caption = '/<table(.*)<caption>(.*)<\/caption>(.*)<\/table>/iUms';
        function remplaceCaption($matches) { 
            $table  = '<table style="border:none;"'.$matches[1].'<tr><td style="text-align: center;border:none;">'.$matches[2].'</td></tr>';
            $table .= '<tr><td style="border:none;">';
            $table .= '<table'.$matches[1].$matches[3].'</table>';
            $table .= '</td></tr></table>';
            return $table; 
        }
        $flux = preg_replace_callback($patterns_caption, 'remplaceCaption', $flux);

        // tableaux centré
        $flux = preg_replace('/<table/iUms', '<table align="center"', $flux);

        // gestion de cractères soéciaux et de charset
        $flux = spipdf_first_clean($flux);

        // appel de la classe HTML2pdf
        require_once(dirname(__FILE__).'/html2pdf/html2pdf.class.php');
        try
        {
            $html2pdf = new HTML2PDF('P','A4','fr', false, 'ISO-8859-15');
            $html2pdf->setDefaultFont('Arial');
            $html2pdf->writeHTML($flux);
            if(defined('SPIPDF_DEBUG_HTML2PDF')) {
                $html2pdf->setModeDebug();
            }

            // identifiant de l'article
            $id_article = $GLOBALS['contexte']['id_article'];

            // titre du fichier avec pattern ou sans
            if(defined('SPIPDF_TITRE_FICHIER')) {
                $titre_pdf = sprintf(SPIPDF_TITRE_FICHIER, $id_article);
            } else {
                $titre_pdf = 'Article-'.$id_article.'.pdf';
            }

            // génère et renvoi la page au browser
            $html2pdf->Output($titre_pdf);
            return null; // stoppe le flux d'execution
        }
        catch(HTML2PDF_exception $e) { echo $e; }
    }

    return $flux;

}
?>
