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
function iterateur_SPHINX2_dist($b) {
	$b->iterateur = 'SPHINX2'; # designe la classe d'iterateur
	$b->show = array(
		'field' => array(
			'*' => 'ALL' // Champ joker *
		)
	);
	return $b;
}


/**
 * Iterateur SPHINX pour itérer sur des données
 *
 * La boucle SPHINX n'a toujours qu'un seul élément.
 */
class IterateurSPHINX2 implements Iterator {

	/**
	 * Type de l'iterateur
	 * @var string
	 */
	protected $type = 'SPHINX2';

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
	 * Résultats par la requête à Sphinx
	 * @var array
	 */
	protected $result = array();

	/**
	 * Données de la requête (hors documents récupérés)
	 *
	 * ArrayObject pour avoir 1 seul objet non dupliqué
	 *
	 * @var ArrayObject
	 */
	protected $data = null;


	/**
	 * Cle courante
	 * @var null
	 */
	protected $cle = null;

	/**
	 * facettes
	 * @var array
	 */
	protected $facet = array();

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
			'orderby'   => array(),
			'group'     => array(),
			'snippet'   => array(),
			'facet'     => array(),
			'filter'    => array(),
		);


		$this->info = $info;

		include_spip('inc/indexer');

		$this->sphinxQL  = new \Sphinx\SphinxQL\SphinxQL(SPHINX_SERVER_HOST, SPHINX_SERVER_PORT);
		$this->queryApi  = new \Sphinx\SphinxQL\QueryApi();

		$this->setIndex($this->command['index']);
		$this->setSelection($this->command['selection']);
		$this->setMatch($this->command['recherche']);
		$this->setOrderBy($this->command['orderby']);
		$this->setGroupBy($this->command['group']); // groupby interfère avec spip :/
		$this->setFacet($this->command['facet']);

		$this->setFilter($this->command['filter']);

		$this->setSnippet($this->command);


		$this->setPagination($this->command['pagination']);

		$this->runQuery();
	}


	public function runQuery() {
		$query  = $this->queryApi->get();
		$result = $this->sphinxQL->allfetsel($query);
		if (!$result) {
			return false;
		}

		// decaler les docs en fonction de la pagination demandee
		if (is_array($result['query']['docs'])
			AND $pagination = $this->getPaginationLimit()) { 

			list($debut) = array_map('intval', $pagination); 

			$result['query']['docs'] = array_pad($result['query']['docs'], - count($result['query']['docs']) - $debut, null);
			$result['query']['docs'] = array_pad($result['query']['docs'], $result['query']['meta']['total'], null);
		}

		$this->result = $result['query'];
		unset($result['query']['docs']);

		// remettre les alias sur les facettes :
		// {facet truc, FORMULE()} cree la facette 'truc'
		$facets = array();
		foreach ($this->facet as $f) {
			$facets[$f['alias']] = array_shift($result['query']['facets']);
		}
		$result['query']['facets'] = $facets;

		$this->data = new ArrayObject($result['query']);

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
	public function setMatch($match) {
		if (!is_array($match)) $match = array($match);
		$match = array_filter($match);
		if (!$match) {
			return false;
		}
		$match = implode(' ',$match);
		$this->queryApi
			->select('WEIGHT() AS score')
			->match( $match );
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

	public function setGroupby($groupby) {
		if (!is_array($groupby)) $groupby = array($groupby);
		$groupby = array_filter($groupby);
		if (!$groupby) {
			return false;
		}
		foreach ($groupby as $group) {
			$this->queryApi->groupby($group);
		}
		return true;
	}


	/** 
	* Affecte une limite à la requête Sphinx (et sauve ses bornes) 
	* 
	* @param int Début 
	* @param int Nombre de résultats 
	**/ 
	public function setPaginationLimit($debut, $nombre) { 
		$this->pagination_limit = array($debut, $nombre); 
		$this->queryApi->limit("$debut,$nombre"); 
	} 

	/** 
	* Retourne les limites de pagination précédemment sauvées 
	* 
	* @param int Début 
	* @param int Nombre de résultats 
	**/ 
	public function getPaginationLimit() { 
		return $this->pagination_limit; 
		# return explode(',', $this->queryApi->getLimit()); 
	}

	/**
	 * Définir la pagination
	 *
	 * @param array $pagination
	 * @return bool True si une pagination est demandee
	**/
	public function setPagination($pagination) {
		# {pagination 20}
		if (is_array($pagination) and $pagination) {
			$debut = intval($pagination[0]);
			$nombre = 20;
			if (isset($pagination[1])) {
				$nombre = intval($pagination[1]);
			}
			$this->setPaginationLimit($debut, $nombre); 
			return true;
		}
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

		$this->queryApi->addSnippetWords( $desc['phrase'] );
		$desc['phrase'] = $this->queryApi->getSnippetWords();

		if (!$desc['phrase'] OR !$desc['champ']) {
			return false;
		}
		$this->queryApi->select("SNIPPET($desc[champ], " . $this->quote($desc['phrase']) . ", 'limit=$desc[limit],html_strip_mode=strip') AS $desc[as]");
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
	public function setFilter($filters) {
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
			$aucun = ($valeur == '-'); // si aucun demandé 
			$valeur = $this->quote($valeur);
			$valeurs = array_map(array($this, 'quote'), $valeurs);
			$valeurs = implode(', ', $valeurs);

			if (($aucun == '-') and $filter['select_null']) {
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



	// pour #SPHINX_*, permet de récupérer tous les champs de metadata
	public function getMetaData() {
		return $this->data;
	}


	/**
	 * Revenir au depart
	 * @return void
	 */
	public function rewind() {
		if (!is_array($this->result['docs'])) return false;
		reset($this->result['docs']);
		list($this->cle, $this->valeur) = each($this->result['docs']);
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
		if ($this->valid()) {
			list($this->cle, $this->valeur) = each($this->result['docs']);
			// on transmet, pour chaque ligne les metas données avec…
			// histoire d'être certain qu'on les verra dans $Pile[$SP]
			// (feinte de sioux)
			if (is_array($this->valeur)) {
				$this->valeur['_sphinx_data'] = $this->data;
			}
		}
	}

	/**
	 * Compter le nombre total de resultats
	 * @return int
	 */
	public function count() {
		if (is_null($this->total))
			$this->total = count($this->result['docs']);
	  return $this->total;
	}

}


/**
 * Transmettre la source (l'index sphinx) désirée
 * @param string $idb
 * @param object $boucles
 * @param object $crit
 */
function critere_SPHINX2_index_dist($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];
	// critere unique
	$boucle->hash .= "\n\t" . '$command[\'index\'] = array();';

	foreach ($crit->param as $param){
		$boucle->hash .= "\n\t" . '$command[\'index\'][] = '.calculer_liste($param, array(), $boucles, $boucles[$idb]->id_parent).';';
	}
}

/**
 * Transmettre la recherche (le match fulltext) désirée
 * @param string $idb
 * @param object $boucles
 * @param object $crit
 */
function critere_SPHINX2_recherche_dist($idb, &$boucles, $crit) {
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
function critere_SPHINX2_select_dist($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];
	// critere multiple
	$boucle->hash .= "\n\tif (!isset(\$select_init)) { \$command['selection'] = array(); \$select_init = true; }\n";

	foreach ($crit->param as $param){
		$boucle->hash .= "\t\$command['selection'][] = "
				. calculer_liste($param, array(), $boucles, $boucles[$idb]->id_parent) . ";\n";
	}
}


/**
 * Indiquer les group by de la requête
 *
 * @param string $idb
 * @param object $boucles
 * @param object $crit
 */
function critere_SPHINX2_groupby_dist($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];
	// critere multiple
	$boucle->hash .= "\n\tif (!isset(\$group_init)) { \$command['group'] = array(); \$group_init = true; }\n";

	foreach ($crit->param as $param){
		$boucle->hash .= "\t\$command['group'][] = "
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
function critere_SPHINX2_snippet_dist($idb, &$boucles, $crit) {
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
function critere_SPHINX2_facet_dist($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];
	// critere multiple
	$boucle->hash .= "\n\tif (!isset(\$facet_init)) { \$command['facet'] = array(); \$facet_init = true; }\n";

	$boucle->hash .= "\t\$command['facet'][] = array(\n"
		. (isset($crit->param[0]) ? "\t\t'alias'  => ". calculer_liste($crit->param[0], array(), $boucles, $boucles[$idb]->id_parent) . ",\n" : '')
		. (isset($crit->param[1]) ? "\t\t'query' => ". calculer_liste($crit->param[1], array(), $boucles, $boucles[$idb]->id_parent) . ",\n" : '')
		. "\t);\n";
}

/**
 * Indiquer les filtres de la requête
 *
 * @param string $idb
 * @param object $boucles
 * @param object $crit
 */
function critere_SPHINX2_filter_dist($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];
	// critere multiple
	$boucle->hash .= "\n\tif (!isset(\$sfilter_init)) { \$command['filter'] = array(); \$sfilter_init = true; }\n";

	$boucle->hash .= "\t\$command['filter'][] = array(\n"
		. (isset($crit->param[0]) ? "\t\t'valeur'      => ". calculer_liste($crit->param[0], array(), $boucles, $boucles[$idb]->id_parent) . ",\n" : '')
		. (isset($crit->param[1]) ? "\t\t'select_oui'  => ". calculer_liste($crit->param[1], array(), $boucles, $boucles[$idb]->id_parent) . ",\n" : '')
		. (isset($crit->param[2]) ? "\t\t'select_null' => ". calculer_liste($crit->param[2], array(), $boucles, $boucles[$idb]->id_parent) . ",\n" : '')
		. "\t);\n";
}



/**
 * Tris `{par x}`
 *
 * @param string $idb
 * @param object $boucles
 * @param object $crit
 */
function critere_SPHINX2_par_dist($idb, &$boucles, $crit) {
	return critere_SPHINX2_parinverse($idb, $boucles, $crit);
}

/**
 * Tris `{inverse}`
 *
 * @param string $idb
 * @param object $boucles
 * @param object $crit
 */
function critere_SPHINX2_inverse_dist($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];
	if ($crit->not) {
		critere_SPHINX2_parinverse($idb, $boucles, $crit);
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
function critere_SPHINX2_parinverse($idb, $boucles, $crit, $sens = '') {
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


/**
 * Récupère pour une balise `#SPHINX_QQC` la valeur de 'qqc'
 * dans les meta données associées à la requête
 *
 * - `#SPHINX_QUERY`
 * - `#SPHINX_META`
 * - `#SPHINX_FACETS`
 *
 * @param Champ $p
 * @return Champ
**/
function balise_SPHINX__dist($p){
	$champ = $p->nom_champ;
	if ($champ == 'SPHINX_') {
		$msg = _T('zbug_balise_sans_argument', array('balise' => ' SPHINX_'));
		erreur_squelette($msg, $p);
		$p->interdire_scripts = true;
		return $p;
	};
	$champ = substr($champ, 7);
	return calculer_balise_SPHINX_CHAMP($p, $champ);
}




/**
 * Récupère pour une balise `#SPHINX_QQC` la valeur de 'qqc'
 * dans les meta données associées à la requête.
 * 
 * On les puise soit directement dans l'iterateur si on l'a (partie centrale de boucle),
 * soit dans une globale (conjointement à l'utilisation de `#SPHINX_SAVE_META`
 *
 * @param Champ $p
 * @param string $champ
 * @return Champ
**/
function calculer_balise_SPHINX_CHAMP($p, $champ) {
	$b = $p->nom_boucle ? $p->nom_boucle : $p->descr['id_mere'];
	if ($b === '' || !isset($p->boucles[$b])) {
		$msg = array('zbug_champ_hors_boucle', array('champ' => '#SPHINX_' . $champ));
		erreur_squelette($msg, $p);
		$p->interdire_scripts = true;
		return $p;
	}

	$champ = strtolower($champ);
	$p->code =
		// iterateur présent ?
		"(isset(\$Iter) ? "
		. "((\$d = \$Iter->getIterator()->getMetaData()) ? \$d['$champ'] : '') : "
		// sinon sauvegarde de #SPHINX_SAVE_META
		. "(isset(\$GLOBALS['SphinxSave']['$b']['$champ']) ? \$GLOBALS['SphinxSave']['$b']['$champ'] : '') )";
	$p->interdire_scripts = false;
	return $p;
}

/**
 * Sauvegarde les meta données de requête Sphinx pour une
 * utilisation ultérieure dans les parties alternatives de la boucle…
 *
 * - `#SPHINX_SAVE_META`
 *
 * Permet l'usage dans 'avant' ou 'apres' des boucles Sphinx des
 * balises :
 *
 * - `#SPHINX_QUERY`
 * - `#SPHINX_META`
 * - `#SPHINX_FACETS`
 *
 * @param Champ $p
 * @return Champ
**/
function balise_SPHINX_SAVE_META_dist($p){
	$b = $p->nom_boucle ? $p->nom_boucle : $p->descr['id_mere'];
	// doit être dans la partie centrale de la boucle
	if ($b === '' || !isset($p->boucles[$b])) {
		$msg = array('zbug_champ_hors_boucle', array('champ' => '#SPHINX_SAVE_META' ));
		erreur_squelette($msg, $p);
		$p->interdire_scripts = true;
		return $p;
	}

	$p->code =
		  "(!isset(\$GLOBALS['SphinxSave']) ? vide(\$GLOBALS['SphinxSave'] = array()) : '') . "
		. "(!isset(\$GLOBALS['SphinxSave']['$b']) ? vide(\$GLOBALS['SphinxSave']['$b'] = \$iter->getInnerIterator()->getMetaData()) : '')"
		#. " . ('<pre>' . print_r( \$GLOBALS['SphinxSave'] , true) . '</pre>')"
		;
	$p->interdire_scripts = false;
	return $p;
}
