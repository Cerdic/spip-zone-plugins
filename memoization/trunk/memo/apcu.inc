<?php
if (!defined('_CACHE_NAMESPACE'))
	define('_CACHE_NAMESPACE', $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].":");

if (!defined('_CACHE_KEY'))
	define('_CACHE_KEY', '0123456789ABCDEF');

class MCacheBackend_apcu extends MCacheBackend {

	function key_for_lock($str) {
		return _CACHE_NAMESPACE . 'lock:' . md5($str . "locked");
	}

	function _xor($message) {
		$key = _CACHE_KEY;
		$keylen = strlen($key);
		if (!$keylen)
			return $message;

		$messagelen = strlen($message);
		for ($i = 0; $i < $messagelen; $i++) {
			$message[$i] = ~($message[$i] ^ $key[$i % $keylen]);
		}
		return $message;
	}

	function store_value($value) {
		if (is_object($value)
			or is_array($value)
			or is_bool($value)
			or !$value) {
				return array($this->_xor(serialize($value)));
		}
		return $this->_xor($value);
	}

	function unstore_value($a) {
		if (is_null($a)) {
			return $a;
		}
		// maybe_unserialize
		if (is_array($a)){
			return @unserialize($this->_xor($a[0]));
		}
		return $this->_xor($a);
	}

	function get($key) {
		$a = apcu_fetch(_CACHE_NAMESPACE.$key);
		return $this->unstore_value($a);
	}
	
	function set($key, $value, $ttl=null) {
		return is_null($ttl)
			? apcu_store(_CACHE_NAMESPACE.$key, $this->store_value($value))
			: apcu_store(_CACHE_NAMESPACE.$key, $this->store_value($value), $ttl);
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
		// le cache en memoire est de toute facon invalide par la globale cache_mark et le ttl est cense nettoyer le reste
		if (!class_exists('APCUIterator'))
			return false;
		$a = new APCUIterator('/^'.preg_quote(_CACHE_NAMESPACE).'/', APC_ITER_ALL, 256*256);
		apcu_delete($a);
		return true;
	}

}
