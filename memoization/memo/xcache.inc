<?php

/*
mixed cache_get(string key)
bool  cache_set(string key, mixed value [, int ttl])
bool  cache_isset(string key)
bool  cache_unset(string key)
int   cache_inc(string key[, int value[, int ttl] ])
int   cache_dec(string key[, int value[, int ttl] ])
bool  cache_lock(string key)
bool  cache_unlock(string key)
*/

# http://xcache.lighttpd.net/wiki/XcacheApi

@define('_CACHE_NAMESPACE', $_SERVER['HTTP_HOST'].':');
@define('_LOCK_MAX', 30); # max lock time (seconds)

function cache_get($key) {
	$a = xcache_get(_CACHE_NAMESPACE.$key);
	// maybe_unserialize
	if (is_array($a))
		return @unserialize($a[0]);
	else
		return $a;
}

function cache_set($key, $value, $ttl=null) {
	// maybe_serialize
	if (is_object($value) OR is_array($value))
		$value = array(serialize($value));
	return is_null($ttl)
		? xcache_set(_CACHE_NAMESPACE.$key, $value)
		: xcache_set(_CACHE_NAMESPACE.$key, $value, $ttl);
}

function cache_isset($key) {
	return xcache_isset(_CACHE_NAMESPACE.$key);
}

function cache_unset($key) {
	return xcache_unset(_CACHE_NAMESPACE.$key);
}

function cache_inc($key, $value=null, $ttl=null) {
	$a = func_get_args();
	$a[0] = _CACHE_NAMESPACE.$key;
	return call_user_func_array('xcache_inc', $a);
}

function cache_dec($key, $value=null, $ttl=null) {
	$a = func_get_args();
	$a[0] = _CACHE_NAMESPACE.$key;
	return call_user_func_array('xcache_dec', $a);
}

function cache_lock($key, /* private */ $unlock = false) {
	static $locks = array();

	/* unlock */
	if ($unlock) {
		unset($locks[$key]);
		cache_unset('lock::'.$key);
	}
	/* lock */
	else {
		if (!isset($locks[$key])) {
			while (cache_isset('lock::'.$key)
			OR cache_inc('lock::'.$key, 1, _LOCK_MAX) !== 1)
				sleep (1);
			register_shutdown_function('cache_lock', $key, true);
		}
	}
}

function cache_unlock($key) {
	cache_lock($key, true);
}

?>
