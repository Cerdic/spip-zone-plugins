<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Gestion de l'itérateur SPHINX
 *
 * @package SPIP\Indexer\Iterateur\Sphinx
**/


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
	 * Résultats par la requête à Sphinx
	 * @var array
	 */
	protected $result = array();

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
	 * index(es) scannés
	 * @var array
	 */
	protected $index = array();

	/**
	 * Valeur courante
	 * @var null
	 */
	protected $valeur = null;

	/**
	 * Limite d'une pagination
	 * @var int
	**/
	protected $pagination_limit;

	/**
	 * Constructeur
	 *
	 * @param  $command
	 * @param array $info
	 */
	public function __construct($command, $info=array()) {

		$this->command = $command + array(
			'index'             => array(),
			'selection'         => array(),
			'recherche'         => array(),
			'orderby'           => array(),
			'group'             => array(),
			'snippet'           => array(),
			'options'           => array(),
			'facet'             => array(),
			'filter'            => array(),
			'filters_mono'      => array(),
			'filters_multijson' => array(),
			'filters_distance'  => array(),
			'pagination'        => array(),
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
		$this->setFiltersMono($this->command['filters_mono']);
		$this->setFiltersMultiJson($this->command['filters_multijson']);
		$this->setFiltersDistance($this->command['filters_distance']);

		$this->setSnippet($this->command);

		$this->setPagination($this->command['pagination']);
		$this->setOptions($this->command['options']);

		$this->runQuery();
	}


	/**
	 * Sauvegarde des données pour utilisation ultérieure
	 * dans les squelettes via les balises `#SPHINX_xx`
	 * où xx est la clé sauvegardée.
	 *
	 * @param string $cle
	 * @param mixed $val
	 * @return void
	**/
	private function save($cle, $val) {
		if (!isset($GLOBALS['SphinxSave'])) {
			$GLOBALS['SphinxSave'] = array();
		}
		// identifiant de la boucle
		$id = $this->command['id'];
		if (!isset($GLOBALS['SphinxSave'][$id])) {
			$GLOBALS['SphinxSave'][$id] = array();
		}
		$GLOBALS['SphinxSave'][$id][$cle] = $val;
	}

	/**
	 * Sauvegarde toutes les données pour utilisation ultérieure
	 *
	 * @param array $data
	 * @return void
	 */
	private function saveAll($data) {
		foreach ($data as $cle => $val) {
			$this->save($cle, $val);
		}
	}

	/*
	 * Récupérer la forme exacte du mot à partir de
	 * la version indexée ; utilise snippet(query, racine)
	 */
	public function keyword2word($keyword, $q) {
		$u = $this->sphinxQL->allfetsel(
		"SELECT SNIPPET("._q($q).",". ($this->quote($keyword, 'string')).") AS m "
		. "FROM ". join(',', $this->index) ." LIMIT 1"
		);
		if (!$mot = supprimer_tags(extraire_balise($u['query']['docs'][0]['m'], 'b')))
			$mot = $keyword;
		return $mot;
	}

	/**
	 * Exécute la requête
	 *
	 * Exécute la requête, sauvegarde des données, retravaille
	 * les résultats pour que la pagination fonctionne.
	 *
	 * @param 
	 * @return 
	**/
	public function runQuery() {
		$q = $this->queryApi->getMatch();
		
		// on sait deja que cette requete necessite une correction ?
		if (isset($GLOBALS['sphinxReplace'][$q])) {
			$this->queryApi->match($GLOBALS['sphinxReplace'][$q]);
			$this->save('message', $GLOBALS['sphinxReplaceMessage'][$q]);
		}

		$query  = $this->queryApi->get();
		$this->save('query', $query);

		$result = $this->sphinxQL->allfetsel($query);

		// erreur de syntaxe ? correction de la requete
		// TODO : lever une erreur_squelette() comme en SQL ?
		if (isset($result['query']['meta']['error'])) {
			spip_log($result['query'], 'indexer');
			if (preg_match('/syntax error/', $result['query']['meta']['error'])) {
				$q = $this->queryApi->getMatch();
				$GLOBALS['sphinxReplace'][$q] = trim(preg_replace('/\W+/u', ' ', $q));
				$this->queryApi->match($GLOBALS['sphinxReplace'][$q]);
				$query  = $this->queryApi->get();
				$result = $this->sphinxQL->allfetsel($query);
				$message = _L('transformation de la requête en « ').htmlspecialchars($GLOBALS['sphinxReplace'][$q])." »";
				$GLOBALS['sphinxReplaceMessage'][$q] = $message;
				$this->save('message', $message);
			}
		}

		if (!$result) {
			return false;
		}

		// resultat vide et plusieurs mots dont certains ont 0 hit ?
		if (is_array($result['query']['docs'])
		AND count($result['query']['docs']) == 0
		AND !preg_match('/["\/&|)(]/u', $q)
		) {
			$q2 = $msg = array();
			if (isset($result['query']['meta']['keywords'])){
				foreach($result['query']['meta']['keywords'] as $w) {
					$mot = $this->keyword2word($w['keyword'], $q);
					if($w['docs'] == 0) {
						$msg[] = "<del>".htmlspecialchars($mot)."</del>";
					} else {
						$msg[] = htmlspecialchars($mot);
						$q2[] = $mot;
					}
				}
			}

			if (count($q2) >0
			AND count($q2) < count($result['query']['meta']['keywords'])) {
				$q2 = trim(join(' ',$q2));
				$GLOBALS['sphinxReplace'][$q] = trim(preg_replace('/\W+/u', ' ', $q2));
				$this->queryApi->match($GLOBALS['sphinxReplace'][$q]);
				$query  = $this->queryApi->get();
				$result = $this->sphinxQL->allfetsel($query);
				$GLOBALS['sphinxReplace'][$q] = $q2;
				$GLOBALS['sphinxReplaceMessage'][$q] = $message = _L('Résultats pour : ').join(' ',$msg);
				$this->save('message', $message);
			}
		}

		// expérimental : utiliser aspell
		// pour chercher des suggestions de mots-clés
		// define('_INDEXER_SUGGESTIONS', 5);
		// define('ASPELL_BIN', '/usr/local/bin/aspell');
		if (defined('_INDEXER_SUGGESTIONS') AND _INDEXER_SUGGESTIONS) {
			$max_suggestions = _INDEXER_SUGGESTIONS;
			if (isset($result['query']['meta']['keywords'])){
				foreach($result['query']['meta']['keywords'] as $w) {
					// un mot inexistant ou rare
					// est possiblement mal orthographié
					if($w['docs'] <= 2) {
						$mot = $this->keyword2word($w['keyword'], $q);
						$suggests = indexer_suggestions_motivees($mot);
						if (is_array($suggests) && count($suggests)>0) {
							$liens_suggestion = array();
							// on prend n suggestions
							spip_log($suggests, 'indexer');
							foreach(array_slice($suggests, 0, $max_suggestions) as $sug) {

								$rech = preg_replace('/'.$mot.'/i', $sug, $q);

								// tester si la requete modifiée donne plus de resultats ?
								// attention à ne pas creer de boucle infernale

								$url = parametre_url(self(), 'recherche', $rech);
								$liens_suggestion[] = inserer_attribut('<a rel="nofollow">'.$sug.'</a>', 'href', $url);
							}
						}
						if (count($liens_suggestion)>0) {
							$message .= '<div class="indexer_suggestions">'._L('Suggestion : ') .join(', ', $liens_suggestion)."</div>";
							$GLOBALS['sphinxReplaceMessage'][$q] = $message;
							$this->save('message', $message);
							$this->save('suggestions', $liens_suggestion);
						}
					}
				}
			}
		}

		// decaler les docs en fonction de la pagination demandee
		if (is_array($result['query']['docs'])
		AND $pagination = $this->getPaginationLimit()) { 

			list($debut) = array_map('intval', $pagination); 

			$result['query']['docs'] = array_pad($result['query']['docs'], - count($result['query']['docs']) - $debut, null);
			$result['query']['docs'] = array_pad($result['query']['docs'], $result['query']['meta']['total'], null);
		}

		// remettre les alias sur les facettes :
		// {facet truc, FORMULE()} cree la facette 'truc'
		$facets = array();
		foreach ($this->facet as $f) {
			$facets[$f['alias']] = array_shift($result['query']['facets']);
		}
		$result['query']['facets'] = $facets;


		$this->result = $result['query'];
		unset($result['query']['docs']);
		$this->saveAll($result['query']);

		return true;
	}


	public function quote($m, $type=null) {
		return $this->queryApi->quote($m, $type);
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
		$this->index = $index;
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
	 * Définir les éventuelles options (poids des champs etc)
	 *
	 * @param array $options
	 * @return bool True s'il y a au moins une option
	 */
	public function setOptions($options){
		if (is_array($options) and $options){
			foreach ($options as $nom=>$option){
				// Si la clé est bien un nom d'option, càd pas un nombre
				if (!is_numeric($nom) and is_string($option)){
					$this->queryApi->option($nom . '=' . $option);
				}
			}
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
		$this->queryApi->select("SNIPPET($desc[champ], " . $this->quote($desc['phrase'], 'string') . ", 'limit=$desc[limit]','html_strip_mode=strip') AS $desc[as]");
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
			if (is_array($valeur)) {
				$valeurs = $valeur;
				$valeur = 'Array !';
			} else {
				$valeur = trim($valeur);
				$valeurs = array($valeur);
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
	
	function setFiltersMono($filters){
		$filters = array_filter($filters);
		if (!$filters) {
			return false;
		}
		
		$ok = true;
		foreach ($filters as $filter){
			$ok &= $this->queryApi->setApiFilterMono($filter);
		}
		
		return $ok;
	}
	
	function setFiltersMultiJson($filters){
		$filters = array_filter($filters);
		if (!$filters) {
			return false;
		}
		
		$ok = true;
		foreach ($filters as $filter){
			$ok &= $this->queryApi->setApiFilterMultiJson($filter);
		}
		
		return $ok;
	}
	
	function setFiltersDistance($filters){
		$filters = array_filter($filters);
		if (!$filters) {
			return false;
		}
		
		$ok = true;
		foreach ($filters as $filter){
			$ok &= $this->queryApi->setApiFilterDistance($filter);
		}
		
		return $ok;
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
function critere_SPHINX_index_dist($idb, &$boucles, $crit) {
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
 * Indiquer les group by de la requête
 *
 * @param string $idb
 * @param object $boucles
 * @param object $crit
 */
function critere_SPHINX_groupby_dist($idb, &$boucles, $crit) {
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
 * Ajouter une option à la requête
 *
 * @param string $idb
 * @param object $boucles
 * @param object $crit
 */
function critere_SPHINX_option_dist($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];
	// critere multiple
	$boucle->hash .= "\n\tif (!isset(\$options_init)) { \$command['options'] = array(); \$options_init = true; }\n";
	
	// Il faut deux paramètres : le nom et l'option
	if (isset($crit->param[0]) and isset($crit->param[1])) {
		$nom = calculer_liste($crit->param[0], array(), $boucles, $boucles[$idb]->id_parent);
		$option = calculer_liste($crit->param[1], array(), $boucles, $boucles[$idb]->id_parent);
		
		$boucle->hash .= "\t\$command['options'][$nom] = $option;\n";
	}
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
function critere_SPHINX_filter_dist($idb, &$boucles, $crit) {
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
 * Indiquer les filtres mono-valués de la requête
 *
 * @param string $idb
 * @param object $boucles
 * @param object $crit
 */
function critere_SPHINX_filtermono_dist($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];
	
	if (isset($crit->param[0])) {
		$test = calculer_liste($crit->param[0], array(), $boucles, $boucles[$idb]->id_parent);
	}
	if (isset($crit->param[1])) {
		$field = calculer_liste($crit->param[1], array(), $boucles, $boucles[$idb]->id_parent);
	}
	if (isset($crit->param[2])) {
		$values = calculer_liste($crit->param[2], array(), $boucles, $boucles[$idb]->id_parent);
	}
	if (isset($crit->param[3])) {
		$comparison = calculer_liste($crit->param[3], array(), $boucles, $boucles[$idb]->id_parent);
	}
	if (isset($crit->param[4])) {
		$type = calculer_liste($crit->param[4], array(), $boucles, $boucles[$idb]->id_parent);
	}
	
	// Test
	$boucle->hash .= "\n\tif ($test) {\n";
	
	// Critere multiple
	$boucle->hash .= "\t\tif (!isset(\$filters_mono_init)) { \$command['filters_mono'] = array(); \$filters_mono_init = true; }\n";

	$boucle->hash .= "\t\t\$command['filters_mono'][] = array(\n"
		. (isset($crit->param[1]) ? "\t\t\t'field'       => $field,\n" : '')
		. (isset($crit->param[2]) ? "\t\t\t'values'      => $values,\n" : '')
		. (isset($crit->param[3]) ? "\t\t\t'comparison'  => $comparison,\n" : '')
		. (isset($crit->param[4]) ? "\t\t\t'type'        => $type,\n" : '')
		. "\t\t);\n";
	
	// Fin de test
	$boucle->hash .= "\t}\n";
}

/**
 * Indiquer les filtres multi-valués JSON de la requête
 *
 * @param string $idb
 * @param object $boucles
 * @param object $crit
 */
function critere_SPHINX_filtermultijson_dist($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];
	
	if (isset($crit->param[0])) {
		$test = calculer_liste($crit->param[0], array(), $boucles, $boucles[$idb]->id_parent);
	}
	if (isset($crit->param[1])) {
		$field = calculer_liste($crit->param[1], array(), $boucles, $boucles[$idb]->id_parent);
	}
	if (isset($crit->param[2])) {
		$values = calculer_liste($crit->param[2], array(), $boucles, $boucles[$idb]->id_parent);
	}
	if (isset($crit->param[3])) {
		$type = calculer_liste($crit->param[3], array(), $boucles, $boucles[$idb]->id_parent);
	}
	
	// Test
	$boucle->hash .= "\n\tif ($test) {\n";
	
	// Critere multiple
	$boucle->hash .= "\t\tif (!isset(\$filters_multijson_init)) { \$command['filters_multijson'] = array(); \$filters_multijson_init = true; }\n";

	$boucle->hash .= "\t\t\$command['filters_multijson'][] = array(\n"
		. (isset($crit->param[1]) ? "\t\t\t'field'       => $field,\n" : '')
		. (isset($crit->param[2]) ? "\t\t\t'values'      => $values,\n" : '')
		. (isset($crit->param[3]) ? "\t\t\t'type'        => $type,\n" : '')
		. ($crit->not ? "\t\t\t'not'                     => 'true',\n" : '')
		. "\t\t);\n";
	
	// Fin de test
	$boucle->hash .= "\t}\n";
}

/**
 * Indiquer les filtres de distance de la requête
 *
 * @param string $idb
 * @param object $boucles
 * @param object $crit
 */
function critere_SPHINX_filterdistance_dist($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];
	
	if (isset($crit->param[0])) {
		$test = calculer_liste($crit->param[0], array(), $boucles, $boucles[$idb]->id_parent);
	}
	if (isset($crit->param[1])) {
		$point1 = calculer_liste($crit->param[1], array(), $boucles, $boucles[$idb]->id_parent);
	}
	if (isset($crit->param[2])) {
		$point2 = calculer_liste($crit->param[2], array(), $boucles, $boucles[$idb]->id_parent);
	}
	if (isset($crit->param[3])) {
		$distance = calculer_liste($crit->param[3], array(), $boucles, $boucles[$idb]->id_parent);
	}
	if (isset($crit->param[4])) {
		$comparison = calculer_liste($crit->param[4], array(), $boucles, $boucles[$idb]->id_parent);
	}
	if (isset($crit->param[5])) {
		$as = calculer_liste($crit->param[5], array(), $boucles, $boucles[$idb]->id_parent);
	}
	
	// Test
	$boucle->hash .= "\n\tif ($test) {\n";
	
	// Critere multiple
	$boucle->hash .= "\t\tif (!isset(\$filters_distance_init)) { \$command['filters_distance'] = array(); \$filters_distance_init = true; }\n";

	$boucle->hash .= "\t\t\$command['filters_distance'][] = array(\n"
		. (isset($crit->param[1]) ? "\t\t\t'point1'      => $point1,\n" : '')
		. (isset($crit->param[2]) ? "\t\t\t'point2'      => $point2,\n" : '')
		. (isset($crit->param[3]) ? "\t\t\t'distance'    => $distance,\n" : '')
		. (isset($crit->param[4]) ? "\t\t\t'comparison'  => $comparison,\n" : '')
		. (isset($crit->param[5]) ? "\t\t\t'as'          => $as,\n" : '')
		. "\t\t);\n";
	
	// Fin de test
	$boucle->hash .= "\t}\n";
}

/**
 * Pagination
 *
 * @param string $idb
 * @param object $boucles
 * @param object $crit
 */
function critere_SPHINX_pagination_dist($idb, &$boucles, $crit) {
	$boucle = &$boucles[$idb];
	// critere unique
	$boucle->hash .= 	"\t\$command['pagination'] = array("
		. "intval(@\$Pile[0]['debut".$idb."']),"
		. (isset($crit->param[0]) ? calculer_liste($crit->param[0], array(), $boucles, $boucles[$idb]->id_parent) : '0')
		. ");\n";

	// appliquer enfin le critere {pagination} normal
	return critere_pagination_dist($idb, $boucles, $crit);
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
	$p->code = '(isset($GLOBALS["SphinxSave"]["'.$b.'"]["'.$champ.'"]) ? $GLOBALS["SphinxSave"]["'.$b.'"]["'.$champ.'"] : "")';

	$p->interdire_scripts = false;
	return $p;
}
