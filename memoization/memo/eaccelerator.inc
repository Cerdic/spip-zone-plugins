<?php

/*
mixed cache_get(string key)
bool  cache_set(string key, mixed value [, int ttl])
bool  cache_isset(string key)
bool  cache_unset(string key)
int   cache_inc(string key)
int   cache_dec(string key)
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

function cache_inc($key) {
	while (!eaccelerator_lock($key)){ sleep(1); }
	$value = 1 + intval(cache_get($key));
	cache_set($key, $value);
	eaccelerator_unlock($key);
	return $value;
}

function cache_dec($key) {
	while (!eaccelerator_lock($key)){ sleep(1); }
	$value = -1 + intval(cache_get($key));
	cache_set($key, $value);
	eaccelerator_unlock($key);
	return $value;
}

function cache_lock($key) {
	return eaccelerator_lock(_CACHE_NAMESPACE.$key);
}

function cache_unlock($key) {
	return eaccelerator_unlock(_CACHE_NAMESPACE.$key);
}

?>
