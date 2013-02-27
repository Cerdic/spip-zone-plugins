<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

    /**
     * xml -> tableau
     * @param  string $u
     * @return array
	 * from cerdic et marcimat sur http://contrib.spip.net/Les-Iterateurs-pour-SPIP-2-1#forum457898
     */

function inc_simplexml_to_array_dist($u){
        return array('root'=>@xmlObjToArr(simplexml_load_string($u)));
}
     
    // http://www.php.net/manual/pt_BR/book.simplexml.php#108688 (17 mai 2012)
    function xmlObjToArr($obj) {
    # Cette fonction getDocNamespaces est tres gourmande sur de gros fichiers
    # $namespace = $obj->getDocNamespaces(true);
     
            $namespace[NULL] = NULL;
     
            $children = array();
            $attributes = array();
            $name = strtolower((string)$obj->getName());
     
            $text = trim((string)$obj);
            if( strlen($text) <= 0 ) {
                $text = NULL;
            }
     
            // get info for all namespaces
            if(is_object($obj)) {
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
                        $children[$childName][] = xmlObjToArr($child);
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
