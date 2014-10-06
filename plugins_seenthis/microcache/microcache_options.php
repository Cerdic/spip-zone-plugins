<?php

// FALSE pour new style = memoization, TRUE pour old style = fichier dans local/
if (FALSE) {


function supprimer_microcache($id, $fond) {
	include_spip("inc/microcache");
	_supprimer_microcache($id, $fond);
}

function microcache($id, $fond, $calcul=false) {
	include_spip("inc/microcache");
	return _microcache($id, $fond, $calcul);
}

function esi_microcache($id, $fond) {
	include_spip("inc/microcache");
	return _esi_microcache($id, $fond);
}



} else {



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
		) {
			cache_set($key, $contenu, $ttl = 7*24*3600);
		}
	}
	return $contenu;
}



}


?>