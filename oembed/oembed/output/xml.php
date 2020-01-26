<?php
/**
 * Plugin oEmbed
 * Licence GPL3
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/filtres');

/**
 * Generer une sortie XML a partir d'un tableau associatif
 * @param array $res
 * @param bool $output
 * @return void|string
 */
function oembed_output_xml_dist($res, $output = true) {

	$out = '<'.'?xml version="1.0" encoding="utf-8" standalone="yes"?>'."\n";
	$out .= '<oembed>';
	$out .= oembed_assocToXML($res, 0);
	$out .= "\n</oembed>\n";
	if (!$output) {
		return $out;
	}

	header('Content-type: text/xml; charset=utf-8');
	echo $out;
}


/*
	* James Earlywine - July 20th 2011
	*
	* Translates a jagged associative array
	* to XML
	*
	* @param : $theArray - The jagged Associative Array
	* @param : $tabCount - for persisting tab count across recursive function calls
	*/
function oembed_assocToXML($theArray, $tabCount = 2) {
	//echo "The Array: ";
	//var_dump($theArray);
	// variables for making the XML output easier to read
	// with human eyes, with tabs delineating nested relationships, etc.

	$theXML = '';
	$tabCount++;
	$tabSpace = '';
	for ($i = 0; $i<$tabCount; $i++) {
		$tabSpace .= "\t";
	}

	// parse the array for data and output xml
	foreach ($theArray as $tag => $val) {
		if (!is_array($val)) {
			$theXML .= PHP_EOL
						.$tabSpace
						.'<'.$tag.'>'.
						texte_backend($val)
						.'</'.$tag.'>';
		} else {
			$tabCount++;
			$theXML .= PHP_EOL.$tabSpace.'<'.$tag.'>'.assocToXML($val, $tabCount+1);
			$theXML .= PHP_EOL.$tabSpace.'</'.$tag.'>';
		}
	}

	return $theXML;
}
