<?php
if (!defined('_CACHE_NAMESPACE'))
	define('_CACHE_NAMESPACE', $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].":");


class MCacheBackend_redis extends MCacheBackend {

	var $redis = null;
	var $serializer = null;

	function init($params=null) {

		$cfg = cfg_redis_server();
		$redis = new Redis();
		
		if($cfg['redis_type'] == "server"){
			list($host, $port) = explode(':', $cfg['redis_server']);
			$port = intval($port);
			$connect = $redis->connect($host, $port, 2.5);
		}else{
			$connect = $redis->connect($cfg['redis_sock']);
		}
		
		if($connect) {
			if(!empty($cfg['redis_auth'])){
				$redis->auth($cfg['redis_auth']);
			}

			$cfg['redis_dbindex'] = (isset($cfg['redis_dbindex']) && !empty($cfg['redis_dbindex'])) ? intval($cfg['redis_dbindex']) : 0;
			if(is_int($cfg['redis_dbindex'])){
				$redis->select($cfg['redis_dbindex']);
			}

			if($cfg['redis_serializer'] == "igbinary") {
				$this->serializer = Redis::SERIALIZER_IGBINARY;
			}else{
				$this->serializer = Redis::SERIALIZER_PHP;
			}

			$this->redis = $redis;
		}

	}

	function get($key) {
		$encoding = $this->redis->object('encoding', _CACHE_NAMESPACE.$key);

		if($encoding == "int") {
			$this->redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_NONE);
		}else{
			$this->redis->setOption(Redis::OPT_SERIALIZER, $this->serializer);
		}

		$value = $this->redis->get(_CACHE_NAMESPACE.$key);

		if ($value !== false && $encoding == "int") {
			return intval($value);
		}

		return $value;
	}

	function set($key, $value, $ttl=null) {
		if (is_int($value)) {
			$this->redis->setOption(Redis::OPT_SERIALIZER, Redis::SERIALIZER_NONE);
		}else{
			$this->redis->setOption(Redis::OPT_SERIALIZER, $this->serializer);
		}

		return is_null($ttl)
			? $this->redis->set(_CACHE_NAMESPACE.$key, $value)
			: $this->redis->setEx(_CACHE_NAMESPACE.$key, $ttl, $value);
	}

	function exists($key) {
		return $this->redis->exists(_CACHE_NAMESPACE.$key);
	}

	function del($key) {
		return $this->redis->delete(_CACHE_NAMESPACE.$key);
	}

	function inc($key, $value=null, $ttl=null) {
		$value = isset($value) ? intval($value) : 1;
		$new_value = $this->redis->incrBy(_CACHE_NAMESPACE.$key, $value);
		if (!is_null($ttl)) {
			$this->redis->setTimeout(_CACHE_NAMESPACE.$key, $ttl);
		}
		return $new_value;
	}

	function dec($key, $value=null, $ttl=null) {
		$value = isset($value) ? intval($value) : 1;
		$new_value = $this->redis->decrBy(_CACHE_NAMESPACE.$key, $value);
		if (!is_null($ttl)) {
			$this->redis->setTimeout(_CACHE_NAMESPACE.$key, $ttl);
		}
		return $new_value;
	}


	function lock($key, $unlock = false) {
		static $locks = array();
	
		/* unlock */
		if ($unlock) {
			unset($locks[$key]);
			return $this->del('lock::'.$key);
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
		return $this->lock($key, true);
	}

	function purge() {
		foreach ($this->redis->keys(_CACHE_NAMESPACE."*") as $key) {
			$this->redis->del($key);
		}

		return true;
	}

	function size() {
		$total_size = 0;

		foreach ($this->redis->keys(_CACHE_NAMESPACE."*") as $key) {

	        switch ($this->redis->type($key)) {
	            case Redis::REDIS_LIST:
	                $size = $this->redis->lSize($key);
	                break;
	            case Redis::REDIS_SET:
	                $size = $this->redis->sCard($key);
	                break;
	            case Redis::REDIS_HASH:
	                $size = $this->redis->hLen($key);
	                break;
	            case Redis::REDIS_ZSET:
	                $size = $this->redis->zCard($key);
	                break;
	            case Redis::REDIS_STRING:
	                $size = $this->redis->strlen($key);
	                break;
	            default:
	                $size = '-';
	        }

	        if($size != '-') {
	        	$total_size += $size;
	        }
	        
        }

        return $total_size;
	}
    
}

