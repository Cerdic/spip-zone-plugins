<?php

/*
mixed cache_get(string key)
bool  cache_set(string key, mixed value [, int ttl])
bool  cache_isset(string key)
bool  cache_unset(string key)
int   cache_inc(string key[, int value[, int ttl] ])
int   cache_dec(string key[, int value[, int ttl] ])
null  cache_lock(string key)
null  cache_unlock(string key)
*/

if (!defined('_DIR_TMP')) define('_DIR_TMP', 'tmp/');
if (!defined('_DIR_FILECACHE')) define('_DIR_FILECACHE', 'cache/');

// file named tmp/cache/ab/cd
// that's a maximum of 16^4 files in 256 directories
function cache_filename($u) {
	$u = md5($u);
	$d = substr($u,0,2); 
	$u = substr($u,2,2);

	# SPIP
	if (function_exists('sous_repertoire')) {
		return
			sous_repertoire(
				sous_repertoire(_DIR_TMP, _DIR_FILECACHE),
				$d
			)
			. $u;
	}

	# Normal PHP
	if (!is_dir(_DIR_TMP_XCACHE))
		@mkdir(_DIR_TMP_XCACHE);
	if (!is_dir($sub = _DIR_TMP_XCACHE.$d))
		@mkdir($sub);
	return $sub.'/'.$u;
}

function cache_get($key) {
	if ($c = @file_get_contents(cache_filename($key))
	AND $r = unserialize($c)
	AND $r[1] === $key
	AND $r[2] >= time())
		return $r[0];
}

function cache_set($key, $value, $ttl=null) {
	$r = array($value);
	$r[1] = $key;
	$r[2] = isset($ttl)
		? time() + intval($ttl)
		: time() + 365*24*3600;

	return (
		$f = cache_filename($key)
		AND $n = @tempnam(dirname($f), 'xc')
		AND $h = @fopen($n, 'w')
		AND @fwrite($h, serialize($r))
		AND @fclose($h)
		AND @rename($n, $f)
	);
}

function cache_isset($key) {
	if ($c = @file_get_contents(cache_filename($key))
	AND $r = @unserialize($c)
	AND $r[1] == $key
	AND $r[2] <= time())
		return true;
	else
		return false;
}

function cache_unset($key) {
	return (
		$c = @file_get_contents($f = cache_filename($key))
		AND $s = supprimer_fichier($f)
		AND $r = @unserialize($c)
		AND $r[1] == $key
		AND $r[2] <= time()
	);
}

function cache_inc($key, $value=null, $ttl=null) {
	cache_lock($key);
	$value = (isset($value) ? intval($value) : 1) + intval(cache_get($key));
	cache_set($key, $value);
	cache_unlock($key);
	return $value;
}

function cache_dec($key, $value=null, $ttl=null) {
	$value = isset($value) ? intval($value) : 1;
	return cache_inc($key, -$value, $ttl);
}

function cache_lock($key, /* private */ $unlock=false) {
	static $locks = array();
	$f = cache_filename($key);

	/* unlock */
	if ($unlock) {
		if (isset($locks[$f])
		AND is_resource($locks[$f])) {
			@flock($locks[$f], LOCK_UN);
			@fclose($locks[$f]);
			unset($locks[$f]);
		}
	}
	/* lock */
	else {
		if (!isset($locks[$f])) {
			$locks[$f] = @fopen($f, 'a');
			@flock($locks[$f], LOCK_EX);
			return true;
		}
	}
}

function cache_unlock($key) {
	cache_lock($key, true);
}

?>
