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

# http://php.net/memcache

@define('_CACHE_NAMESPACE', $_SERVER['HTTP_HOST'].':');
@define('_LOCK_MAX', 30); # max lock time (seconds)

unset($GLOBALS['memcache']);
foreach(cfg_memcache_servers() as $server) {
	list($host, $port) = explode(':', $server);
	var_dump($host, $port);
	if (!$GLOBALS['memcache'])
		$GLOBALS['memcache'] = @memcache_connect($host,$port);
	else
		@memcache_addserver($GLOBALS['memcache'], $host, $port);
}

function cache_get($key) {
	$a = memcache_get($GLOBALS['memcache'], _CACHE_NAMESPACE.$key);
	// maybe_unserialize
	if (is_array($a))
		return @unserialize($a[0]);
	else
		return $a;
}

function cache_set($key, $value, $ttl=null) {
	// maybe_serialize
	if (is_object($value) OR is_array($value) OR is_null($value))
		$value = array(serialize($value));
	$flag = strlen($value) > 16000 ? MEMCACHE_COMPRESSED : 0;
	return is_null($ttl)
		? memcache_set($GLOBALS['memcache'], _CACHE_NAMESPACE.$key, $value)
		: memcache_set($GLOBALS['memcache'], _CACHE_NAMESPACE.$key, $value, $flag, $ttl);
}

function cache_isset($key) {
	return memcache_get($GLOBALS['memcache'], _CACHE_NAMESPACE.$key) !== FALSE;
}

function cache_unset($key) {
	return memcache_set($GLOBALS['memcache'], _CACHE_NAMESPACE.$key, FALSE);
}

function cache_inc($key, $value=null, $ttl=null) {
	$a = func_get_args();
	$a[0] = _CACHE_NAMESPACE.$key;
	array_unshift($a, $GLOBALS['memcache']);

	// set if not exists
	$b = $a; $b[2] = 0; call_user_func_array('memcache_add', $b);

	return call_user_func_array('memcache_increment', $a);
}

function cache_dec($key, $value=null, $ttl=null) {
	$a = func_get_args();
	$a[0] = _CACHE_NAMESPACE.$key;
	array_unshift($a, $GLOBALS['memcache']);

	// set if not exists
	$b = $a; $b[2] = 0; call_user_func_array('memcache_add', $b);

	return call_user_func_array('memcache_decrement', $a);
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
