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

/**
 * Itérateurs de conversion.
 * Pour faire des <BOUCLE_conversion(DATA){source extracteur, fichier}>
 */

// Extracteurs qui renvoient des tableaux (TODO voir spip-cli convertir ligne 69 pour dynamiser)
$GLOBALS['extracteurs_connus'] = array('indesign_xml', 'quark_xml', 'xml_ocr', 'xml_de') ;

// Iterateur pour l'extracteur quark_xml
function inc_indesign_xml_to_array_dist($u){
	return activer_iterateur('indesign_xml', $u) ;
}

// Iterateur pour l'extracteur quark_xml
function inc_quark_xml_to_array_dist($u){
	return activer_iterateur('quark_xml', $u) ;
}

// Iterateur pour l'extracteur xml_ocr
function inc_xml_ocr_to_array_dist($u){
	return activer_iterateur('xml_ocr', $u) ;
}

// Iterateur pour l'extracteur xml_de
function inc_xml_de_to_array_dist($u){
	return activer_iterateur('xml_de', $u) ;
}

function activer_iterateur($extracteur, $u){
	
	$item = array();
	
	// convertir en tableau
	include_spip("extract/" . $extracteur);
	$item = call_user_func('convertir_' . $extracteur, $u);
	
	include_spip("inc/convertisseur");
	foreach($item as &$i)
		$i = nettoyer_format($i);
	
	$item['insertion'] = extracteur_preparer_insertion($item);
	
	$m[] = $item ;
	return $m ;
}

// Transformer le tableau de valeurs converties par un extracteur en format d'insertion pour spip
function extracteur_preparer_insertion($item){ 

	$texte = "" ; 
	$champs_article = array("surtitre", "titre", "chapo"); 

	# Champs articles 
	# Baliser les champs articles 

	foreach($item as $k => $v)
		if(in_array($k, $champs_article)) 
			$texte .= "<ins class='$k'>" . trim($v) . "</ins>\n" ; 

		# autres champs
		# en plus des champs de données converties, un extracteur peut envoyer des champs techniques (xml, logs, alertes), on ne les insert pas.
		foreach($item as $k => $v)       
			if(!in_array($k,array("texte","xml","logs","alertes")) and !in_array($k, $champs_article)) 
				if(is_array($v)) 
					$texte .= "<ins class='$k'>" . trim(join(",", $v)) . "</ins>\n"; 
				else 
					$texte .= "<ins class='$k'>" . trim($v) . "</ins>\n" ;

		# texte 
		$texte .=  "\n" . trim($item['texte']) . "\n" ;

	return $texte ; 
}
