<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

function cfgname_to_inputname($key){
	if (strpos($key,"/")===false) return $key;
	$key = explode('/',$key);
	$main = array_shift($key);
	$key = "[".implode("][",$key)."]";
	return $main.$key;
}

function cfgname_to_id($key){
	return preg_replace(",\W,","_",$key);
}
