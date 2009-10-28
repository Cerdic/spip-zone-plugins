<?php

# xcache ?
if (function_exists('xcache_set'))
	require_once dirname(__FILE__).'/'.'xcache.inc';
else
	require_once dirname(__FILE__).'/'.'filecache.inc';


//
// Cache a function's result cache_me()
// (c) Fil 2009 - Double-licensed under the GNU/LGPL and MIT licenses
// http://zzz.rezo.net/-SPIP-
// $ttl = time to live
// $vars = other variables that could change the result
// (the function's variables are automatically taken into account)
//
// Usage: require_once 'xcache.php';
// In any cacheable function add at top:
// if(!is_null($c=cache_me())) return$c;
if (!function_exists('debug_backtrace')) {
	function cache_me() {return;}
} else {
	function cache_me($vars=null, $ttl=3600) {
		$trace = debug_backtrace();
		$trace = $trace[1];
		$key = md5(
			$trace['function']
			.serialize($trace['args'])
			.serialize($vars)
		);
		if (!cache_isset($key)) {
			cache_set($key, null, $ttl);
			$r = call_user_func_array($trace['function'], $trace['args']);
			cache_set($key, $r, $ttl);
			return $r;
		}
		return cache_get($key);
	}
}

?>
