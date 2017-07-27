<?php
if (!defined('_CACHE_NAMESPACE'))
	define('_CACHE_NAMESPACE', $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].":");

class MCacheBackend_apc extends MCacheBackend {

	function key_for_lock($str) {
		return _CACHE_NAMESPACE.md5($str."locked");
	}

	function get($key) {
		$a = apc_fetch(_CACHE_NAMESPACE.$key);
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
			? apc_store(_CACHE_NAMESPACE.$key, $value)
			: apc_store(_CACHE_NAMESPACE.$key, $value, $ttl);
	}
	
	function exists($key) {
		return apc_exists(_CACHE_NAMESPACE.$key);
	}
	
	function del($key) {
		return apc_delete(_CACHE_NAMESPACE.$key);
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
	   return apc_add($this->key_for_lock($key), true);
	}
	
	function unlock($key) {
		return apc_delete($this->key_for_lock($key));
	}

	function size() {
		// le cache n'est pas vidé au moment de la demande de vidage mais au moment de l'affichage de la taille
		// (avant, les éléments étaient considérés comme périmés donc recalculés au besoin, mais conservés)
		// todo plus générique : personnaliser action_purger_dist selon la méthode de cache
		if ($mark = $GLOBALS['meta']['cache_mark']) {
			spip_log("cache_mark=$mark; apc_clear_cache('user')");
			apc_clear_cache('user');
			ecrire_meta('cache_mark', 0);
			$GLOBALS['meta']['cache_mark']=0;
		}; 
		$a = new APCIterator('user'); 
		return $a->getTotalSize ();
	}
}

?>
