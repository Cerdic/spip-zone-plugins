<?php

# http://php.net/memcache

if (!defined('_CACHE_NAMESPACE'))
	define('_CACHE_NAMESPACE', $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].":");
if (!defined('_LOCK_MAX'))
	define('_LOCK_MAX', 30); # max lock time (seconds)

class MCacheBackend_memcache extends MCacheBackend {
	var $memcache = null;
	var $ok = false;

	function init($params=null) {
		foreach(cfg_memcache_servers() as $server) {
			list($host, $port) = explode(':', $server);
			if (!$this->memcache)
				$this->memcache = @memcache_connect($host,$port);
			else
				@memcache_add_server($this->memcache, $host, $port);
		}
		if ($this->memcache) $this->ok = true;
	}

	function get($key) {
		if (!$this->ok) return null;
		$a = memcache_get($this->memcache, _CACHE_NAMESPACE.$key);
		// maybe_unserialize
		if (is_array($a))
			return @unserialize($a[0]);
		else
			return $a;
	}
	
	function set($key, $value, $ttl=null) {
		if (!$this->ok) return null;
		// maybe_serialize
		if (is_object($value) OR is_array($value) OR is_null($value))
			$value = array(serialize($value));
		$flag = strlen(is_array($value) ? $value[0] : $value) > 16000
			? MEMCACHE_COMPRESSED : 0;
		return is_null($ttl)
			? memcache_set($this->memcache, _CACHE_NAMESPACE.$key, $value)
			: memcache_set($this->memcache, _CACHE_NAMESPACE.$key, $value, $flag, $ttl);
	}
	
	function exists($key) {
		if (!$this->ok) return null;
		return memcache_get($this->memcache, _CACHE_NAMESPACE.$key) !== FALSE;
	}
	
	function del($key) {
		if (!$this->ok) return null;
		return memcache_delete($this->memcache, _CACHE_NAMESPACE.$key, 0);
	}
	
	function inc($key, $value=null, $ttl=null) {
		if (!$this->ok) return null;

		// set if not exists
		memcache_add($this->memcache, _CACHE_NAMESPACE.$key, 0, $ttl);
	
		if (!isset($value))
			$value = 1;
		return memcache_increment($this->memcache, _CACHE_NAMESPACE.$key, $value);
	}
	
	function dec($key, $value=null, $ttl=null) {
		if (!$this->ok) return null;

		// set if not exists
		memcache_add($this->memcache, _CACHE_NAMESPACE.$key, 0, $ttl);
	
		if (!isset($value))
			$value = 1;
		return memcache_decrement($this->memcache, _CACHE_NAMESPACE.$key, $value);
	}
	
	function lock($key, /* private */ $unlock = false) {
		static $locks = array();
	
		if (!$this->ok) return null;

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
			# attention inc() renvoie parfois false (??)
			if ($this->inc('lock::'.$key, 1, _LOCK_MAX) <= 1) {
				$this->set('lock::'.$key, 2, _LOCK_MAX);
				$locks[$key] = true;
				register_shutdown_function(array($this,'unlock'), $key);
				return true;
			}
	
			return false;
		}
	}
	
	function unlock($key) {
		if (!$this->ok) return null;
		$this->lock($key, true);
	}

}

?>
