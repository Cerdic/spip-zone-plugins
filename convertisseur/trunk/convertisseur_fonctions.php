<?php
/**
 * Ce fichier contient l'ensemble fonctions implémentant l'API du plugin Taxonomie.
 *
 * @package SPIP\TAXONOMIE\API
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Conversion d'un texte donné dans un format X dans un format Y.
 * Cette fonction est principalement utilisée pour convertir un texte mediawiki, en SPIP.
 * Il est aussi possible de convertir de SPIP vers xxx
 *
 * @api
 * @filtre
 *
 * @param string	$texte
 * 		Le texte à convertir
 * @param string	$format
 * 		Formatage du texte d'entrée, correspond à :
 *		- 'BBcode_SPIP' 	: BBcode (PhpBB) vers SPIP
 *		- 'DOCX_SPIP'		: DOCX (Word 2007) vers SPIP
 *		- 'DotClear_SPIP'	: DotClear vers SPIP
 *		- 'MediaWiki_SPIP'	: Wiki (MediaWiki) vers SPIP
 *		- 'MoinWiki_SPIP'	: Wiki (MoinWiki) vers SPIP
 *		- 'SLA_SPIP'		: SLA (Scribus) vers SPIP
 *		- 'XTG_SPIP'		: XTG (XPressTags) vers SPIP
 *		- 'SPIP_txt'		: SPIP vers texte brut
 *		- 'SPIP_mediawiki'	: SPIP vers Wiki (MediaWiki)
 * @param array		$options
 * 		Options de traitement du texte. Actuellement l'index 'charset' permet de préciser que le
 * 		le texte d'entrée doit être préalablement mis au charset précisé avant d'être converti
 *
 * @return string
 * 		Texte converti ou chaine vide
 */
function convertisseur_texte_spip($texte, $format, $options=array()) {
	include_spip('inc/convertisseur');
	global $conv_formats;

	$texte_converti = '';

	if (($texte)
	AND array_key_exists($format, $conv_formats)) {
		// Si demandé, on convertit le texte d'entrée dans le charset du site (todo: pourquoi pas en utf-8???)
		if (isset($options['charset']) AND ($options['charset'] == 'utf-8')) {
			include_spip('inc/charsets');
			$texte = importer_charset($texte);
		}

		$texte_converti = conversion_format($texte, $format);
		$texte_converti = nettoyer_format($texte_converti);
	}

	return $texte_converti;
}

?>