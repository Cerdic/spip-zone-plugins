<?php

# l'API de cache_set etc est exactement celle de xcache
# a ceci pres qu'on se reserve un namespace specifique au site
# afin de ne pas se melanger sur un serveur mutualise
#
# je laisse le soin a qui voudra de refaire ca en mode objet

/*
mixed cache_get(string name)
bool  cache_set(string name, mixed value [, int ttl])
bool  cache_isset(string name)
bool  cache_unset(string name)
bool  cache_unset_by_prefix(string prefix)
int   cache_inc(string name [, int value [, int ttl]])
int   cache_dec(string name [, int value [, int ttl]])
*/

@define('_CACHE_NAMESPACE', $_SERVER['HTTP_HOST'].':');

function cache_get() {
	$a = func_get_args();
	$a[0] = _CACHE_NAMESPACE.$a[0];
	return ($v = call_user_func_array('eaccelerator_get', $a))
		? @unserialize($v)
		: null;
}

function cache_set() {
	$a = func_get_args();
	$a[0] = _CACHE_NAMESPACE.$a[0];
	$a[1] = serialize($a[1]);
	return call_user_func_array('eaccelerator_put', $a);
}

function cache_isset() {
	$a = func_get_args();
	return cache_get($a[0]) !== null;
}

function cache_unset() {
	$a = func_get_args();
	$a[0] = _CACHE_NAMESPACE.$a[0];
	return call_user_func_array('eaccelerator_rm', $a);
}

function cache_unset_by_prefix() {
	$a = func_get_args();
	return false; # on sait pas faire
}

function cache_inc() {
	$a = func_get_args();
	$a[1] = intval(cache_get($a[0]))+1;
	return call_user_func_array('cache_set', $a);
}

function cache_dec() {
	$a = func_get_args();
	$a[1] = intval(cache_get($a[0]))-1;
	return call_user_func_array('cache_set', $a);
}

?>
