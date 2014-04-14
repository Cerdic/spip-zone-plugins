<?php
/**
 * Ce fichier contient la fonction surchargeable de transformation d'un XML en tableau PHP.
 * Cette fonction utilise les fonctions d'encodage et décodage JSON.
 *
 * @package SPIP\BOUSSOLE\Outils\XML
 */

if (!defined("_ECRIRE_INC_VERSION")) return;


/**
 * Transformation d'un texte XML en tableau PHP.
 *
 * L'argument XML est un texte: il est au préalable converti en objet *SimpleXML*
 * par la fonction `simplexml_load_string()`. Ensuite, c'est l'objet *SimpleXML* qui est
 * traduit en tableau PHP à partir des fonctions `json_encode` et `json_decode`.
 *
 * @example
 *	```
 * $page = recuperer_page($action);
 * $convertir = charger_fonction('xml_decode', 'inc');
 * $tableau = $convertir($page);
 *	```
 *
 * @param string $xml
 * 		XML à phraser et à transformer en tableau PHP. Le XML est fourni comme une chaine
 * 		de caractères représentant le texte XML lui-même.
 * @return array
 */
function inc_xml_decode_dist($xml){
	// On englobe la chaine xml fournie par une balise bidon afin de renvoyer le nom de la balise
	// de plus haut niveau du xml car simpleXML renvoie un objet sans cette balise.
	$objet_xml = simplexml_load_string("<dummyroot>${xml}</dummyroot>");
	$tableau = json_decode(json_encode($objet_xml), true);

	return $tableau;
}


?>
