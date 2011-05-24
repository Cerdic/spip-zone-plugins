<?php
/**
 * plugin FreeRadio
 * Radio Web
 *
 * Auteurs :
 * Franck Ruzzin
 * le 10/05/2011
 *
 **/
 
function balise_CHEMINPLUGINFREERADIO($p) {
	$searchFile = interprete_argument_balise (1, $p);
	$p->code = "get_cheminPluginFreeRadio($searchFile)";
	return $p;
}

function get_cheminPluginFreeRadio($file="") {
	return find_in_path(_DIR_PLUGIN_FREERADIO . $file);	
}

/*
* retourne la valeur du paramètre $c contenu dans $url
*/
function get_param($url, $c) {
	// eclater
	$url = preg_split(',[?]|&amp;|&,', $url);
	$ret="";
	foreach ($url as $n => $val) {
		if (preg_match(',^'.preg_quote($c,',').'(=.*)?$,', urldecode($val))) {
			$ret = explode("=", $url[$n]);
			$ret = $ret[1];
		}
	}
	return $ret;
}


?>
