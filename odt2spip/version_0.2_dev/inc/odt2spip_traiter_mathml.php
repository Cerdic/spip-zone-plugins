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
 *
 */
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * appliquer la transformation XSLT spécifique des <maths> sur le fichier content.xml extrait du .ODT
 *
 * @internal XSLT pour la transformation MathML 2.0 to LaTeX :
 * 		Vasil Yaroshevich, <yarosh@raleigh.ru>
 * 		http://www.raleigh.ru/MathML/mmltex/index.php?lang=en
 * @param string $chemin_fichier Le chemin du fichier contenant le MathML
 * @return string Le LateX de sortie
 * 
 */
function odt2spip_traiter_mathml($chemin_fichier) {
	// recuperer le contenu du fichier
	if (!$mathml = file_get_contents($chemin_fichier))
		return(_T('odtspip:err_transformation_xslt_mathml'));

	// virer le DOCTYPE qui plante le parseur vu que la dtd n'est pas disponible
	$mathml = preg_replace('/<!DOCTYPE.*?>/i', '', $mathml);

	// appliquer la transformation XSLT sur le fichier content.xml
	// chemin du fichier xslt a utiliser pour les maths
	$xslt_texte = _DIR_PLUGIN_ODT2SPIP.'inc/xsltml/mmltex.xsl';
	
	// on est php5: utiliser les fonctions de la classe XSLTProcessor
	$proc = new XSLTProcessor();

	$xml = new DOMDocument();
	$xml->loadXML($mathml);
	$xsl = new DOMDocument();
	$xsl->load($xslt_texte);
	$proc->importStylesheet($xsl); // attachement des règles xsl

	// lancer le parseur
	if (!$latex_sortie = $proc->transformToXml($xml))
		return(_T('odtspip:err_transformation_xslt_mathml'));
  
    return $latex_sortie;
}

?>
