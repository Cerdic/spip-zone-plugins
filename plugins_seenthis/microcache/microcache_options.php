<?php

function microcache_key($id, $fond) {
	include_spip('memoization_options');
	if (!function_exists('cache_set')) return false;
	return $fond.'-'. (is_numeric($id) ? $id : md5($id));
}

function supprimer_microcache($id, $fond) {
	if ($key = microcache_key($id, $fond))
		return cache_del($key);
}

function microcache($id, $fond, $calcul=false) {
	$key = microcache_key($id, $fond);
	if (!$key
	OR $calcul
	OR in_array($_GET['var_mode'], array('recalcul', 'debug'))
	OR !($contenu = cache_get($key))
	) {
		$contenu = recuperer_fond($fond, array('id'=>$id));
		if ($key
		AND $_GET['var_mode'] != 'inclure'
		AND !$_POST
		AND !(isset($GLOBALS['var_nocache']) AND $GLOBALS['var_nocache'])
		AND !defined('spip_interdire_cache')
		) {
			cache_set($key, $contenu, $ttl = 7*24*3600);
		}
	}
	return $contenu;
}


?>