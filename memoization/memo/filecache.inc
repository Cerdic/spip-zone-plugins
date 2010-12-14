<?php

if (!defined('_DIR_TMP')) define('_DIR_TMP', 'tmp/');
if (!defined('_DIR_FILECACHE')) define('_DIR_FILECACHE', 'cache/');

class MCacheBackend_filecache extends MCacheBackend {

	// file named tmp/cache/ab/cd
	// that's a maximum of 16^4 files in 256 directories
	function filename($u) {
		$u = md5($u);
		$d = substr($u,0,2); 
		$u = substr($u,2,2);
	
		# SPIP
		if (function_exists('sous_repertoire')) {
			return
				sous_repertoire(
					sous_repertoire(_DIR_TMP, _DIR_FILECACHE),
					$d
				)
				. $u;
		}
	
		# Normal PHP
		if (!is_dir(_DIR_TMP._DIR_FILECACHE))
			@mkdir(_DIR_TMP._DIR_FILECACHE);
		if (!is_dir($sub = _DIR_TMP._DIR_FILECACHE.$d))
			@mkdir($sub);
		return $sub.'/'.$u;
	}
	
	function get($key) {
		if ($c = @file_get_contents($this->filename($key))
		AND $r = unserialize($c)
		AND $r[1] === $key
		AND $r[2] >= time())
			return $r[0];
	}
	
	function set($key, $value, $ttl=null) {
		$r = array($value);
		$r[1] = $key;
		$r[2] = isset($ttl)
			? time() + intval($ttl)
			: time() + 365*24*3600;
	
		return (
			$f = $this->filename($key)
			AND $n = @tempnam(dirname($f), 'xc')
			AND $h = @fopen($n, 'w')
			AND @fwrite($h, serialize($r))
			AND @fclose($h)
			AND @rename($n, $f)
		);
	}
	
	function exists($key) {
		if ($c = @file_get_contents($this->filename($key))
		AND $r = @unserialize($c)
		AND $r[1] == $key
		AND $r[2] >= time())
			return true;
		else
			return false;
	}
	
	function del($key) {
		return (
			$c = @file_get_contents($f = $this->filename($key))
			AND $s = supprimer_fichier($f)
			AND $r = @unserialize($c)
			AND $r[1] == $key
			AND $r[2] >= time()
		);
	}
	
	function inc($key, $value=null, $ttl=null) {
		$this->lock($key);
		$value = (isset($value) ? intval($value) : 1) + intval($this->get($key));
		$this->set($key, $value);
		$this->unlock($key);
		return $value;
	}
	
	function dec($key, $value=null, $ttl=null) {
		$value = isset($value) ? intval($value) : 1;
		return $this->inc($key, -$value, $ttl);
	}
	
	function lock($key, /* private */ $unlock=false) {
		static $locks = array();
		$f = $this->filename($key);
	
		/* unlock */
		if ($unlock) {
			if (isset($locks[$f])
			AND is_resource($locks[$f])) {
				@flock($locks[$f], LOCK_UN);
				@fclose($locks[$f]);
				unset($locks[$f]);
			}
		}
		/* lock */
		else {
			if (!isset($locks[$f])) {
				$locks[$f] = @fopen($f, 'a');
				@flock($locks[$f], LOCK_EX);
				return true;
			}
		}
	}
	
	function unlock($key) {
		$this->lock($key, true);
	}
}

?>
