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

function cache_get() {
}

function cache_set() {
	return false;
}

function cache_isset() {
	return false;
}

function cache_unset() {
	return false;
}

function cache_unset_by_prefix() {
	return false;
}

function cache_inc() {
	return 1;
}

function cache_dec() {
	return -1;
}

?>
