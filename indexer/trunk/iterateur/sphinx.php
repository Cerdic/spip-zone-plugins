<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Gestion de l'itérateur SPHINX
 *
 * @package SPIP\Indexer\Iterateur\Sphinx
**/

include_spip('iterateur/data');

/**
 * Créer une boucle sur un itérateur SPHINX
 *
 * Annonce au compilateur les "champs" disponibles,
 *
 * @param Boucle $b
 *     Description de la boucle
 * @return Boucle
 *     Description de la boucle complétée des champs
 */
function iterateur_SPHINX_dist($b) {
	$b->iterateur = 'SPHINX'; # designe la classe d'iterateur
	$b->show = array(
		'field' => array(
			'docs' => 'ARRAY',
			'meta' => 'ARRAY',
			'facets' => 'ARRAY',
			'query' => 'STRING',
			#'*' => 'ALL' // Champ joker *
		)
	);
	return $b;
}


/**
 * Iterateur SPHINX pour itérer sur des données
 *
 * La boucle SPHINX n'a toujours qu'un seul élément.
 */
class IterateurSPHINX implements Iterator {

	/**
	 * Type de l'iterateur
	 * @var string
	 */
	protected $type = 'SPHINX';

	/**
	 * Commandes transmises à l'iterateur
	 * @var array
	 */
	protected $command = array();

	/**
	 * Infos de debug transmises à l'iterateur
	 * @var array
	 */
	protected $info = array();

	/**
	 * Instance de SphinxQL
	 * @var \Sphinx\SphinxQL\SphinxQL
	 */
	protected $sphinxQL = null;

	/**
	 * Instance de SphinxQL\QueryApi
	 * @var \Sphinx\SphinxQL\QueryAPi
	 */
	protected $queryApi = null;

	/**
	 * Résultat de la requête à Sphinx
	 * @var array
	 */
	protected $result = array();

	/**
	 * Cle courante
	 * @var null
	 */
	protected $cle = null;

	/**
	 * Valeur courante
	 * @var null
	 */
	protected $valeur = null;

	/**
	 * Constructeur
	 *
	 * @param  $command
	 * @param array $info
	 */
	public function __construct($command, $info=array()) {

		$this->command = $command + array(
			'index'     => array(),
			'selection' => array(),
			'recherche' => array(),
			'snippet'   => array(),
			'facet'     => array(),

			'select_filter' => array(),
		);

#var_dump($this->command);

		$this->info = $info;

		include_spip('inc/indexer');

		$this->sphinxQL  = new \Sphinx\SphinxQL\SphinxQL(SPHINX_SERVER_HOST, SPHINX_SERVER_PORT);
		$this->queryApi  = new \Sphinx\SphinxQL\QueryApi();

		$this->setIndex($this->command['index']);
		$this->setSelection($this->command['selection']);
		$this->setRecherche($this->command['recherche']);
		$this->setOrderBy($this->command['orderby']);
		$this->setFacet($this->command['facet']);

		$this->setSelectFilter($this->command['select_filter']);

		$this->setSnippet($this->command);

		$this->runQuery();
	}


	public function runQuery() {
		$query  = $this->queryApi->get();
		$result = $this->sphinxQL->allfetsel($query);
		if (!$result) {
			return false;
		}
		$this->result = $result;
		return true;
	}


	public function quote($m) {
		return $this->queryApi->quote($m);
	}


	/**
	 * Définir la liste des index interrogés (FROM de la requête)
	 *
	 * Par défaut on utilise l'index déclaré dans la conf
	 *
	 * @param array $index Liste des index
	 * @return bool True si au moins un index est ajouté, false sinon
	**/
	public function setIndex($index) {
		if (!is_array($index)) $index = array($index);
		$index = array_filter($index);
		if (!$index) {
			$index[] = SPHINX_DEFAULT_INDEX;
		}
		foreach ($index as $i) {
			$this->queryApi->from($i);
		}
		return true;
	}



	/**
	 * Définir la liste des champs récupérés (SELECT de la requête)
	 *
	 * Par défaut, en absence de précisions, on prend tous les champs
	 *
	 * @param array $select Liste des index
	 * @return bool True si au moins un index est ajouté, false sinon
	**/
	public function setSelection($select) {
		if (!is_array($select)) $select = array($select);
		$select = array_filter($select);
		// si aucune selection demandée, on prend tout !
		if (!$select) {
			$select[] = '*';
		}
		foreach ($select as $s) {
			$this->queryApi->select($s);
		}
		return true;
	}



	/**
	 * Définir la recherche fulltext
	 *
	 * @param array $index Liste des index
	 * @return bool True si au moins un index est ajouté, false sinon
	**/
	public function setRecherche($recherche) {
		if (!is_array($recherche)) $recherche = array($recherche);
		$recherche = array_filter($recherche);
		if (!$recherche) {
			return false;
		}
		$match = implode(' ',$recherche);
		$this->queryApi
			->select('WEIGHT() AS score')
			->where('MATCH(' . $this->quote( $recherche ) . ')');
		return true;
	}


	public function setOrderby($orderby) {
		if (!is_array($orderby)) $orderby = array($orderby);
		$orderby = array_filter($orderby);
		if (!$orderby) {
			return false;
		}
		foreach ($orderby as $order) {
			// juste ASC ou DESC sans le champ… passer le chemin…
			if (in_array(trim($order), array('ASC', 'DESC'))) {
				continue;
			}
			if (!preg_match('/(ASC|DESC)$/i', $order)) {
				$order .= ' ASC';
			}
			$this->queryApi->orderby($order);
		}
		return true;
	}

	/**
	 * Définir le snippet
	 */
	public function setSnippet($command) {
		$snippet = array_filter($command['snippet']);
		// si aucune selection demandée, on prend tout !
		if (!$snippet) {
			return $this->setSnippetAuto($command);
		} else {
			$ok = true;
			foreach ($snippet as $s) {
				if (!is_array($s)) continue;
				if (!$s['phrase']) {
					$s['phrase'] = $this->getSnippetAutoPhrase($command);
				}
				$ok &= $this->setOneSnippet($s);
			}
		}
		return $ok;
	}

	/**
	 * Définir 1 snippet depuis sa description
	 *
	 * @param array $desc
	 * @return bool
	**/
	public function setOneSnippet($desc) {

		$desc += array(
			'champ'  => 'content',
			'phrase' => '',
			'limit'  => 200,
			'as'     => 'snippet'
		);
		if (!$desc['phrase']) {
			return false;
		}

		$desc['phrase'] = $this->queryApi->get_snippet_words($desc['phrase']);

		if (!$desc['phrase'] OR !$desc['champ']) {
			return false;
		}
		$this->queryApi->select("SNIPPET($desc[champ], " . $this->quote($desc['phrase']) . ", 'limit=$desc[limit]') AS $desc[as]");
		return true;
	}

	/**
	 * Définir automatiquement un snippet dans le champ 'snippet'
	 * à partir de la recherche et des filtres
	 */
	public function setSnippetAuto($command) {
		$phrase = $this->getSnippetAutoPhrase($command);
		if (!$phrase) return false;
		return $this->setOneSnippet(array('phrase' => $phrase));
	}

	/**
	 * Extrait de la commande de boucle les phrases pertinentes cherchées
	 *
	 * - Cherche la phrase de recherche
	 *
	 * @param array $command Commande de la boucle Sphinx
	 * @return string phrases séparées par espace.
	**/
	public function getSnippetAutoPhrase($command) {
		$phrase = '';

		// mots de la recherche
		$recherche = $command['recherche'];
		if (!is_array($recherche)) $recherche = array($recherche);
		$recherche = array_filter($recherche);
		$phrase .= implode(' ', $recherche);

		return $phrase;
	}


	/**
	 * Définit les commandes FACET
	 *
	 * @param array $facets Tableau des facettes demandées
	 * @return bool
	**/
	public function setFacet($facets) {
		$facets = array_filter($facets);
		if (!$facets) {
			return false;
		}
		$ok = true;
		foreach ($facets as $facet) {
			if (!isset($facet['alias']) OR !isset($facet['query'])) {
				$ok = false;
				continue;
			}
			$alias = trim($facet['alias']);
			$query = trim($facet['query']);
			if (!$alias OR !$query) {
				$ok =  false;
				continue;
			}
			$this->facet[] = array('alias' => $alias, 'query' => $query);
			$this->queryApi->facet($query);
		}
		return $ok;
	}



	/**
	 * Définit des filtres
	 *
	 * @param array $facets Tableau des filtres demandées
	 * @return bool
	**/
	public function setSelectFilter($filters) {
		// compter le nombre de filtres ajoutés à la requête.
		static $nb = 0;

		$facets = array_filter($filters);
		if (!$filters) {
			return false;
		}
		foreach ($filters as $filter) {
			// ignorer toutes les données vides
			if (!is_array($filter) OR !isset($filter['valeur']) OR !$valeur = $filter['valeur']) {
				continue;
			}
			if (is_string($valeur)) {
				$valeur = trim($valeur);
				$valeurs = array($valeur);
			} else {
				$valeurs = $valeur;
				$valeur = 'Array !';
			}
			$valeurs = array_unique(array_filter($valeurs));
			if (!$valeurs) {
				continue;
			}

			$filter += array(
				'select_oui'  => '',
				'select_null' => '',
			);

			// préparer les données
			$valeur = $this->quote($valeur);
			$valeurs = array_map(array($this, 'quote'), $valeurs);
			$valeurs = implode(', ', $valeurs);

			if (($valeur == '-') and $filter['select_null']) {
				$f = $filter['select_null'];
			} elseif ($filter['select_oui']) {
				$f = $filter['select_oui'];
			}

			// remplacer d'abord le pluriel !
			$f = str_replace(array('@valeurs', '@valeur'), array($valeurs, $valeur), $f);
			$this->queryApi->select("($f) AS f$nb");
			$this->queryApi->where("f$nb = 1");
			$nb++;
		}
	}

	/**
	 * Revenir au depart
	 * @return void
	 */
	public function rewind() {
		reset($this->result);
		list($this->cle, $this->valeur) = each($this->result);
	}

	/**
	 * L'iterateur est-il encore valide ?
	 * @return bool
	 */
	public function valid(){
		return !is_null($this->cle);
	}

	/**
	 * Retourner la valeur
	 * @return null
	 */
	public function current() {
		return $this->valeur;
	}

	/**
	 * Retourner la cle
	 * @return null
	 */
	public function key() {
		return $this->cle;
	}

	/**
	 * Passer a la valeur suivante
	 * @return void
	 */
	public function next(){
		if ($this->valid())
			list($this->cle, $this->valeur) = each($this->result);
	}

	/**
	 * Compter le nombre total de resultats
	 * @return int
	 */
	public function count() {
		if (is_null($this->total))
			$this->total = count($this->result);
	  return $this->total;
	}

}


/**
 * Transmettre la source (l'index sphinx) désirée
 * @param string $idb
 * @param object $boucles
 * @param object $crit
 */
function critere_SPHINX_index_dist($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];
	// critere unique
	$boucle->hash .= "\n\t" . '$command[\'index\'] = array();';

	foreach ($crit->param as $param){
		$boucle->hash .= "\n\t" . '$command[\'index\'][] = '.calculer_liste($param, array(), $boucles, $boucles[$idb]->id_parent).';';
	}
}

/**
 * Transmettre la recherche (le match fulltext) désiréé
 * @param string $idb
 * @param object $boucles
 * @param object $crit
 */
function critere_SPHINX_recherche_dist($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];
	// critere unique
	$boucle->hash .= "\n\t" . '$command[\'recherche\'] = array();';

	foreach ($crit->param as $param){
		$boucle->hash .= "\n\t" . '$command[\'recherche\'][] = '.calculer_liste($param, array(), $boucles, $boucles[$idb]->id_parent).';';
	}
}


/**
 * Indiquer les sélections de la requête
 *
 * @param string $idb
 * @param object $boucles
 * @param object $crit
 */
function critere_SPHINX_select_dist($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];
	// critere multiple
	$boucle->hash .= "\n\tif (!isset(\$select_init)) { \$command['selection'] = array(); \$select_init = true; }\n";

	foreach ($crit->param as $param){
		$boucle->hash .= "\t\$command['selection'][] = "
				. calculer_liste($param, array(), $boucles, $boucles[$idb]->id_parent) . ";\n";
	}
}


/**
 * Indiquer les snippets de la requête
 *
 * @param string $idb
 * @param object $boucles
 * @param object $crit
 */
function critere_SPHINX_snippet_dist($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];
	// critere multiple
	$boucle->hash .= "\n\tif (!isset(\$snippet_init)) { \$command['snippet'] = array(); \$snippet_init = true; }\n";

	$boucle->hash .= "\t\$command['snippet'][] = [\n"
		. (isset($crit->param[0]) ? "\t\t'champ'  => ". calculer_liste($crit->param[0], array(), $boucles, $boucles[$idb]->id_parent) . ",\n" : '')
		. (isset($crit->param[1]) ? "\t\t'phrase' => ". calculer_liste($crit->param[1], array(), $boucles, $boucles[$idb]->id_parent) . ",\n" : '')
		. (isset($crit->param[2]) ? "\t\t'limit'  => ". calculer_liste($crit->param[2], array(), $boucles, $boucles[$idb]->id_parent) . ",\n" : '')
		. (isset($crit->param[3]) ? "\t\t'as'     => ". calculer_liste($crit->param[3], array(), $boucles, $boucles[$idb]->id_parent) . "\n"  : '')
		. "\t];\n";
}



/**
 * Indiquer les facets de la requête
 *
 * @param string $idb
 * @param object $boucles
 * @param object $crit
 */
function critere_SPHINX_facet_dist($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];
	// critere multiple
	$boucle->hash .= "\n\tif (!isset(\$facet_init)) { \$command['facet'] = array(); \$facet_init = true; }\n";

	$boucle->hash .= "\t\$command['facet'][] = [\n"
		. (isset($crit->param[0]) ? "\t\t'alias'  => ". calculer_liste($crit->param[0], array(), $boucles, $boucles[$idb]->id_parent) . ",\n" : '')
		. (isset($crit->param[1]) ? "\t\t'query' => ". calculer_liste($crit->param[1], array(), $boucles, $boucles[$idb]->id_parent) . ",\n" : '')
		. "\t];\n";
}



/**
 * Indiquer les filtres de la requête
 *
 * @param string $idb
 * @param object $boucles
 * @param object $crit
 */
function critere_SPHINX_select_filter_dist($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];
	// critere multiple
	$boucle->hash .= "\n\tif (!isset(\$sfilter_init)) { \$command['select_filter'] = array(); \$sfilter_init = true; }\n";

	$boucle->hash .= "\t\$command['select_filter'][] = [\n"
		. (isset($crit->param[0]) ? "\t\t'valeur'      => ". calculer_liste($crit->param[0], array(), $boucles, $boucles[$idb]->id_parent) . ",\n" : '')
		. (isset($crit->param[1]) ? "\t\t'select_oui'  => ". calculer_liste($crit->param[1], array(), $boucles, $boucles[$idb]->id_parent) . ",\n" : '')
		. (isset($crit->param[2]) ? "\t\t'select_null' => ". calculer_liste($crit->param[2], array(), $boucles, $boucles[$idb]->id_parent) . ",\n" : '')
		. "\t];\n";
}



/**
 * Tris `{par x}`
 *
 * @param string $idb
 * @param object $boucles
 * @param object $crit
 */
function critere_SPHINX_par_dist($idb, &$boucles, $crit) {
	return critere_SPHINX_parinverse($idb, $boucles, $crit);
}

/**
 * Tris `{inverse}`
 *
 * @param string $idb
 * @param object $boucles
 * @param object $crit
 */
function critere_SPHINX_inverse_dist($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];
	if ($crit->not) {
		critere_SPHINX_parinverse($idb, $boucles, $crit);
	} else {
		// sinon idem parent.
		critere_inverse_dist($idb, $boucles, $crit);
	}
}

/**
 * Gestion des critères `{par}` et `{inverse}`
 *
 * @note
 *     Sphinx doit toujours avoir le sens de tri (ASC ou DESC).
 *
 *     Version simplifié du critère natif de SPIP, avec une permission
 *     pour les champs de type json `properties.truc`
 *
 * @param string $idb
 * @param object $boucles
 * @param object $crit
**/
function critere_SPHINX_parinverse($idb, $boucles, $crit, $sens = '') {
	$boucle = &$boucles[$idb];
	if ($crit->not) {
		$sens = $sens ? "" : " . ' DESC'";
	}

	foreach ($crit->param as $tri){
		$order = "";

		// tris specifies dynamiquement
		if ($tri[0]->type!='texte'){
			// calculer le order dynamique qui verifie les champs
			$order = calculer_critere_arg_dynamique($idb, $boucles, $tri, $sens);
		} else {
			$par = array_shift($tri);
			$par = $par->texte;
			$order = "'$par'";
		}


		$t = $order.$sens;
		$boucle->order[] = $t;
	}
}



