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

@define('_DIR_TMP_XCACHE', (defined('_DIR_TMP') ? _DIR_TMP : 'tmp/').'xcache/');

function cache_filename($u) {
	if (!is_dir(_DIR_TMP_XCACHE))
		@mkdir(_DIR_TMP_XCACHE);
	return _DIR_TMP_XCACHE.md5($u);
}
function cache_delete_dir($dir) {
	if ($d = @opendir($dir))
		while (($f = readdir($d)) !== false)
			if (is_file($f))
				unlink($f);
}

function cache_get() {
	$a = func_get_args();
	if ($c = @file_get_contents(cache_filename($a[0]))
	AND $r = unserialize($c)
	AND $r[1] <= time())
		return $r[0];
}

function cache_set() {
	$a = func_get_args();
	$r = array($a[1]);
	$r[1] = isset($a[2])
		? time() + intval($a[2])
		: time() + 365*24*3600;
	return (
		$f = cache_filename($a[0])
		AND $n = @tempnam(dirname($f), 'xc')
		AND $h = @fopen($n, 'w')
		AND @fwrite($h, serialize($r))
		AND @fclose($h)
		AND @rename($n, $f)
	);
}

function cache_isset() {
	$a = func_get_args();
	if ($c = file_get_contents(cache_filename($a[0]))
	AND $r = unserialize($c)
	AND $r[1] <= time())
		return true;
	else
		return false;
}

function cache_unset() {
	$a = func_get_args();
	return (
		$c = file_get_contents($f = cache_filename($a[0]))
		AND $s = supprimer_fichier($f)
		AND $r = unserialize($c)
		AND $r[1] <= time()
	);
}

function cache_unset_by_prefix() {
	# ici on efface tout aveuglement
	cache_delete_dir(_DIR_TMP_XCACHE);
	return true;
}

function cache_inc() {
	$a = func_get_args();
	$a[1] = intval(cache_get($a[0]))+1;
	return call_user_func_array('xcache_set', $a);
}

function cache_dec() {
	$a = func_get_args();
	$a[1] = intval(cache_get($a[0]))-1;
	return call_user_func_array('xcache_set', $a);
}

?>
