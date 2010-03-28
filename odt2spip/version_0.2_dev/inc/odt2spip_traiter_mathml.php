<?php

/**
 * Créer un article à partir d'un fichier au format odt
 *
 * @author cy_altern
 * @license GNU/LGPL
 *
 * @package plugins
 * @subpackage odt2spip
 * @category import
 *
 * @version $Id$
 *
 */

/**
 * Traiter les éléments au format {@link http://fr.wikipedia.org/wiki/MathML MathML}
 *
 * Le format {@link http://fr.wikipedia.org/wiki/MathML MathML} est transformé par
 * XSLT pour générer du code {@link http://fr.wikipedia.org/wiki/Aide%3AFormules_TeX LaTEX}
 * interprétable par SPIP (cf. {@link http://www.spip.net/fr_article3016.html})
 *
 * @param string $chemin_fichier Chemin provisoire dans lequel a été téléchargé le fichier
 * @return string Message d'erreur ou résultat de la transformation XSLT
 */
function odt2spip_traiter_mathml($chemin_fichier) {
	// recuperer le contenu du fichier
	if (!$mathml = file_get_contents($chemin_fichier)) {
		return(_T('odtspip:err_transformation_xslt_mathml'));
	}

	// virer le DOCTYPE qui plante le parseur vu que la dtd n'est pas disponible
	$mathml = preg_replace('/<!DOCTYPE.*?>/i', '', $mathml);

	// variable en dur pour xslt utilisée
	// $xml_entre = _DIR_TMP.'odt2spip/'.$id_auteur.'/content.xml';	// chemin du fichier xml à lire
	$xslt_texte = _DIR_PLUGIN_ODT2SPIP . 'inc/xsltml/mmltex.xsl'; // chemin de la xslt à utiliser pour les maths
	
	// appliquer la transformation XSLT sur le fichier content.xml
	// déterminer les fonctions xslt à utiliser (php 4 ou php 5)
	if (!class_exists('XSLTProcessor')) {
		// on est en php4 : utiliser l'extension et les fonction xslt de Sablotron
		// Crée le processeur XSLT
		$xh = xslt_create();
		// si on est sur un serveur Windows utiliser xslt_set_base avec le préfixe file://
		if (strpos($_SERVER['SERVER_SOFTWARE'], 'Win') !== false) {
			xslt_set_base($xh, 'file://' . getcwd () . '/');
		}
	  
		// lancer le parseur
		$arguments = array('/_xml' => $mathml);
		$latex_sortie = xslt_process($xh, 'arg:/_xml', $xslt_texte, NULL, $arguments);
		if (!$latex_sortie) {
			return(_T('odtspip:err_transformation_xslt_mathml'));
		}
	  
		// Détruit le processeur XSLT
		xslt_free($xh);
	} else {
		// on est php5: utiliser les fonctions de la classe XSLTProcessor
		$proc = new XSLTProcessor();
		$xml = new DOMDocument();
		$xsl = new DOMDocument();

		$xml->loadXML($mathml);
		$xsl->load($xslt_texte);
		$proc->importStylesheet($xsl); // attachement des règles xsl
		
		// lancer le parseur
		if (!$latex_sortie = $proc->transformToXml($xml)) {
			return(_T('odtspip:err_transformation_xslt_mathml'));
		}
	}

	return $latex_sortie;  
}

?>