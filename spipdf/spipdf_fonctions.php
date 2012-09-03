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
 * @version      0.2
 */

// Options pour les marges des PDF, valables seulement pour la librairie mPDF
// définissez vos options par défaut directement dans votre mes_options.php
if (!defined('_SPIPDF_FORMAT')){
	define('_SPIPDF_FORMAT', 'A4');
}
if (!defined('_SPIPDF_MARGIN_TOP')){
	define('_SPIPDF_MARGIN_TOP', 20);
}
if (!defined('_SPIPDF_MARGIN_RIGHT')){
	define('_SPIPDF_MARGIN_RIGHT', 20);
}
if (!defined('_SPIPDF_MARGIN_BOTTOM')){
	define('_SPIPDF_MARGIN_BOTTOM', 15);
}
if (!defined('_SPIPDF_MARGIN_LEFT')){
	define('_SPIPDF_MARGIN_LEFT', 15);
}
if (!defined('_SPIPDF_MARGIN_HEADER')){
	define('_SPIPDF_MARGIN_HEADER', 2);
}
if (!defined('_SPIPDF_MARGIN_FOOTER')){
	define('_SPIPDF_MARGIN_FOOTER', 2);
}

// Charset (qui peut être défini dans un fichier d'options
if (!defined('SPIPDF_CHARSET')){
	define('SPIPDF_CHARSET', 'UTF-8');
	//define('SPIPDF_CHARSET', 'ISO-8859-15');
}

// utilisé pour le constructeur de HTML2PDF
if (SPIPDF_CHARSET=='UTF-8'){
	define('SPIPDF_UNICODE', true);
} else {
	define('SPIPDF_UNICODE', false);
}

// les librairies necessaires doivent-être dans "lib". A la racine ou dans le répertoire du plugin
if (!defined('_DIR_LIB')){
	define('_DIR_LIB', _DIR_RACINE . 'lib/');
}

// pour les function unicode2charset
include_spip('inc/charsets');

// repris dans le plugin article_pdf => a modifier
// http://zone.spip.org/trac/spip-zone/browser/_plugins_/article_pdf
function spipdf_first_clean($texte){

	// supprimer les remarques HTML (du Couteau Suisse ?)
	$texte = preg_replace(',<!-- .* -->,msU', '', $texte);

	$trans = array();
	$trans["<br />\n"] = '<BR>'; // Pour éviter que le \n ne se tranforme en espace dans les <DIV class=spip_code> (TT, tag SPIP : code)

	// gestion d'un encodage latin1
	if (SPIPDF_CHARSET=='ISO-8859-15' || SPIPDF_CHARSET=='iso-8859-15'){
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
		$trans['&deg;'] = '°';
		$trans['œ'] = 'oe';
		$trans['Œ'] = 'OE';
		$trans['…'] = '...';
		$trans['&euro;'] = 'Euros';
		$trans['€'] = 'Euros';
		$trans['&copy;'] = '©';
	}
	// pas d'insécable
	$trans['&nbsp;'] = ' ';

	// certains titles font paniquer l'analyse
	// TODO : a peaufiner car ils sont necessaires pour les signets
	// <bookmark title="Compatibilité" level="0" ></bookmark>
	// http://wiki.spipu.net/doku.php?id=html2pdf:fr:v4:bookmark
	//$texte = preg_replace(',title=".*",msU', 'title=""', $texte);

	$texte = strtr($texte, $trans);
	if (SPIPDF_CHARSET=='UTF-8'){
		$texte = charset2unicode($texte);
	} else {
		$texte = unicode2charset(charset2unicode($texte), SPIPDF_CHARSET); // Repasser tout dans le charset demandé
	}

	// Décoder les codes HTML dans le charset final
	$texte = html_entity_decode($texte, ENT_NOQUOTES, SPIPDF_CHARSET);

	return $texte;
}


//function spipdf_remplaceSpan($matches) { return str_replace('img', 'img style="padding:5px;" style="float:'.$matches[1].'"', $matches[2]); }
function spipdf_remplaceSpan_wfloat($matches){
	return str_replace('img', 'img style="padding:5px;" class="pdf_img_float_' . $matches[1] . '"', $matches[2]);
}

function spipdf_remplaceSpan($matches){
	return str_replace('img', 'img style="padding:5px;" align="' . $matches[1] . '"', $matches[2]);
}

function spipdf_remplaceSpanCenter($matches){
	return $matches[1];
}

//function spipdf_remplaceDt($matches) { return str_replace('img', 'img style="padding:5px;" style="float:'.$matches[1].'"', $matches[2]); }
function spipdf_remplaceDt_wfloat($matches){
	return str_replace('img', 'img style="padding:5px;" class="pdf_img_float_' . $matches[1] . '"', $matches[2]);
}

function spipdf_remplaceDt($matches){
	return str_replace('img', 'img style="padding:5px;" align="' . $matches[1] . '"', $matches[2]);
}

function spipdf_remplaceIdParName($matches){
	return str_replace('id=\'', 'name=\'', $matches[0]);
}

function spipdf_remplaceFloatPuce($matches){
	return str_replace('style=\'', 'style=\'float:left;', $matches[0]);
}

function spipdf_remplaceDtCenter($matches){
	return $matches[1];
}

function spipdf_remplaceCaption($matches){
	$table = '<table style="border:none;"' . $matches[1] . '<tr><td style="text-align: center;border:none;">' . $matches[2] . '</td></tr>';
	$table .= '<tr><td style="border:none;">';
	$table .= '<table' . $matches[1] . $matches[3] . '</table>';
	$table .= '</td></tr></table>';
	return $table;
}

function spipdf_nettoyer_html($html, $params_pdf = array()){
	// supprimer les spans autour des images et récupérer le placement
	$patterns_float = '/<span class=\'spip_document_.*spip_documents.*float:(.*);.*>(.*)<\/span>/iUms';
	$html = preg_replace_callback($patterns_float, !empty($params_pdf['float']) ? 'spipdf_remplaceSpan' : 'spipdf_remplaceSpan_wfloat', $html);

	// supprimer les spans autour des images
	$patterns_float = '/<span class=\'spip_document_.*spip_documents.*>(.*)<\/span>/iUms';
	$html = preg_replace_callback($patterns_float, 'spipdf_remplaceSpanCenter', $html);

	// supprimer les dl autour des images et récupérer le placement
	$patterns_float = '/<dl class=\'spip_document_.*spip_documents.*float:(.*);.*<dt>(.*)<\/dt>.*<\/dl>/iUms';
	$html = preg_replace_callback($patterns_float, !empty($params_pdf['float']) ? 'spipdf_remplaceDt' : 'spipdf_remplaceDt_wfloat', $html);

	// replacer id par name pour les notes
	$patterns_note = '/<a.*href.*class=\'spip_note\'.*>/iUms';
	$html = preg_replace_callback($patterns_note, 'spipdf_remplaceIdParName', $html);

	// float sur les puces graphiques TODO
	$patterns_puce = '/<a.*href.*class=\'puce\'.*>/iUms';
	//$html = preg_replace_callback($patterns_puce, 'spipdf_remplaceFloatPuce', $html);
	//img src="local/cache-vignettes/L8xH11/puce-32883.gif" class="puce" alt="-" style="height: 11px; width: 8px;" height="11" width="8">

	// supprimer les dl autour des images centrer
	$patterns_float = '/<dl class=\'spip_document_.*spip_documents.*<dt>(.*)<\/dt>.*<\/dl>/iUms';
	$html = preg_replace_callback($patterns_float, 'spipdf_remplaceDtCenter', $html);

	// remplacer les captions
	if (!empty($params_pdf['caption'])){
		$patterns_caption = '/<table(.*)<caption>(.*)<\/caption>(.*)<\/table>/iUms';
		$html = preg_replace_callback($patterns_caption, 'spipdf_remplaceCaption', $html);
	}

	// tableaux centré
	$html = preg_replace('/<table/iUms', '<table align="center"', $html);

	// gestion des caractères spéciaux et de charset
	$html = spipdf_first_clean($html);

	return $html;

}

// traiter la balise page
function traite_balise_page($html){

	// on teste la balise page
	if (preg_match('/<page(.*)>/iUms', $html, $matches)){
		// on crée un tableau avec (beurk) Global pour accèder aux valeurs de pages
		if (!empty($matches[1])){
			$balise_page = $matches[1];
			$pattern = '/(.*)="(.*)"/iUms';
			function getBalise($matches){
				$matches[2] = str_replace('mm', '', $matches[2]);
				$GLOBALS['valeurs_page'][trim($matches[1])] = trim($matches[2]);
			}

			$balise_page = preg_replace_callback($pattern, 'getBalise', $balise_page);

			// supprimer <page> et </page>
			$html = preg_replace('/<\/?page(.*)>/iUms', '', $html);

			return $html;
		}

	} else {
		return $html;
	}

}

// supprimer le puces graphiques (d'après le plugin couteau suisse)
function spipdf_pre_typo($texte){
	return preg_replace('/^-\s*(?![-*#])/m', '-* ', $texte);
}

// traitement principal. avec ce pipeline, le PDF est mis en cache et recalculé "normalement"
function spipdf_html2pdf($html){

	// les librairies possibles
	$possible_librairies = array(
		'mpdf' => array( // gére le float d'image mais pas les captions de tableau
			'float' => true,
			'caption' => true,
			'traite_balise_page' => true
		),
		'html2pdf' => array( // ne gére pas le float d'image et les captions
			'float' => false,
			'caption' => true
		),
		'dompdf' => array( // domPDF beta 0.6 EXPERIMENTAL
			'float' => false,
			'caption' => true,
			'traite_balise_page' => true
		),
	);

	// choix de la classe de génération via la balise <page lib>
	if (preg_match('/\<page*.lib_pdf=["|\'](.*)["|\']/iUms', $html, $match_librairie)
		&& !empty($match_librairie[1])
		&& array_key_exists(strtolower($match_librairie[1]), $possible_librairies)
	){
		$librairie_pdf = strtolower($match_librairie[1]);
	} else {
		$librairie_pdf = 'mpdf';
	}

	// tester si la librairie est dans /lib à la racine du spip ou dans le répertoire du plugin
	if (is_dir(_DIR_LIB . $librairie_pdf)){
		$dir_librairie_pdf = _DIR_LIB . $librairie_pdf . '/';
	} elseif (is_dir(dirname(__FILE__) . '/lib/' . $librairie_pdf)) {
		$dir_librairie_pdf = dirname(__FILE__) . '/lib/' . $librairie_pdf . '/';
	} else {
		die('Impossible de trouver la librairie de génération de PDF ' . $librairie_pdf . '. vérifiez que vous l\'avez bien téléchargée et installée dans /lib');
	}

	// nettoyer le HTML et gérer les placements d'image en fonction de la librairie utilisée
	$html = spipdf_nettoyer_html($html, $possible_librairies[$librairie_pdf]);

	// Debug = voir le html sans génération de PDF
	if (isset($_GET['debug_spipdf'])){
		echo $html;
		exit;
	}

	// du A4 par defaut
	$format_page = _SPIPDF_FORMAT;

	// traiter la balise page pour les librairies qui ne la comprennent pas
	if (!empty($possible_librairies[$librairie_pdf]['traite_balise_page'])){

		$html = traite_balise_page($html);

		// dans balise_page, on ne récupère que quelques possibilité dont le format
		if (!empty($GLOBALS['valeurs_page'])){
			if (!empty($GLOBALS['valeurs_page']['format']))
				$format_page = $GLOBALS['valeurs_page']['format'];
			if (!empty($GLOBALS['valeurs_page']['backtop']))
				$backtop = $GLOBALS['valeurs_page']['backtop'];
			else
				$backtop = _SPIPDF_MARGIN_TOP;
			if (!empty($GLOBALS['valeurs_page']['backbottom']))
				$backbottom = $GLOBALS['valeurs_page']['backbottom'];
			else
				$backbottom = _SPIPDF_MARGIN_BOTTOM;
			if (!empty($GLOBALS['valeurs_page']['backleft']))
				$backleft = $GLOBALS['valeurs_page']['backleft'];
			else
				$backleft = _SPIPDF_MARGIN_LEFT;
			if (!empty($GLOBALS['valeurs_page']['backright']))
				$backright = $GLOBALS['valeurs_page']['backright'];
			else
				$backright = _SPIPDF_MARGIN_RIGHT;
			if (!empty($GLOBALS['valeurs_page']['margin_header']))
				$margin_header = $GLOBALS['valeurs_page']['margin_header'];
			else
				$margin_header = _SPIPDF_MARGIN_HEADER;
			if (!empty($GLOBALS['valeurs_page']['margin_footer']))
				$margin_footer = $GLOBALS['valeurs_page']['margin_footer'];
			else
				$margin_footer = _SPIPDF_MARGIN_FOOTER;
		}
	}

	if ($librairie_pdf=='mpdf'){ // la librairie mPDF

		// si il y a des options dans la balise page
		// http://mpdf1.com/manual/index.php?tid=307

		// le chemin relatif vers mPDF
		define('_MPDF_PATH', $dir_librairie_pdf);
		include_once(_MPDF_PATH . 'mpdf.php');

		// la classe mPDF
		$mpdf = new mPDF(SPIPDF_CHARSET, $format_page, 0, "", $backleft, $backright, $backtop, $backbottom, $margin_header, $margin_footer);
		$mpdf->WriteHTML($html);

		$html = $mpdf->Output('', 'S'); // envoyer le code binaire du PDF dans le flux
		$echap_special_pdf_chars = true;

	} elseif ($librairie_pdf=='dompdf') { // la librairie dompdf beta 0.6 // EXPERIMENTAL

		// le chemin relatif vers mPDF
		require_once(_DIR_LIB . 'dompdf/dompdf_config.inc.php');

		$dompdf = new DOMPDF();
		$dompdf->load_html($html, SPIPDF_CHARSET);
		$dompdf->set_paper($format_page);
		$dompdf->render();

		$html = $dompdf->output(); // envoyer le code binaire du PDF dans le flux
		$echap_special_pdf_chars = true;

	} else { // la librairie HTML2PDF par défaut

		// appel de la classe HTML2pdf
		require_once($dir_librairie_pdf . 'html2pdf.class.php');
		try {
			// les paramétres d'orientation et de format son écrasé par ceux défini dans la balise <page> du squelette
			$html2pdf = new HTML2PDF('P', $format_page, $flux['args']['contexte']['lang'], SPIPDF_UNICODE, SPIPDF_CHARSET);

			// mode debug de HTML2PDF
			if (defined('SPIPDF_DEBUG_HTML2PDF')){
				$html2pdf->setModeDebug();
			}
			// police différente selon unicode ou latin
			if (SPIPDF_UNICODE){
				$police_caractere = 'FreeSans';
			} else {
				$police_caractere = 'Arial';
			}
			$html2pdf->setDefaultFont($police_caractere);
			$html2pdf->writeHTML($html);

			$html = $html2pdf->Output('', true); // envoyer le code binaire du PDF dans le flux
			$echap_special_pdf_chars = true;
		} catch (HTML2PDF_exception $e) {
			echo $e;
		}

	}

	// On échappe les suites de caractères <? pour éviter des erreurs d'évaluation PHP (seront remis en place avec affichage_final)
	// l'erreur d'évaluation est liée à la directive short_open_tag=On dans la configuration de PHP
	if (!empty($echap_special_pdf_chars)
		AND strpos($html, "<" . "?")!==false
	){
		$html = str_replace("<" . "?", "<\2\2?", $html);
	}

	return $html;

}

/**
 * On rétablit les <? du code PDF si necessaire
 * on n'agit que sur les pages non html
 *
 * @param string $texte
 * @return string
 */
function spipdf_affichage_final($texte){
	if ($GLOBALS['html']==false
		AND strpos($texte, "<\2\2?")!==false
	){
		$texte = str_replace("<\2\2?", "<" . "?", $texte);
	}
	return $texte;
}

?>
