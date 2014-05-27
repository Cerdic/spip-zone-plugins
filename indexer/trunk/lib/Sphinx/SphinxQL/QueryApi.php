<?php

namespace Sphinx\SphinxQL;



/**
 * Crée une requête Sphinx à partir d'un tableau de description spécifique
 *
	```
	// exemple de description
	array(
		'index' => 'visites',
		'select' => array('date', 'properties', '*', 'etc'),
		'fulltext' => 'ma recherche',
		'snippet' => array(
			'words' => 'un mot',
			'field' => 'content',
			'limit' => 200,
		),
		'filters' => array(
			array(
				'type' => 'mono',
				'field' => 'properties.lang',
				'values' => array('fr'),
				'comparison' => '!=', // default : =
			),
			array(
				'type' => 'multi_json',
				'field' => 'properties.tags',
				'values' => array('pouet', 'glop'),
			),
			array(
				'type' => 'distance',
				'center' => array(
					'lat' => 44.837862,
					'lon' => -0.580086,
				),
				'fields' => array(
					'lat' => 'properties.geo.lat',
					'lon' => 'properties.geo.lon',
				),
				'distance' => 10000,
				'comparison' => '>', // default : <=
			),
			array(
				'type' => 'interval',
				'expression' => 'uint(properties.truc)',
				'intervals' => array(1,2,3,4,5),
				'field' => 'truc',
				'test' => 'truc = 2',
				'select' => 'interval(uint(properties.truc),1,2,3,4)',
				'where' => 'test = 2',
			),
		),
		'orders' => array(
			array(
				'field' => 'score',
				'direction' => 'asc', // default : desc
			),
			array(
				'field' => 'distance',
				'center' => array(
					'lat' => 44.837862,
					'lon' => -0.580086,
				),
				'fields' => array(
					'lat' => 'properties.geo.lat',
					'lon' => 'properties.geo.lon',
				),
			),
		),
		'facet' => array(
			'field' => 'properties.tags',
			'group_name' => 'tag',
			'order' => 'tag asc', // default : count desc
		),
	);
	```

**/
class QueryApi extends Query {

	/** @var string[] liste des mots pour le snippet */
	private $snippet_words = array();

	/**
	 * Crée une description de requête Sphinx à partir d'un tableau d'API
	 *
	 * Se reporter aux méthodes spécifiques pour les précisions sur l'API.
	 * Les clés du tableau peuvent être :
	 *
	 * - index
	 * - select
	 * - fulltext
	 * - snippet
	 * - filters
	 *
	 * À faire :
	 *
	 * - orders
	 * - facet
	 *
	 * @param array $api
	 *     API suivant une certaine norme
	 * @return void
	**/
	public function __construct($api=array()) {
		if (!empty($api)){
			$this->api2query($api);
		}
	}

	/**
	 * Transforme un tableau d'API en requête Sphinx structurée
	 *
	 * @param array $api
	 * @return bool True si tout s'est bien passé, false sinon.
	**/
	public function api2query($api) {
		$ok = true;
		
		if (!is_array($api)) {
			$ok = false;
		}
		
		// Si une clé reconnue existe dans la description demandée, on applique la méthode adaptée
		foreach (array('index', 'select', 'fulltext', 'snippet', 'filters', /*'orders', 'facet'*/) as $cle) {
			if (isset($api[$cle])) {
				$methodApi = 'setApi' . ucfirst($cle);
				$ok &= $this->$methodApi($api[$cle]);
			}
		}
		
		return $ok;
	}
	
	/**
	 * Ajoute des mots pour la sélection de snippet
	 *
	 * @param string $words Mots à ajouter
	 * @return bool True si au moins un mot présent, false sinon.
	**/
	public function addSnippetWords($words) {
		$words = trim($words);
		if (!strlen($words)) {
			return false;
		}
		$this->snippet_words[] = $words;
		return true;
	}
	
	/**
	 * Extrait et retourne les mots pertinents d'une phrase pour un snippet
	 *
	 * @return string Mots séparés par espace.
	**/
	public function getSnippetWords() {
		$phrase = implode(' ', $this->snippet_words);

		// extraction des mots (évitons les opérateurs, guillements…)
		preg_match_all('/\w+/u', $phrase, $mots);
		#var_dump($phrase, $mots);
		$mots = array_filter($mots[0], function($m) {
			// nombres >= 4 chiffres
			if (is_numeric($m)) {
				return (strlen($m) >= 4);
			}
			// mots >= 3 lettres
			return (strlen($m) >= 3);
		});
		return implode(' ', $mots);
	}
	
	/**
	 * Génére le bon select pour produire un snippet suivant un champ et des mots
	 *
	 * @param string $field Nom du champ (ou de la combinaison de champs) pour chercher les mots
	 * @param string $words='' Chaîne contenant les mots à mettre en gras
	 * @param int $limit=200 Limite facultative (200 par défaut)
	 * @return void
	 */
	public function generateSnippet($field, $words='', $limit=200){
		if ($words){
			$limit = intval($limit);
			$this->select('snippet(' . $field . ', ' . $this->quote($words) . ", 'limit=$limit') as snippet");
		}
	}
	
	/**
	 * Définit l'index de la requête.
	 *
	 * Utilise la clé 'index' du tableau d'API
	 *
	 *     ```
	 *     'index' => 'visites'
	 *     'index' => ['visites', 'autre']
	 *     ```
	 *
	 * @param array $api Tableau de description
	 * @return bool True si index présent.
	**/
	public function setApiIndex($index) {
		if (!$index){ return false; }
		
		// Always work with an array of values
		if (!is_array($index)) {
			$index = array($index);
		}
		foreach ($index as $i){
			$this->from($i);
		}
		
		return true;
	}

	/**
	 * Définit le select de la requête.
	 *
	 * Utilise la clé 'select' du tableau d'API
	 *
	 *     ```
	 *     'select' => array('date', 'properties', '*', 'etc'),
	 *     ```
	 *
	 * @param array $api Tableau de description
	 * @return bool True si select présent.
	**/
	public function setApiSelect($select) {
		if (!$select){ return false; }
		
		// Always work with an array of values
		if (!is_array($select)){
			$select = array($select);
		}
		foreach ($select as $s){
			$this->select($s);
		}
		
		return true;
	}

	/**
	 * Définit le fulltext (match) de la requête.
	 *
	 * Utilise la clé 'fulltext' du tableau d'API
	 *
	 *     ```
	 *     'fulltext' => 'ma recherche',
	 *     ```
	 *
	 * @param array $api Tableau de description
	 * @return bool True si fulltext présent.
	**/
	public function setApiFulltext($fulltext) {
		if (!is_string($fulltext)) { return false; }
		
		// Add the condition in where
		$this->where('MATCH(' . $this->quote($fulltext) . ')');
		// Add the score
		$this->select('WEIGHT() as score');
		// Add to snippet
		$this->addSnippetWords($fulltext);
		
		return true;
	}

	/**
	 * Définit un snippet pour la requête.
	 *
	 * Utilise la clé 'snippet' du tableau d'API
	 *
	 * Un snippet est créé dès qu'un mot est connu,
	 * notamment avec la valeur de la clé 'fulltext'.
	 *
	 * Si la clé snippet n'est pas précisée, les valeurs par défaut
	 * sont appliquées.
	 *
	 *     ```
	 *    'snippet' => array(
	 *        'words' => 'un mot',  // optionnel
	 *        'field' => 'content', // optionnel
	 *        'limit' => 200,       // optionnel
	 *     ),
	 *     ```
	 *
	 * @param array $api Tableau de description
	 * @return bool True si snippet ajouté, false sinon.
	**/
	public function setApiSnippet($snippet) {
		if (isset($snippet['words']) and is_string($snippet['words'])){
			$this->addSnippetWords($snippet['words']);
		}

		// If there is fulltext and/or an other words declaration, generate a snippet
		if (!$words = $this->getSnippetWords()) {
			return false;
		}
		
		// Default values
		$field = isset($snippet['field']) ? $snippet['field'] : 'content';
		$limit = isset($snippet['limit']) ? $snippet['limit'] : 200;
		
		// Add the snippet in select
		$this->generateSnippet($field, $words, $limit);
		
		return true;
	}

	/**
	 * Définit les filtres pour la requête.
	 *
	 *     ```
	 *     'filters' => array(
	 *         array(
	 *             'type' => 'mono',
	 *             'field' => 'properties.lang',
	 *             'values' => array('fr'),
	 *             'comparison' => '!=', // default : =
	 *         ),
	 *         array(
	 *             'type' => 'multi_json',
	 *             'field' => 'properties.tags',
	 *             'values' => array('pouet', 'glop'),
	 *         ),
	 *         array(
	 *             'type' => 'distance',
	 *             'center' => array(
	 *                 'lat' => 44.837862,
	 *                 'lon' => -0.580086,
	 *             ),
	 *             'fields' => array(
	 *                 'lat' => 'properties.geo.lat',
	 *                 'lon' => 'properties.geo.lon',
	 *             ),
	 *             'distance' => 10000,
	 *             'comparison' => '>', // default : <=
	 *         ),
	 *         array(
	 *             'type' => 'interval',
	 *             'expression' => 'uint(properties.truc)',
	 *             'intervals' => array(1,2,3,4,5),
	 *             'field' => 'truc',
	 *             'test' => 'truc = 2',
	 *             'select' => 'interval(uint(properties.truc),1,2,3,4)',
	 *             'where' => 'test = 2',
	 *         ),
	 *     ),
	 *     ```
	 *
	 * @param array $api Tableau de description
	 * @return bool True si filtres ajouté, false sinon.
	**/
	public function setApiFilters($filters) {
		if (!is_array($filters)) { return false; }
		
		$ok = true;

		// For each type of filter, call the right method
		foreach ($filters as $filter) {
			if (is_array($filter) and isset($filter['type'])) {
				switch ($filter['type']) {
					case 'mono':
						$ok &= $this->setApiFilterMono($filter);
						break;
					case 'multi_json':
						$ok &= $this->setApiFilterMultiJson($filter);
						break;
				}
			}
		}
		
		return $ok;
	}
	
	/**
	 * Add a mono value filter
	 *
	 * @param array $filter Description of the filter
	 * @return bool Return true if the filter has been added
	 */
	public function setApiFilterMono($filter) {
		if (
			!isset($filter['field'])
			or !is_string($filter['field']) // mandatory
			or !isset($filter['values']) // mandatory
		){
			return false;
		}

		// Default comparison : =
		if (!isset($filter['comparison'])){
			$filter['comparison'] = '=';
		}

		// Always work with an array of values
		if (!is_array($filter['values'])){
			$filter['values'] = array($filter['values']);
		}

		// For each values, we build a comparison
		$comparisons = array();
		foreach ($filter['values'] as $value){
			$comparison = $filter['field'] . $filter['comparison'] . $this->quote($value);
			if (isset($filter['not']) and $filter['not']){
				$comparison = "!($comparison)";
			}
			$comparisons[] = $comparison;
		}
		if ($comparisons){
			$comparisons = implode(' OR ', $comparisons);
			$this->where($comparisons);
		}

		return true;
	}
	
	/**
	 * Add a multi values filter in JSON
	 *
	 * @param array $filter
	 * 		Description of the filter
	 * 		- field : the field to compare to
	 * 		- values : an array of values
	 * 			* in this array, all comparisons will be join with AND
	 * 			* if a value is itself an array, it uses an IN (so like an OR)
	 * @return bool
	 * 		Return true if the filter has been added
	 */
	public function setApiFilterMultiJson($filter) {
		static $as_count = 0;

		// Multi value JSON
		if (
			!isset($filter['field'])
			or !is_string($filter['field']) // mandatory
			or !isset($filter['values']) // mandatory
		){
			return false;
		}

		// Always work with an array of values
		if (!is_array($filter['values'])){
			$filter['values'] = array(array($filter['values']));
		}

		// At depth 1, generate AND
		$ins = array();
		foreach ($filter['values'] as $values_in){
			// Always work with an array of values
			if (!is_array($values_in)){
				$values_in = array($values_in);
			}
			$ins[] = 'IN(' . $filter['field'] . ', ' . join(', ', array_map(array($this, 'quote'), array_filter($values_in))) . ')';
		}

		if ($ins){
			$this->select('(' . join(' AND ', $ins) . ') as multi_'.$as_count);
			$this->where('multi_'.$as_count . '=' . ((isset($filter['not']) and $filter['not']) ? '0' : '1'));
			$as_count++;
		}
		
		return true;
	}
}
