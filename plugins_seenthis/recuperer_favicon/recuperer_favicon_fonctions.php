<?php


################################################################################
#	http://www.google.com/s2/favicons?domain=www.flip-zone.com
################################################################################


function recuperer_favicon($url) {

	$url = parse_url($url, PHP_URL_HOST);
	$racine = ereg_replace("^www\.", "", $url);
	$racine = str_replace(".", "-", $racine) . "-";

	$destination = sous_repertoire(_DIR_VAR, 'cache-favicon') .$racine.md5($url).".png";
	if (!file_exists($destination)){		
		$copie = copie_locale("http://www.google.com/s2/favicons?domain=$url");
		copy($copie, $destination);
	}
	
	$destination = inserer_attribut($destination, "alt", "favicon $url");
	return $destination;		
		
	
}


?>