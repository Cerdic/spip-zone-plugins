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
	if (!$GLOBALS['memcache'])
		$GLOBALS['memcache'] = @memcache_connect($host,$port);
	else
		@memcache_add_server($GLOBALS['memcache'], $host, $port);
}

function cache_get($key) {
	if (!$GLOBALS['memcache']) return null;

	$a = memcache_get($GLOBALS['memcache'], _CACHE_NAMESPACE.$key);
	// maybe_unserialize
	if (is_array($a))
		return @unserialize($a[0]);
	else
		return $a;
}

function cache_set($key, $value, $ttl=null) {
	if (!$GLOBALS['memcache']) return null;

	// maybe_serialize
	if (is_object($value) OR is_array($value) OR is_null($value))
		$value = array(serialize($value));
	$flag = strlen($value) > 16000 ? MEMCACHE_COMPRESSED : 0;
	return is_null($ttl)
		? memcache_set($GLOBALS['memcache'], _CACHE_NAMESPACE.$key, $value)
		: memcache_set($GLOBALS['memcache'], _CACHE_NAMESPACE.$key, $value, $flag, $ttl);
}

function cache_isset($key) {
	if (!$GLOBALS['memcache']) return false;

	return memcache_get($GLOBALS['memcache'], _CACHE_NAMESPACE.$key) !== FALSE;
}

function cache_unset($key) {
	if (!$GLOBALS['memcache']) return false;

	return memcache_delete($GLOBALS['memcache'], _CACHE_NAMESPACE.$key);
}

function cache_inc($key, $value=null, $ttl=null) {
	if (!$GLOBALS['memcache']) return null;

	// set if not exists
	memcache_add($GLOBALS['memcache'], _CACHE_NAMESPACE.$key, 0, $ttl);

	if (!isset($value))
		$value = 1;
	return memcache_increment($GLOBALS['memcache'], _CACHE_NAMESPACE.$key, $value);
}

function cache_dec($key, $value=null, $ttl=null) {
	if (!$GLOBALS['memcache']) return null;

	// set if not exists
	memcache_add($GLOBALS['memcache'], _CACHE_NAMESPACE.$key, 0, $ttl);

	if (!isset($value))
		$value = 1;
	return memcache_decrement($GLOBALS['memcache'], _CACHE_NAMESPACE.$key, $value);
}

function cache_lock($key, /* private */ $unlock = false) {
	if (!$GLOBALS['memcache']) return null;

	static $locks = array();

	/* unlock */
	if ($unlock) {
		unset($locks[$key]);
		cache_unset('lock::'.$key);
	}
	/* lock */
	else {
		# si on l'a deja, ok
		if (isset($locks[$key]))
			return true;

		# sinon s'il existe, on attend qu'il se libere
		$wait = 0;
		while (cache_isset('lock::'.$key)) {
			if ($wait++ < _LOCK_MAX)
				sleep(1);
			else
				return false;
		}

		# ... puis on le pose
		# attention cache_inc() renvoie parfois false (??)
		if (cache_inc('lock::'.$key, 1, _LOCK_MAX) <= 1) {
			cache_set('lock::'.$key, 2, _LOCK_MAX);
			$locks[$key] = true;
			register_shutdown_function('cache_unlock', $key);
			return true;
		}

		return false;
	}
}

function cache_unlock($key) {
	if (!$GLOBALS['memcache']) return null;

	cache_lock($key, true);
}

?>
