<?php

/*
mixed cache_get(string key)
bool  cache_set(string key, mixed value [, int ttl])
bool  cache_isset(string key)
bool  cache_unset(string key)
int   cache_inc(string key [, int value [, int ttl]])
int   cache_dec(string key [, int value [, int ttl]])
bool  cache_lock(string key)
bool  cache_unlock(string key)
*/

function cache_get($key) {
}

function cache_set($key, $value, $ttl=null) {
	return false;
}

function cache_isset($key) {
	return false;
}

function cache_unset($key) {
	return false;
}

function cache_inc($key) {
	return 1;
}

function cache_dec($key) {
	return -1;
}

function cache_lock($key) {
	return false;
}

function cache_unlock($key) {
	return false;
}

?>
