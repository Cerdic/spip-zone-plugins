<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Teste la validite d'une url d'un depot de paquets
 *
 * @param string $url
 * @return boolean
 */

// $url	=> url du fichier xml de description du depot
function svp_verifier_adresse_depot($url){
	include_spip('inc/distant');
	return (!$xml = recuperer_page($url)) ? false : true;
}


// aplatit plusieurs cles d'un arbre xml dans un tableau
// effectue un trim() au passage
function svp_xml_aplatit_multiple($array, $arbre){
	include_spip('inc/xml');

	$a = array();
	// array('uri','archive'=>'zip',...)
	foreach ($array as $i=>$n){
		if (is_string($i)) $cle = $i;
		else $cle = $n;
		$a[$n] = trim(spip_xml_aplatit($arbre[$cle]));
	}
	return $a;	
}

?>
