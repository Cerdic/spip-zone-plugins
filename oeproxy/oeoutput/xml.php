<?php
/*
 * Plugin oEmebed The Web
 * (c) 2011 Cedric Morin
 * Distribue sous licence GPL
 *
 * http://oembed.com/
 *
 */

include_spip('inc/filtres');

function oeoutput_xml_dist($res){

	header("Content-type: text/xml;");
	echo '<'.'?xml version="1.0" encoding="utf-8" standalone="yes"?>'."\n";
	echo "<oembed>";
	echo assocToXML($res,0);
	echo "\n</oembed>\n";

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
function assocToXML($theArray, $tabCount = 2){
	//echo "The Array: ";
	//var_dump($theArray);
	// variables for making the XML output easier to read
	// with human eyes, with tabs delineating nested relationships, etc.

	$theXML = "";
	$tabCount++;
	$tabSpace = "";
	for ($i = 0; $i<$tabCount; $i++){
		$tabSpace .= "\t";
	}

	// parse the array for data and output xml
	foreach ($theArray as $tag => $val){
		if (!is_array($val)){
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

?>