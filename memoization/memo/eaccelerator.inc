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

# http://bart.eaccelerator.net/doc/phpdoc/eAccelerator/_shared_memory_php.html

@define('_CACHE_NAMESPACE', $_SERVER['HTTP_HOST'].':');

function cache_get($key) {
	return ($v = eaccelerator_get(_CACHE_NAMESPACE.$key))
		? @unserialize($v)
		: null;
}

function cache_set($key, $value, $ttl = null) {
	$value = serialize($value);
	return is_null($ttl)
		? eaccelerator_put(_CACHE_NAMESPACE.$key, $value)
		: eaccelerator_put(_CACHE_NAMESPACE.$key, $value, $ttl);
}

function cache_isset($key) {
	return cache_get($key) !== null;
}

function cache_unset($key) {
	return eaccelerator_rm(_CACHE_NAMESPACE.$key);
}

function cache_inc($key, $value=null, $ttl=null) {
	eaccelerator_lock($key);
	$value = isset($value) ? intval($value) : 1;
	$value += intval(cache_get($key));
	cache_set($key, $value, $ttl);
	eaccelerator_unlock($key);
	return $value;
}

function cache_dec($key, $value=null, $ttl=null) {
	$value = isset($value) ? intval($value) : 1;
	return cache_inc($key, -$value, $ttl);
}

function cache_lock($key) {
	eaccelerator_lock(_CACHE_NAMESPACE.$key);
	return true;
}

function cache_unlock($key) {
	eaccelerator_unlock(_CACHE_NAMESPACE.$key);
	return true;
}

?>
