<?php

namespace Sphinx\SphinxQL;



class SphinxQL {

	private $host;
	private $port;
	private $sql; // objet MySQLi

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
			$this->sql = new \MySQLi($this->host, null, null, null, $this->port);
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
		spip_log($query, 'sphinx');
		if (!$this->sql) {
			return false;
		}
		return $this->sql->multi_query($query);
	}


	/**
	 * Échappe une chaîne
	**/
	public function escape_string($string) {
		if (!$this->sql) {
			return false;
		}
		return $this->sql->escape_string($string);
	}

	/**
	 * Récupère les dernières erreurs
	**/
	public function errors() {
		if (!$this->sql) {
			return false;
		}
		return $this->sql->error_list;
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

		try {
			$docs = $this->query($query);

			// les jeux de réponses sont les suivants :
			// 1) les documents trouvés
			// 2+) les FACET à la suite
			$reponses = array();
			do {
				if ($result = $this->sql->store_result()) {
					$a = array(); while ($row = $result->fetch_assoc()) $a[] = $row;
					$reponses[] = $a;
					$result->free();
				}
			} while ($this->sql->more_results() AND $this->sql->next_result());

			$liste['docs']   = array_shift($reponses);
			$liste['facets'] = $this->parseFacets($reponses);

		} catch  (\Exception $e) {
			echo "\n<div><tt>",htmlspecialchars($query),"</tt></div>\n";
			var_dump($e->getMessage());
			return false;
		}

		// recuperer les META
		if ($meta = $this->query('SHOW META')) {
			$result = $this->sql->store_result();
			$a = array(); while ($row = $result->fetch_assoc()) $a[] = $row;
			$liste['meta']   = $this->parseMeta($a);
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

