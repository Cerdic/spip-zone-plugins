<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

/*
Vérifier la connexion au serveur redis avec les paramètres fournis par le webmestre
*/
function memoization_formulaire_verifier($flux){

	if ($flux['args']['form'] == 'configurer_memoization'){
		$methode = _request('methode');
		if ($methode == "redis") {

			$redis_type = _request('redis_type');
			$redis_server = _request('redis_server');
			$redis_sock = _request('redis_sock');
			$redis_auth = _request('redis_auth');
			$redis_dbindex = _request('redis_dbindex');

			$redis = new Redis();
			
			if ($redis_type == "server") {
				list($host, $port) = explode(':', $redis_server);
				$port = intval($port);
				$connect = @$redis->connect($host, $port, 5);
			}else{
				$connect = @$redis->connect($redis_sock);
			}
			
			if ($connect) {

				if(!empty($redis_auth)){
					$auth = $redis->auth($redis_auth);
					if (!$auth)
						$flux['data']['message_erreur'] = _T('memoization:redis_erreur_password');
				}

				if(is_int($redis_dbindex)){
					$redis->select($redis_dbindex);
					if (!$dbindex)
						$flux['data']['message_erreur'] = _T('memoization:redis_erreur_database');
				}

				try{
					$ping = $redis->ping();
				} catch (Exception $e) {
					$flux['data']['message_erreur'] = _T('memoization:redis_erreur_connexion').": ".$e->getMessage();
				}

			}else{
				$flux['data']['message_erreur'] = _T('memoization:redis_erreur_connexion');
			}
			
		}
	}

	return $flux;
}