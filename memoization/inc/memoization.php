<?php

# xcache ?
$cfg = @unserialize($GLOBALS['meta']['memoization']);

function memoization_methode ($methode=null) {
	if (!$methode) {
		$methodes = array('apc', 'xcache', 'eaccelerator', 'filecache');
		while (!memoization_methode($methode = array_shift($methodes))){};
		return $methode;
	}

	switch($methode) {
		case 'apc':
			return function_exists('apc_add');
		case 'xcache':
			return function_exists('xcache_set');
		case 'memcache':
			return function_exists('memcache_set');
		case 'eaccelerator':
			return function_exists('eaccelerator_put');
		case 'filecache':
		case 'nocache':
			return true;
	}
}

if (!$cfg['methode']) {
	$cfg['methode'] = memoization_methode();
}
if ($cfg['methode']
AND memoization_methode($cfg['methode'])) {
	@define('_MEMOIZE', $cfg['methode']);
	require_once dirname(dirname(__FILE__)).'/memo/'.$cfg['methode'].'.inc';
} else {
	@define('_MEMOIZE', 'nocache');
	require_once dirname(dirname(__FILE__)).'/memo/'.'nocache'.'.inc';
}

//
// Cache a function's result cache_me()
// (c) Fil 2009 - Double-licensed under the GNU/LGPL and MIT licenses
// http://zzz.rezo.net/-SPIP-
// $ttl = time to live
// $vars = other variables that could change the result
// (the function's variables are automatically taken into account)
//
// Usage: include_spip('inc/memoization');
// In any cacheable function add at top:
// if(!is_null($c=cache_me())) return$c;
if (!function_exists('debug_backtrace')) {
	function cache_me() {return;}
} else {
	function cache_me($vars=null, $ttl=3600) {
		$trace = debug_backtrace();
		$trace = $trace[1];
		if (isset($trace['object']))
			$fun = array($trace['object'], $trace['function']);
		else
			$fun = $trace['function'];
		$key = md5(
			$fun
			.serialize($trace['args'])
			.serialize($vars)
		);
		if (!cache_isset($key)) {
			cache_set($key, null, $ttl);
			$r = call_user_func_array($fun, $trace['args']);
			cache_set($key, $r, $ttl);
			return $r;
		}
		return cache_get($key);
	}
}

// outil pour memcache
// Attention, vérifier que le port 11211 est celui utilisé par memcached
// Sinon, adapter ce code selon la configuration de votre serveur
function cfg_memcache_servers() {
	$cfg = @unserialize($GLOBALS['meta']['memoization']);
	if (!$cfg = $cfg['memcache_servers'])
		$cfg = 'localhost:11211';
	preg_match_all('/[a-z0-9._-]*(?::\d+)/', $cfg, $s, PREG_PATTERN_ORDER);
	return $s[0];
}

?>
