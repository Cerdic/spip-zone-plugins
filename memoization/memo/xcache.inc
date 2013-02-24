<?php

# http://xcache.lighttpd.net/wiki/XcacheApi
if (!defined('_CACHE_NAMESPACE'))
	define('_CACHE_NAMESPACE', $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].":");
if (!defined('_LOCK_MAX'))
	define('_LOCK_MAX', 30); # max lock time (seconds)


class MCacheBackend_xcache extends MCacheBackend {

	function get($key) {
		$a = @xcache_get(_CACHE_NAMESPACE.$key);
		// maybe_unserialize
		if (is_array($a))
			return @unserialize($a[0]);
		else
			return $a;
	}
	
	function set($key, $value, $ttl=null) {
		// maybe_serialize
		if (is_object($value) OR is_array($value))
			$value = array(serialize($value));
		return is_null($ttl)
			? xcache_set(_CACHE_NAMESPACE.$key, $value)
			: xcache_set(_CACHE_NAMESPACE.$key, $value, $ttl);
	}
	
	function exists($key) {
		return xcache_isset(_CACHE_NAMESPACE.$key);
	}
	
	function del($key) {
		return xcache_unset(_CACHE_NAMESPACE.$key);
	}
	
	function inc($key, $value=null, $ttl=null) {
		$a = func_get_args();
		$a[0] = _CACHE_NAMESPACE.$key;
		return call_user_func_array('xcache_inc', $a);
	}
	
	function dec($key, $value=null, $ttl=null) {
		$a = func_get_args();
		$a[0] = _CACHE_NAMESPACE.$key;
		return call_user_func_array('xcache_dec', $a);
	}
	
	function lock($key, /* private */ $unlock = false) {
		static $locks = array();
	
		/* unlock */
		if ($unlock) {
			unset($locks[$key]);
			$this->del('lock::'.$key);
		}
		/* lock */
		else {
			# si on l'a deja, ok
			if (isset($locks[$key]))
				return true;
	
			# sinon s'il existe, on attend qu'il se libere
			$wait = 0;
			while ($this->exists('lock::'.$key)) {
				if ($wait++ < _LOCK_MAX)
					sleep(1);
				else
					return false;
			}
	
			# ... puis on le pose
			if ($this->inc('lock::'.$key, 1, _LOCK_MAX) <= 1) {
				$locks[$key] = true;
				register_shutdown_function(array($this,'unlock'), $key);
				return true;
			}
	
			return false;
		}
	}
	
	function unlock($key) {
		$this->lock($key, true);
	}

	function size() {
		# necessite xcache.admin.user
		#$a = xcache_list();
		#var_dump($a);
		return false;
	}
}

?>
