<?php
/**
 * Plugin manuel du site
 * Fichier de fonctions spécifique au modele modeles/faq.html
 */

/**
 * Transforme une chaine p1:v1;p2:v2 en tableau associatif
 * 
 * @param string $params
 * 		Les paramètres sous forme de chaine
 * @return array $tablo
 * 		Les paramètres sous forme de tableau
 */
function manuelsite_params_to_array($params="") {
	$tablo = array();
	if($params != "") {
		$params = preg_replace( '/^(.*)$/','"${1}"',$params) ;
		$params = str_replace(';','","',$params);
		$params= str_replace(':','"=>"',$params);
		eval("\$tablo=array($params);");
	}
	return $tablo;	
}
?>