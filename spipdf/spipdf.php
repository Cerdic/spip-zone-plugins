<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 ff=unix fenc=utf8: */

/**
 * Génération d'article SPIP au format PDF
 *
 * @package      spiPDF
 * @author       Yves Tannier [grafactory.net]
 * @copyright    2010 Yves Tannier
 * @link         http://www.spip-contrib.net/spiPDF-generer-des-contenus-sur-mesure-en-PDF
 * @link         http://zone.spip.org/trac/spip-zone/browser/_plugins_/spipdf
 * @link         http://www.grafactory.net/
 * @license      GPL Gnu Public Licence
 * @version      0.1
 */

// Charset (qui peut être défini dans un fichier d'options
if (!defined('SPIPDF_CHARSET')) {
	define('SPIPDF_CHARSET', 'UTF-8');
	//define('SPIPDF_CHARSET', 'ISO-8859-15');
}

// utilisé pour le constructeur de HTML2PDF
if(SPIPDF_CHARSET=='UTF-8') {
	define('SPIPDF_UNICODE', true);
} else {
	define('SPIPDF_UNICODE', false);
}

// pour les function unicode2charset
include_spip('inc/charsets') ;

// repris dans le plugin article_pdf => a modifier
// http://zone.spip.org/trac/spip-zone/browser/_plugins_/article_pdf
function spipdf_first_clean($texte) {

		// supprimer les remarques HTML (du Couteau Suisse ?)
		$texte = preg_replace(',<!-- .* -->,msU', '', $texte);
		
		$trans = array();
		$trans["<br />\n"] = '<BR>'; // Pour éviter que le \n ne se tranforme en espace dans les <DIV class=spip_code> (TT, tag SPIP : code)

		// gestion d'un encodage latin1
		if(SPIPDF_CHARSET=='ISO-8859-15' || SPIPDF_CHARSET=='iso-8859-15') {
			$trans['&#176;'] = '°';
			$trans["&#339;"] = 'oe';
			$trans["&#8211;"] = '-';
			$trans["&#8216;"] = '\'';
			$trans["&#8217;"] = '\'';
			$trans["&#8220;"] = '"';
			$trans["&#8221;"] = '"';
			$trans["&#8230;"] = '...';
			$trans["&#8364;"] = 'Euros';
			$trans["&ucirc;"] = "û";
			$trans['->'] = '-»';
			$trans['<-'] = '«-';
			$trans['&mdash;'] = '-';
			$trans['&deg;']='°';
			$trans['œ']='oe';
			$trans['Œ']='OE';
			$trans['…']='...';
			$trans['&euro;']='Euros';
			$trans['€']='Euros';
			$trans['&copy;'] ='©';
		}
		// pas d'insécable
		$trans['&nbsp;'] = ' ';

		// certains titles font paniquer l'analyse
		// TODO : a peaufiner car ils sont necessaires pour les signets
		// <bookmark title="Compatibilité" level="0" ></bookmark>
		// http://wiki.spipu.net/doku.php?id=html2pdf:fr:v4:bookmark
		//$texte = preg_replace(',title=".*",msU', 'title=""', $texte);

		$texte = strtr($texte, $trans);
		if(SPIPDF_CHARSET=='UTF-8')
			$texte = charset2unicode($texte);
		else
			$texte = unicode2charset(charset2unicode($texte), SPIPDF_CHARSET); // Repasser tout dans le charset demandé
		
		// Décoder les codes HTML dans le charset final
		$texte = html_entity_decode($texte, ENT_NOQUOTES, SPIPDF_CHARSET);
		
		return $texte;
}


function spipdf_nettoyer_html($html) {

	// supprimer les spans autour des images et récupérer le placement
	$patterns_float = '/<span class=\'spip_document_.*spip_documents.*float:(.*);.*>(.*)<\/span>/iUms';
	function remplaceSpan($matches) { return str_replace('img', 'img style="padding:5px;" align="'.$matches[1].'"', $matches[2]); }
	$html = preg_replace_callback($patterns_float, 'remplaceSpan', $html);

	// supprimer les spans autour des images
	$patterns_float = '/<span class=\'spip_document_.*spip_documents.*>(.*)<\/span>/iUms';
	function remplaceSpanCenter($matches) { return $matches[1]; }
	$html = preg_replace_callback($patterns_float, 'remplaceSpanCenter', $html);

	// supprimer les dl autour des images et récupérer le placement
	$patterns_float = '/<dl class=\'spip_document_.*spip_documents.*float:(.*);.*<dt>(.*)<\/dt>.*<\/dl>/iUms';
	function remplaceDt($matches) { return str_replace('img', 'img style="padding:5px;" align="'.$matches[1].'"', $matches[2]); }
	$html = preg_replace_callback($patterns_float, 'remplaceDt', $html);

	// supprimer les dl autour des images
	$patterns_float = '/<dl class=\'spip_document_.*spip_documents.*<dt>(.*)<\/dt>.*<\/dl>/iUms';
	function remplaceDtCenter($matches) { return $matches[1]; }
	$html = preg_replace_callback($patterns_float, 'remplaceDtCenter', $html);

	// remplacer les captions
	$patterns_caption = '/<table(.*)<caption>(.*)<\/caption>(.*)<\/table>/iUms';
	function remplaceCaption($matches) { 
		$table  = '<table style="border:none;"'.$matches[1].'<tr><td style="text-align: center;border:none;">'.$matches[2].'</td></tr>';
		$table .= '<tr><td style="border:none;">';
		$table .= '<table'.$matches[1].$matches[3].'</table>';
		$table .= '</td></tr></table>';
		return $table; 
	}
	$html = preg_replace_callback($patterns_caption, 'remplaceCaption', $html);

	// tableaux centré
	$html = preg_replace('/<table/iUms', '<table align="center"', $html);

	// gestion des caractères spéciaux et de charset
	$html = spipdf_first_clean($html);
	
	return $html;

}

// traitement principal. avec ce pipeline, le PDF est mis en cache et recalculé "normalement"
function spipdf_recuperer_fond($flux) {

	// Le squelette est-il appelé par spipdf.html
	if ($flux['args']['contexte']['html2pdf'] == 'oui') {
		$html = $flux['data']['texte'];
		$html = spipdf_nettoyer_html($html);
		// appel de la classe HTML2pdf
		require_once(dirname(__FILE__).'/html2pdf/html2pdf.class.php');
		try
		{
            // les paramétres d'orientation et de format son écrasé par ceux défini dans la balise <page> du squelette
			$html2pdf = new HTML2PDF('P','A4',$flux['args']['contexte']['lang'], SPIPDF_UNICODE, SPIPDF_CHARSET);
            // mode debug de HTML2PDF
            if(defined('SPIPDF_DEBUG_HTML2PDF')) {
                $html2pdf->setModeDebug();
            }
            // police différente selon unicode ou latin
            if(SPIPDF_UNICODE) {
                $police_caractere = 'FreeSans';
            } else {
                $police_caractere = 'Arial';
            }
            $html2pdf->setDefaultFont($police_caractere);
			$html2pdf->writeHTML($html);
            // envoyer le code binaire du PDF dans le flux
			$flux['data']['texte'] = $html2pdf->Output('', true);
			
			// On échappe les suites de caractères <? pour éviter des erreurs d'évaluation PHP (seront remis en place avec affichage_final)
            // l'erreur d'évaluation est liée à la directive short_open_tag=On dans la configuration de PHP
			$flux['data']['texte'] = preg_replace('/<\?/','<§§§§§>',$flux['data']['texte']);
		}
		catch(HTML2PDF_exception $e) { echo $e; }
	}

	return $flux;

}

// On rétablit les <? du code PDF
function spipdf_affichage_final($texte) {
	return preg_replace('/<§§§§§>/','<?',$texte);
}
?>
