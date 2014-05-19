<?php

namespace Sphinx\SphinxQL;



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
		try {
			$this->sql = new \PDO("mysql:host=" . $this->host . ";port=" . $this->port, "", "");
		} catch (\Exception $e) {
			var_dump($e->getMessage());
			return false;
		}
		return true;
	}

	/**
	 * Exécute une requête
	**/
	public function query($query) {
		if (!$this->sql) {
			return false;
		}
		return $this->sql->query($query);
	}

	/**
	 * Prépare une requête
	**/
	public function prepare($query) {
		if (!$this->sql) {
			return false;
		}
		return $this->sql->prepare($query);
	}


	/**
	 * Récupère toutes les informations de la requête ET ses metas
	**/
	public function allfetsel($query) {
		if (!$this->sql) {
			return false;
		}

		$liste = array(
			'docs'   => array(),
			'facets' => array(),
			'meta'   => array(),
			'query'  => $query
		);

		if ($docs = $this->query($query)) {
			// les jeux de réponses sont les suivant :
			// 1) les documents trouvés
			// 2+) les FACET à la suite
			$reponses = array();
			 do {
				$reponses[] = $docs->fetchAll(\PDO::FETCH_ASSOC);
			} while ($docs->nextRowset());

			$liste['docs']   = array_shift($reponses);
			$liste['facets'] = $this->parseFacets($reponses);

			$meta = $this->query('SHOW meta');
			if ($errs = $this->sql->errorInfo()) {
				# TODO: comprendre le pourquoi de l'erreur
				# Cannot execute queries while other unbuffered queries are active. Consider using PDOStatement::fetchAll(). Alternatively, if your code is only ever going to run against mysql, you may enable query buffering by setting the PDO::MYSQL_ATTR_USE_BUFFERED_QUERY attribute.
				var_dump($errs);
			}
			if ($meta) {
				$liste['meta']   = $this->parseMeta($meta->fetchAll(\PDO::FETCH_ASSOC));
			}
		} elseif ($errs = $this->sql->errorInfo()) {
			var_dump($errs);
		}

		return array('query' => $liste);
	}


	/**
	 * Transforme un tableau de FACET en tableau PHP utilisable
	 *
	 * @param array $facettes
	 * @return array
	**/
	public function parseFacets($facettes) {
		$facets = array();
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
						$facets[$key] = array();
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
		$liste = array();
		foreach ($metas as $meta) {
			$cle = $meta['Variable_name'];
			$val = $meta['Value'];
			// cles keywords[0] ...
			if (substr($cle,-1,1) == ']') {
				list($cle, $index) = explode('[', $cle);
				$index = rtrim($index, ']');

				if (!isset($liste[$cle])) {
					$liste[$cle] = array();
				}

				$liste[$cle][$index] = $val;
			} else {
				$liste[$cle] = $val;
			}
		}
		if (isset($liste['keyword'])) {
			$liste['keywords'] = array();
			foreach ($liste['keyword'] as $index => $key) {
				$liste['keywords'][$key] = array(
					'keyword' => $key,
					'docs' => $liste['docs'][$index],
					'hits' => $liste['hits'][$index],
				);
			}
			unset($liste['keyword'], $liste['docs'], $liste['hits']);
		}
		return $liste;
	}
}

