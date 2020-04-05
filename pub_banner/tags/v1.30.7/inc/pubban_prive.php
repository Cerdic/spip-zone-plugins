<?php

// ---------------------
// FONCTIONS VERIFICATIONS/TRANSFORMATION
// ---------------------

function pubban_transformer_nombre($nombre){
	$nombre = str_replace(' ', '', $nombre);
	$nombre = str_replace(',', '.', $nombre);
	return trim($nombre);
}

function pubban_transformer_titre_id($str){
	$str = str_replace(' ', '_', utf8_encode($str));
	return trim($str);
}

function pubban_poubelle_pleine(){
	include_spip('base/abstract_sql');
	$trash = sql_countsel('spip_publicites', "statut='5poubelle'", '', '', '', '') + sql_countsel('spip_bannieres', "statut='5poubelle'", '', '', '', '');
	return $trash;
}

// ---------------------
// FONCTIONS AFFICHAGE
// ---------------------

/**
 * Recuperateur d'extension
 * Recupere la chaine apres son dernier point.
 * Si cette chaine est plus longue qu'attendu pour une extension (par defaut 5 caracteres),
 * retourne FALSE. Sinon, retourne l'extension, SANS le point.
 * @return boolean/string FALSE | extension sans point
 * @param string $file Le fichier ou l'adresse dont on cherche l'extension
 * @param numeric $car Le nombre de caracteres a verifier (longueur de l'extension) | default : 5 caracteres
 */
function pubban_extension($file, $car='5') {
	if(!is_string($file) OR !strlen($file)) return false;
	$ext = substr(strrchr($file, '.'), 1);
	if(strlen($ext) <= $car) return $ext;
	return false;
}

/**
 * Valideur d'url
 */
function pubban_UrlOK($url) { 
	if( substr_count($url, 'localhost') ) return true;
//	return eregi("^http://[_A-Z0-9-]+\.[_A-Z0-9-]+[.A-Z0-9-]*(/~|/?)[/_.A-Z0-9#?&=+-]*$",$url); 
	return preg_match("/^[http|https]+[:\/\/]+[A-Za-z0-9\-_]+\\.+[A-Za-z0-9\.\/%&=\?\-_]+$/i",$url);
} 

function pubban_url_reponse($url) { 
    $url = @parse_url($url);
    if (!$url) return false;
    $url = array_map('trim', $url);
    $url['port'] = (!isset($url['port'])) ? 80 : (int)$url['port'];
    $path = (isset($url['path'])) ? $url['path'] : '';
    if ($path == '') $path = '/';
    $path .= (isset($url['query'])) ? "?$url[query]" : '';
    if (isset($url['host']) AND $url['host'] != gethostbyname($url['host'])){
        if (PHP_VERSION >= 5)
            $headers = get_headers("$url[scheme]://$url[host]:$url[port]$path");
        else {
            $fp = @fsockopen($url['host'], $url['port'], $errno, $errstr, 30);
            if (!$fp) return false;
            fputs($fp, "HEAD $path HTTP/1.1\r\nHost: $url[host]\r\n\r\n");
            $headers = fread($fp, 4096);
            fclose($fp);
        }
        $headers = (is_array($headers)) ? implode("\n", $headers) : $headers;
        return (bool)preg_match('#^HTTP/.*\s+[(200|301|302)]+\s#i', $headers);
    }
    return false;
}

?>