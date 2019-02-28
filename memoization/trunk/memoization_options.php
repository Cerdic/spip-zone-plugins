<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

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
	function purge() {}
}

/* objet MCache */
class MCache {
	var $methode;
	var $backend;

	function memory() {
		return !in_array($this->methode, array('filecache', 'nocache'));
	}

	function __construct($methode=null, $params=array()) {
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
			$methodes = array('apcu', 'apc', 'xcache', 'filecache', 'nocache');
			while (!MCache::methode($methode = array_shift($methodes))){};
			return $methode;
		}

		switch($methode) {
			case 'apc':
				return function_exists('apc_exists');
			case 'apcu':
				return function_exists('apcu_exists');
			case 'xcache':
				if (!function_exists('xcache_set'))
					return false;
				@xcache_set('xcache_autodetect',1234);
				return @xcache_get('xcache_autodetect')==1234;
			case 'memcache':
				return function_exists('memcache_set');
			case 'memcached':
				return class_exists('Memcached');
			case 'redis':
				return extension_loaded('redis');
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

	/* bool */
	function purge() {
		if (method_exists($this->backend, 'purge'))
			return $this->backend->purge();
		else
			return false;
	}

}

if (isset($GLOBALS['meta']['cache_namespace'])) {
	if (!defined('_CACHE_NAMESPACE')) {
		define('_CACHE_NAMESPACE', $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].':'.$GLOBALS['meta']['cache_namespace'].':');
	}
	if (isset($GLOBALS['meta']['cache_key'])) {
		define('_CACHE_KEY', $GLOBALS['meta']['cache_key']);
	}
}

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
	return $Memoization->lock($key);
}

/* null */
function cache_unlock($key) {
	global $Memoization;
	return $Memoization->unlock($key);
}

/* null */
function cache_purge() {
	global $Memoization;
	return $Memoization->purge();
}

/* filtre pour la page de cfg */
function memoization_methode($methode=null) {
	return MCache::methode($methode);
}


/**
 * Recuperer de l'editorial cache, mais invalide avec la meta derniere_modif ou avec un var_mode
 * @param string $key
 * @return string
 */
function cache_edito_get($key) {
	if (function_exists('cache_get')
	  and !_VAR_MODE
	  and $cache = cache_get("edito-$key")
	  and isset($cache['time'])
	  and isset($cache['value'])
	  and (!isset($GLOBALS['meta']['derniere_modif']) or $cache['time']>$GLOBALS['meta']['derniere_modif'])) {
		return $cache['value'];
	}

	return null;
}

/**
 * Stocker de l'editorial cache, avec un timestamp pour gerer l'invalidation
 * @param string $key
 * @param mixed $value
 * @return mixed
 */
function cache_edito_set($key, $value) {
	if (function_exists('cache_set')) {
		$cache = array('value' => $value, 'time' => $_SERVER['REQUEST_TIME']);
		cache_set("edito-$key", $cache);
	}
	return $value;
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
	if (!$cfg = $cfg['memcache_servers']) {
		$cfg = 'localhost:11211';
	}
	preg_match_all('/[a-z0-9._-]*(?::\d+)/', $cfg, $s, PREG_PATTERN_ORDER);
	return $s[0];
}

function cfg_redis_server() {
	$cfg = @unserialize($GLOBALS['meta']['memoization']);
	if (!$cfg || !isset($cfg['redis_type']) || empty($cfg['redis_type'])) {
		$cfg = array(
			'redis_type' => 'serveur',
			'redis_server' => '127.0.0.1:6379',
			'redis_sock' => '/tmp/redis.sock',
			'redis_auth' => '',
			'redis_dbindex' => 0,
			'redis_serializer' => 'php',
		);
	}
	return $cfg;
}

function redis_serializer() {
	$serializers = array();
	if (defined('Redis::SERIALIZER_IGBINARY') && extension_loaded('igbinary')) {
		$serializers['igbinary'] = array('libelle' => 'SERIALIZER_IGBINARY', 'statut' => 'actif');
	} else {
		$serializers['igbinary'] = array('libelle' => 'SERIALIZER_IGBINARY', 'statut' => 'inactif');
	}
	$serializers['php'] = array('libelle' => 'SERIALIZER_PHP', 'statut' => 'actif');
	return $serializers;
}
