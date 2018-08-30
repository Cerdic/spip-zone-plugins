<?php
if (!defined('_CACHE_NAMESPACE'))
	define('_CACHE_NAMESPACE', $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].":");

class MCacheBackend_apcu extends MCacheBackend {

	function key_for_lock($str) {
		return _CACHE_NAMESPACE.md5($str."locked");
	}

	function get($key) {
		$a = apcu_fetch(_CACHE_NAMESPACE.$key);
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
			? apcu_store(_CACHE_NAMESPACE.$key, $value)
			: apcu_store(_CACHE_NAMESPACE.$key, $value, $ttl);
	}
	
	function exists($key) {
		return apcu_exists(_CACHE_NAMESPACE.$key);
	}
	
	function del($key) {
		return apcu_delete(_CACHE_NAMESPACE.$key);
	}
	
	function inc($key, $value=null, $ttl=null) {
		while (true) {
			if ($this->lock($key)) {
				$value = isset($value) ? intval($value) : 1;
				$value += intval($this->get($key));
				$this->set($key, $value, $ttl);
				$this->unlock($key);
				return $value;
			}
		} 
	}
	
	function dec($key, $value=null, $ttl=null) {
		$value = isset($value) ? intval($value) : 1;
		return $this->inc($key, -$value, $ttl);
	}

	function lock($key, /* private */ $unlock = false) {
	   return apcu_add($this->key_for_lock($key), true);
	}
	
	function unlock($key) {
		return apcu_delete($this->key_for_lock($key));
	}

	function size() {
		if (!class_exists('APCUIterator'))
			return false;
		$a = new APCUIterator('/^'.preg_quote(_CACHE_NAMESPACE).'/', APC_ITER_ALL, 256*256);
		return $a->getTotalSize ();
	}

	function purge() {
		if (!class_exists('APCUIterator'))
			return false;
		$a = new APCUIterator('/^'.preg_quote(_CACHE_NAMESPACE).'/', APC_ITER_ALL, 256*256);
		foreach ($a as $cache) {
			apcu_delete($cache['key']);
		}
		return true;
	}

}
