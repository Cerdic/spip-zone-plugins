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


@define('_CACHE_NAMESPACE', $_SERVER['HTTP_HOST'].':');

function cache_get($key) {
	$a = apc_fetch(_CACHE_NAMESPACE.$key);
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
		? apc_store(_CACHE_NAMESPACE.$key, $value)
		: apc_store(_CACHE_NAMESPACE.$key, $value, $ttl);
}

function cache_isset($key) {
	return apc_exists(_CACHE_NAMESPACE.$key);
}

function cache_unset($key) {
	return apc_delete(_CACHE_NAMESPACE.$key);
}

function cache_inc($key, $value=null, $ttl=null) {
	$value = isset($value) ? intval($value) : 1;
	if (cache_lock($key)) {
		$value = isset($value) ? intval($value) : 1;
		$value += intval(cache_get($key));
		cache_set($key, $value, $ttl);
		cache_unlock($key);
		return $value;
	}
	return false;
}

function cache_dec($key, $value=null, $ttl=null) {
	$value = isset($value) ? intval($value) : 1;
	return cache_inc($key, -$value, $ttl);
}

function key_for_lock($str) {
    return _CACHE_NAMESPACE.md5($str."locked");
}

function is_locked($key) {
    return apc_exists($key);
}

function cache_lock($key) {
    if (is_locked($key_for_lock = key_for_lock($key))) {
        return false;
    }
    return apc_add($key_for_lock, true);
}

function cache_unlock($key) {
    if (!is_locked($key_for_lock = key_for_lock($key))) {
        return false;
    }
    return apc_delete($key_for_lock);
}

?>
