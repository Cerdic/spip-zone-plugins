<?php
$p=explode(basename(_DIR_PLUGINS)."/",str_replace('\\','/',realpath(dirname(__FILE__))));
define('_DIR_PLUGIN_COMARQUAGE',(_DIR_PLUGINS.end($p)).'/');

function comarquage_liens_externes($texte){
	if (strpos($texte,"<LienExterne")!==FALSE){
		$texte = preg_replace(",<LienExterne[^>]*URL=['\"]([^'\"]*)['\"][^>]*>(.*)</LienExterne>,","<a href='\\1'>\\2</a>",$texte);
	}
	return $texte;
}

function comarquage_cache_xml($xmlname){
	$filename = $xmlname;
	if (file_exists($filename)) return $filename;
	$filename = basename($xmlname,'.xml').".xml";
	# _DIR_CACHE._DIR_CACHE_COMARQUAGE_XML.$filename
	if (file_exists(_DIR_PLUGIN_COMARQUAGE."xml/$filename"))
		return _DIR_PLUGIN_COMARQUAGE."xml/$filename";

	// recuperer le xml distant et le mettre en cache

	return $xmlname;
}
?>