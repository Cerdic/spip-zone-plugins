<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * @param string $url
 * @param string $utiliser_namespace
 * @return array
 */
function url2flux_xml($url, $utiliser_namespace='false') {

	include_spip('inc/distant');
	$flux = recuperer_page($url);

	$xml = @simplexml2array(simplexml_load_string($flux), $utiliser_namespace);

	return $xml;
}

/**
 * @param string $url
 * @return mixed
 */
function url2flux_json($url) {

	include_spip('inc/distant');
	$flux = recuperer_page($url);

	// TODO : à compléter avec le traitement JSON pour OpenWeatherMap

	return $xml;
}


/**
 * Transforme un objet SimpleXML en tableau PHP
 *
 * @param object $obj
 * @return array
**/
// http://www.php.net/manual/pt_BR/book.simplexml.php#108688
// xaviered at gmail dot com 17-May-2012 07:00
function simplexml2array($obj, $utiliser_namespace='false') {

	// Cette fonction getDocNamespaces() est longue sur de gros xml. On permet donc
	// de l'activer ou pas suivant le contenu supposé du XML
	if ($utiliser_namespace)
		$namespace = $obj->getDocNamespaces(true);
	$namespace[NULL] = NULL;

	$children = array();
	$attributes = array();
	$name = strtolower((string)$obj->getName());

	$text = trim((string)$obj);
	if( strlen($text) <= 0 ) {
		$text = NULL;
	}

	// get info for all namespaces
	if (is_object($obj)) {
		foreach( $namespace as $ns=>$nsUrl ) {
			// atributes
			$objAttributes = $obj->attributes($ns, true);
			foreach( $objAttributes as $attributeName => $attributeValue ) {
				$attribName = strtolower(trim((string)$attributeName));
				$attribVal = trim((string)$attributeValue);
				if (!empty($ns)) {
					$attribName = $ns . ':' . $attribName;
				}
				$attributes[$attribName] = $attribVal;
			}

			// children
			$objChildren = $obj->children($ns, true);
			foreach( $objChildren as $childName=>$child ) {
				$childName = strtolower((string)$childName);
				if( !empty($ns) ) {
					$childName = $ns.':'.$childName;
				}
				$children[$childName][] = simplexml2array($child);
			}
		}
	}

	return array(
		'name'=>$name,
		'text'=>$text,
		'attributes'=>$attributes,
		'children'=>$children
	);
}

?>