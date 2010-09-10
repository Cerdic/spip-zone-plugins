<?php

# http://bart.eaccelerator.net/doc/phpdoc/eAccelerator/_shared_memory_php.html

@define('_CACHE_NAMESPACE', $_SERVER['HTTP_HOST'].':');

class CacheBackend_eaccelerator extends CacheBackend {

	function get($key) {
		return ($v = eaccelerator_get(_CACHE_NAMESPACE.$key))
			? @unserialize($v)
			: null;
	}
	
	function set($key, $value, $ttl = null) {
		$value = serialize($value);
		return is_null($ttl)
			? eaccelerator_put(_CACHE_NAMESPACE.$key, $value)
			: eaccelerator_put(_CACHE_NAMESPACE.$key, $value, $ttl);
	}
	
	function test($key) {
		return cache_get($key) !== null;
	}
	
	function del($key) {
		return eaccelerator_rm(_CACHE_NAMESPACE.$key);
	}
	
	function inc($key, $value=null, $ttl=null) {
		eaccelerator_lock($key);
		$value = isset($value) ? intval($value) : 1;
		$value += intval($this->get($key));
		$this->set($key, $value, $ttl);
		eaccelerator_unlock($key);
		return $value;
	}
	
	function dec($key, $value=null, $ttl=null) {
		$value = isset($value) ? intval($value) : 1;
		return $this->inc($key, -$value, $ttl);
	}
	
	function lock($key) {
		return eaccelerator_lock(_CACHE_NAMESPACE.$key);
	}
	
	function unlock($key) {
		eaccelerator_unlock(_CACHE_NAMESPACE.$key);
	}

}

?>
