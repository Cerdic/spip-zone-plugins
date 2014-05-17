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
			'docs'   => [],
			'facets' => [],
			'meta'   => []
		];

		if ($docs = $this->query($query)) {
			// les jeux de réponses sont les suivant :
			// 1) les documents trouvés
			// 2+) les FACET à la suite
			$reponses = [];
			 do {
				$reponses[] = $docs->fetchAll(\PDO::FETCH_ASSOC);
			} while ($docs->nextRowset());

			$meta = $this->query('SHOW meta');

			$liste['docs']   = array_shift($reponses);
			$liste['facets'] = $this->parseFacets($reponses);
			$liste['meta']   = $this->parseMeta($meta->fetchAll(\PDO::FETCH_ASSOC));
		} elseif ($errs = $this->sql->errorInfo()) {
			var_dump($errs);
		}

		return [$liste];
	}


	/**
	 * Transforme un tableau de FACET en tableau PHP utilisable
	 *
	 * @param array $facettes
	 * @return array
	**/
	public function parseFacets($facettes) {
		$facets = [];
		if (is_array($facettes)) {
			foreach($facettes as $facette) {
				foreach ($facette as $i => $desc) {
					$nb = $desc['count(*)'];
					unset($desc['count(*)']);
					$key  = array_keys($desc);
					$key  = reset($key);
					$value = array_shift($desc);
					if (count($desc)) {
						var_dump($desc);
						die("Contenu non pris en compte dans FACET !");
					}
					if ($i == 0) {
						$facets[$key] = [];
					}
					$facets[$key][$value] = $nb;
				}
			}
		}
		return $facets;
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

