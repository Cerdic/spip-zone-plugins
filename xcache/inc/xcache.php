<?php

//
// Cache function W()
// (c) Fil 2009 - Double-licensed under the GNU/LGPL and MIT licenses
// http://zzz.rezo.net/-SPIP-
// $ttl = time to live
// $vars = other variables that could change the result
// (the function's variables are automatically taken into account)
//
// Usage: require_once 'xcache.php';
// In any cacheable function add at top: if (null!==$W=W())return$W;

if (!function_exists('W')) {

# xcache ?
if (function_exists('xcache_set')) {
	function W($vars=null, $ttl=3600) {
		$trace = debug_backtrace();
		$trace = $trace[1];
		$key = __FILE__ . md5(
			$trace['function']
			.serialize($trace['args'])
			.serialize($vars)
		);
		if (!xcache_isset($key)) {
			xcache_set($key, null, $ttl);
			$r = call_user_func_array($trace['function'], $trace['args']);
			xcache_set($key, $r, $ttl);
			return $r;
		}
		return xcache_get($key);
	}
}
# elementary compatibility
else {
	function W(){return null;}
}

}

?>
