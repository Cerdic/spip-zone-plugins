<?php
/**
 * Ce fichier contient la fonction surchargeable de transformation d'un XML en tableau PHP.
 * Cette fonction est une réplication de la fonction identique de SPIP. Elle est dupliquée
 * pour pallier au fait que la fonction SPIP n'est dispobible qu'à partir de SPIP 3.0.10.
 *
 * @package SPIP\BOUSSOLE\Outils\XML
 *
 */


if (!defined("_ECRIRE_INC_VERSION")) return;


/**
 * Transformation d'un texte XML ou d'un objet SimpleXML en tableau PHP.
 *
 * Si l'argument XML est un texte, il est au préalable converti en objet SimpleXML
 * par la fonction `simplexml_load_string()`. Ensuite, c'est l'objet *SimpleXML* qui est
 * traduit en tableau PHP à partir de la fonction xmlObjToArr()`.
 *
 * @uses xmlObjToArr
 * @example
 *	```
 * $page = recuperer_page($action);
 * $convertir = charger_fonction('simplexml_to_array', 'inc');
 * $tableau = $convertir(simplexml_load_string($page), false);
 *
 * ou
 *
 * $tableau = $convertir($page, false);
 *	```
 *
 * @param string|object $xml
 * 		XML à phraser et à transformer en tableau PHP. Le XML peut être fourni :
 *
 * 		- soit comme une chaine de caractères représentant le texte XML lui-même,
 * 		- soit comme un objet XML produit par la fonction simplexml_load_string() de PHP.
 * @param bool $utiliser_namespace
 * 		Indicateur d'utilisation des namespaces dans le XML. Si aucun namespace est
 * 		utilisé, il est plus performant de mettre l'argument à `false afin
 * 		d'éviter l'appel à la fonction getDocNamespaces()` qui peut-être lourd.
 * @return array
 */
function inc_simplexml_to_array_dist($xml, $utiliser_namespace=false){
	// decoder la chaine en SimpleXML si pas deja fait
	if (is_string($xml))
		$xml = simplexml_load_string($xml);
	return array('root'=>@xmlObjToArr($xml, $utiliser_namespace));
}


/**
 * Transformation d'un objet SimpleXML en tableau PHP.
 *
 * @link http://www.php.net/manual/pt_BR/book.simplexml.php#108688
 * @autor xaviered at gmail dot com 17-May-2012 07:00
 *
 * @param object $objet_xml
 * 		Objet SimpleXML à phraser et à transformer en tableau PHP.
 * @param bool $utiliser_namespace
 * 		Indicateur d'utilisation des namespaces dans le XML. Si aucun namespace est
 * 		utilisé, il est plus performant de mettre l'argument à `false afin
 * 		d'éviter l'appel à la fonction getDocNamespaces()` qui peut-être lourd.
 * @return array
**/
function xmlObjToArr($objet_xml, $utiliser_namespace=false) {

	$tableau = array();

	// Cette fonction getDocNamespaces() est longue sur de gros xml. On permet donc
	// de l'activer ou pas suivant le contenu supposé du XML
	if (is_object($objet_xml)) {
		if (is_array($utiliser_namespace)){
			$namespace = $utiliser_namespace;
		}
		else {
			if ($utiliser_namespace)
				$namespace = $objet_xml->getDocNamespaces(true);
			$namespace[NULL] = NULL;
		}

		$name = strtolower((string)$objet_xml->getName());
		$text = trim((string)$objet_xml);
		if (strlen($text) <= 0) {
			$text = NULL;
		}

		$children = array();
		$attributes = array();

		// get info for all namespaces
		foreach( $namespace as $ns=>$nsUrl ) {
			// attributes
			$objAttributes = $objet_xml->attributes($ns, true);
			foreach( $objAttributes as $attributeName => $attributeValue ) {
				$attribName = strtolower(trim((string)$attributeName));
				$attribVal = trim((string)$attributeValue);
				if (!empty($ns)) {
					$attribName = $ns . ':' . $attribName;
				}
				$attributes[$attribName] = $attribVal;
			}

			// children
			$objChildren = $objet_xml->children($ns, true);
			foreach( $objChildren as $childName=>$child ) {
				$childName = strtolower((string)$childName);
				if( !empty($ns) ) {
					$childName = $ns.':'.$childName;
				}
				$children[$childName][] = xmlObjToArr($child, $namespace);
			}
		}

		$tableau = array(
			'name'=>$name,
		);
		if ($text)
			$tableau['text'] = $text;
		if ($attributes)
			$tableau['attributes'] = $attributes;
		if ($children)
			$tableau['children'] = $children;
	}

	return $tableau;
}

?>
