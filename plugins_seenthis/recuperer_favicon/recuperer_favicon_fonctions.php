<?php


################################################################################
#	http://www.google.com/s2/favicons?domain=www.flip-zone.com
################################################################################


function recuperer_favicon($url) {

	$url = parse_url($url, PHP_URL_HOST);
	$racine = preg_replace("/^www\./", "", $url);
	$racine = preg_replace("/[^a-z0-9]+/", "-", $racine) . "-";

	$destination = sous_repertoire(_DIR_VAR, 'cache-favicon') .$racine.md5($url).".png";

	if (!file_exists($destination)
	AND $copie = copie_locale("http://www.google.com/s2/favicons?domain=$url")
	) {
		rename($copie, $destination);
	}
	
	$destination = inserer_attribut($destination, "alt", "favicon $url");
	return $destination;
}

?>
