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

# http://xcache.lighttpd.net/wiki/XcacheApi

@define('_CACHE_NAMESPACE', $_SERVER['HTTP_HOST'].':');

function cache_get($key) {
	return xcache_get(_CACHE_NAMESPACE.$key);
}

function cache_set($key, $value, $ttl=null) {
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

function cache_inc($key) {
	return xcache_inc(_CACHE_NAMESPACE.$key);
}

function cache_dec($key) {
	return xcache_dec(_CACHE_NAMESPACE.$key);
}

function cache_lock($key) {
	return cache_inc('lock::'.$key)===1;
}

function cache_unlock($key) {
	return cache_unset('lock::'.$key);
}

?>
