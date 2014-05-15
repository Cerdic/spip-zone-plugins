<?php

namespace Sphinx;



class SphinxQL {

	private $host;
	private $port;
	private $sql; // objet PDO

	public function __construct($host = '127.0.0.1', $port = 9306) {
		$this->host = $host;
		$this->port = $port;
		$this->connect();
	}

	/**
	 * Se connecter à Sphinx
	**/
	public function connect() {
		$this->sql = new \PDO("mysql:host=" . $this->host . ";port=" . $this->port, "", "");
	}

	/**
	 * Exécute une requête
	**/
	public function query($query) {
		if (!$this->sql) {
			throw new Exception('Connecteur non exécuté');
		}
		return $this->sql->query($query);
	}

	/**
	 * Prépare une requête
	**/
	public function prepare($query) {
		if (!$this->sql) {
			throw new Exception('Connecteur non exécuté');
		}
		return $this->sql->prepare($query);
	}


	/**
	 * Récupère toutes les informations de la requête ET ses metas
	**/
	public function allfetsel($query) {
		$liste = [
			'docs' => [],
			'meta' => []
		];

		if ($docs = $this->query($query)) {
			$meta = $this->query('SHOW meta');
			$liste['docs'] = $docs->fetchAll(\PDO::FETCH_ASSOC);
			$liste['meta'] = $this->parseMeta($meta->fetchAll(\PDO::FETCH_ASSOC));
		}

		return $liste;
	}



	/**
	 * Transforme un tableau des Metas en tableau PHP élaboré
	 *
	 * Regroupe entre autres les infos de keywords
	 */
	public function parseMeta($metas) {
		$liste = [];
		foreach ($metas as $meta) {
			$cle = $meta['Variable_name'];
			$val = $meta['Value'];
			// cles keywords[0] ...
			if (substr($cle,-1,1) == ']') {
				list($cle, $index) = explode('[', $cle);
				$index = rtrim($index, ']');

				if (!isset($liste[$cle])) {
					$liste[$cle] = [];
				}

				$liste[$cle][$index] = $val;
			} else {
				$liste[$cle] = $val;
			}
		}
		if (isset($liste['keyword'])) {
			$liste['keywords'] = array();
			foreach ($liste['keyword'] as $index => $key) {
				$liste['keywords'][$key] = [
					'keyword' => $key,
					'docs' => $liste['docs'][$index],
					'hits' => $liste['hits'][$index],
				];
			}
			unset($liste['keyword'], $liste['docs'], $liste['hits']);
		}
		return $liste;
	}
}

