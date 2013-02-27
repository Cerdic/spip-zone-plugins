<?php

/* prototype de backend */
class MCacheBackend {
	function get($key) {}
	function set($key, $value, $ttl=null) {}
	function exists($key) {}
	function del($key) {}
	function inc($key, $value=null, $ttl=null) {}
	function dec($key, $value=null, $ttl=null) {}
	function lock($key, /* private */ $unlock = false) {}
	function unlock($key) {}
	function init($params = null) {}
}

/* objet MCache */
class MCache {
	var $methode;
	var $backend;

	function memory() {
		return !in_array($this->methode, array('filecache', 'nocache'));
	}

	function MCache($methode=null, $params=array()) {
		// autodetect
		$this->methode = $methode ? $methode : $this->methode();
		$f = find_in_path($this->methode.'.inc',"memo/");
		require_once $f;
		$obj = 'MCacheBackend_'.$this->methode;
		$this->backend = new $obj;
		$this->backend->init($params);
	}

	static function methode($methode = null) {
		if (!$methode) {
			$methodes = array('apc', 'xcache', 'eaccelerator', 'filecache', 'nocache');
			while (!MCache::methode($methode = array_shift($methodes))){};
			return $methode;
		}

		switch($methode) {
			case 'apc':
				return function_exists('apc_exists');
			case 'xcache':
				if (!function_exists('xcache_set'))
					return false;
				@xcache_set('xcache_autodetect',1234);
				return @xcache_get('xcache_autodetect')==1234;
			case 'memcache':
				return function_exists('memcache_set');
			case 'eaccelerator':
				return function_exists('eaccelerator_put');
			case 'filecache':
			case 'nocache':
				return true;
		}
	}

	/* mixed */
	function get($key) {
		return $this->backend->get($key);
	}
	
	/* bool */
	function set($key, $value, $ttl=null) {
		return $this->backend->set($key, $value, $ttl);
	}
	
	/* bool */
	function exists($key) {
		return $this->backend->exists($key);
	}
	
	/* bool */
	function del($key) {
		return $this->backend->del($key);
	}
	
	/* int */
	function inc($key, $value=null, $ttl=null) {
		return $this->backend->inc($key, $value, $ttl);
	}
	
	/* int */
	function dec($key, $value=null, $ttl=null) {
		return $this->backend->dec($key, $value, $ttl);
	}
	
	/* null */
	function lock($key) {
		return $this->backend->lock($key);
	}
	
	/* null */
	function unlock($key) {
		return $this->backend->unlock($key);
	}

	/* mixed */
	function size() {
		if (method_exists($this->backend, 'size'))
			return $this->backend->size();
	}
}


if (!isset($GLOBALS['meta']['cache_namespace'])){
	include_spip('inc/acces');
	ecrire_meta('cache_namespace', dechex(crc32($_SERVER["DOCUMENT_ROOT"] . $_SERVER["SERVER_SIGNATURE"] . creer_uniqid())), 'non');
}
if (!defined('_CACHE_NAMESPACE'))
	define('_CACHE_NAMESPACE', $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].':'.$GLOBALS['meta']['cache_namespace'].':');

global $Memoization;

$cfg = @unserialize($GLOBALS['meta']['memoization']);
$Memoization = new MCache(preg_replace(",\W,","",$cfg['methode']));


/* mode procedural */

/* mixed */
function cache_get($key) {
	global $Memoization;
	return $Memoization->get($key);
}

/* bool */
function cache_set($key, $value, $ttl=null) {
	global $Memoization;
	return $Memoization->set($key, $value, $ttl);
}

/* bool */
function cache_exists($key) {
	global $Memoization;
	return $Memoization->exists($key);
}
function cache_isset($key) { # obsolete
	global $Memoization;
	return $Memoization->exists($key);
}

/* bool */
function cache_del($key) {
	global $Memoization;
	return $Memoization->del($key);
}
function cache_unset($key) { # obsolete
	global $Memoization;
	return $Memoization->del($key);
}

/* int */
function cache_inc($key, $value=null, $ttl=null) {
	global $Memoization;
	return $Memoization->inc($key, $value, $ttl);
}

/* int */
function cache_dec($key, $value=null, $ttl=null) {
	global $Memoization;
	return $Memoization->dec($key, $value, $ttl);
}

/* null */
function cache_lock($key) {
	global $Memoization;
	return $Memoization->lock($key, $value, $ttl);
}

/* null */
function cache_unlock($key) {
	global $Memoization;
	return $Memoization->unlock($key, $value, $ttl);
}

/* filtre pour la page de cfg */
function memoization_methode($methode=null) {
	return MCache::methode($methode);
}


//
// Cache a function's result cache_me()
// (c) Fil 2009 - Double-licensed under the GNU/LGPL and MIT licenses
// http://zzz.rezo.net/-SPIP-
// $ttl = time to live
// $vars = other variables that could change the result
// (the function's variables are automatically taken into account)
//
// Usage: include_spip('inc/memoization');
// In any cacheable function add at top:
// if(!is_null($c=cache_me())) return$c;
if (!function_exists('debug_backtrace')) {
	function cache_me() {return;}
} else {
	function cache_me($vars=null, $ttl=3600) {
		$trace = debug_backtrace();
		$trace = $trace[1];
		if (isset($trace['object']))
			$fun = array($trace['object'], $trace['function']);
		else
			$fun = $trace['function'];
		$key = md5(
			$fun
			.serialize($trace['args'])
			.serialize($vars)
		);
		if (!cache_exists($key)) {
			cache_set($key, null, $ttl);
			$r = call_user_func_array($fun, $trace['args']);
			cache_set($key, $r, $ttl);
			return $r;
		}
		return cache_get($key);
	}
}

// outil pour memcache (hosts et ports a configurer dans le CFG)
function cfg_memcache_servers() {
	$cfg = @unserialize($GLOBALS['meta']['memoization']);
	if (!$cfg = $cfg['memcache_servers'])
		$cfg = 'localhost:11211';
	preg_match_all('/[a-z0-9._-]*(?::\d+)/', $cfg, $s, PREG_PATTERN_ORDER);
	return $s[0];
}

?>
