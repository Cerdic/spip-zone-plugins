<?php

# http://php.net/memcached

if (!defined('_CACHE_NAMESPACE'))
	define('_CACHE_NAMESPACE', $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].":");
if (!defined('_LOCK_MAX'))
	define('_LOCK_MAX', 30); # max lock time (seconds)

class MCacheBackend_memcached extends MCacheBackend {
	/** @var Memcached $memcache */
	var $memcached = null;
	var $ok = false;

	function init($params=null) {
		foreach(cfg_memcache_servers() as $server) {
			list($host, $port) = explode(':', $server);
			if (!$this->memcached) {
				$this->memcached = new Memcached();
			}
			$this->memcached->addServer($host, $port);
		}
		if ($this->memcached) {
			$this->ok = true;
		}
	}

	function get($key) {
		if (!$this->ok) {
			return null;
		}
		$a = $this->memcached->get(_CACHE_NAMESPACE.$key);
		// maybe_unserialize
		if (is_array($a)) {
			return @unserialize($a[0]);
		} else {
			return $a;
		}
	}
	
	function set($key, $value, $ttl=null) {
		if (!$this->ok) {
			return null;
		}
		// maybe_serialize
		if (is_object($value) OR is_array($value) OR is_null($value)) {
			$value = array(serialize($value));
		}
		return is_null($ttl)
			? $this->memcached->set(_CACHE_NAMESPACE.$key, $value)
			: $this->memcached->set(_CACHE_NAMESPACE.$key, $value, $ttl);
	}
	
	function exists($key) {
		if (!$this->ok) {
			return null;
		}
		return $this->memcached->get(_CACHE_NAMESPACE.$key) !== FALSE;
	}
	
	function del($key) {
		if (!$this->ok) {
			return null;
		}
		return $this->memcached->delete(_CACHE_NAMESPACE.$key, 0);
	}
	
	function inc($key, $value=null, $ttl=null) {
		if (!$this->ok) {
			return null;
		}
		// set if not exists
		$this->memcached->add(_CACHE_NAMESPACE.$key, 0, $ttl);
		if (!isset($value)) {
			$value = 1;
		}
		return $this->memcached->increment(_CACHE_NAMESPACE.$key, $value);
	}
	
	function dec($key, $value=null, $ttl=null) {
		if (!$this->ok) {
			return null;
		}
		// set if not exists
		$this->memcached->add(_CACHE_NAMESPACE.$key, 0, $ttl);
		if (!isset($value)) {
			$value = 1;
		}
		return $this->memcached->decrement(_CACHE_NAMESPACE.$key, $value);
	}

	function lock($key, /* private */ $unlock = false) {
		static $locks = array();
	
		if (!$this->ok) {
			return null;
		}

		/* unlock */
		if ($unlock) {
			unset($locks[$key]);
			$this->del('lock::'.$key);
		}
		/* lock */
		else {
			# si on l'a deja, ok
			if (isset($locks[$key])) {
				return true;
			}
	
			# sinon s'il existe, on attend qu'il se libere
			$wait = 0;
			while ($this->exists('lock::'.$key)) {
				if ($wait++ < _LOCK_MAX) {
					sleep(1);
				} else {
					return false;
				}
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
		if (!$this->ok) {
			return null;
		}
		$this->lock($key, true);
	}

}
