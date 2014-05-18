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
	private $snippet_words = [];

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
		if (!is_array($api)) {
			return false;
		}

		$ok = true;
		foreach (['index', 'select', 'fulltext', 'snippet', 'filters', /*'orders', 'facet'*/] as $cle) {
			if (isset($api[$cle])) {
				$methodApi = 'setApi' . ucfirst($cle);
				$ok &= $this->$methodApi($api);
			}
		}
		return $ok;
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
	public function setApiIndex($api) {
		if (!isset($api['index'])) {
			return false;
		}
		// Always work with an array of values
		if (!is_array($api['index'])) {
			$api['index'] = [$api['index']];
		}
		foreach ($api['index'] as $index){
			$this->from($index);
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
	public function setApiSelect($api) {
		if (!isset($api['select'])) {
			return false;
		}
		// Always work with an array of values
		if (!is_array($api['select'])){
			$api['select'] = [$api['select']];
		}
		foreach ($api['select'] as $select){
			$this->select($select);
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
	public function setApiFulltext($api) {
		// Fulltext search string (optional)
		if (!isset($api['fulltext']) OR !is_string($api['fulltext'])) {
			return false;
		}

		$this->where('MATCH(' . $this->quote($api['fulltext']) . ')');
		// add the score
		$this->select('WEIGHT() as score');
		$this->add_snippet_words($api['fulltext']);
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
	public function setApiSnippet($api) {

		if (!isset($api['snippet']) or !is_array($api['snippet'])) {
			return false;
		}
		if (isset($api['snippet']['words']) and is_string($api['snippet']['words'])){
			$this->add_snippet_words($api['snippet']['words']);
		}

		// If there is fulltext and/or an other words declaration, generate a snippet
		if (!$words = $this->get_snippet_words()) {
			return false;
		}

		$field = isset($api['snippet']['field']) ? $api['snippet']['field'] : 'content';
		$limit = isset($api['snippet']['limit']) ? $api['snippet']['limit'] : 200;

		$this->generate_snippet($field, $words, $limit);
		return true;
	}


	/**
	 * Ajoute des mots pour la sélection de snippet
	 *
	 * @param string $words Mots à ajouter
	 * @return bool True si au moins un mot présent, false sinon.
	**/
	public function add_snippet_words($words) {
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
	public function get_snippet_words() {
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
	public function setApiFilters($api) {

		if (!isset($api['filters']) or !is_array($api['filters'])) {
			return false;
		}

		foreach ($api['filters'] as $filter) {
			if (!is_array($filter) or !isset($filter['type'])) {
				continue;
			}
			switch ($filter['type']) {
				case 'mono':
					$this->setFilterMono($api, $filter);
					break;
				case 'multi_json':
					$this->setFilterMultiJson($api, $filter);
					break;
			}
		}
		return true;
	}


	public function setFilterMono($api, $filter) {
		if (
			($filter['type'] != 'mono')
			or !isset($filter['field'])
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
			$filter['values'] = [$filter['values']];
		}

		// For each values, we build a comparison
		$comparisons = array();
		foreach ($filter['values'] as $value){
			$comparison = $filter['field'] . $filter['comparison'] . $this->quote($value);
			if ($filter['not']){
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


	public function setFilterMultiJson($api, $filter) {

		static $as_count = 0;

		// Multi value JSON
		if (
			($filter['type'] != 'multi_json')
			or !isset($filter['field'])
			or !is_string($filter['field']) // mandatory
			or !isset($filter['values']) // mandatory
		){
			return false;
		}

		// Always work with an array of values
		if (!is_array($filter['values'])){
			$filter['values'] = [[$filter['values']]];
		}

		// At depth 1, generate AND
		$ins = [];
		foreach ($filter['values'] as $values_in){
			// Always work with an array of values
			if (!is_array($values_in)){
				$values_in = [$values_in];
			}
			$ins[] = 'IN(' . $filter['field'] . ', ' . join(', ', array_map([$this, 'quote'], array_filter($values_in))) . ')';
		}

		if ($ins){
			$this->select('(' . join(' AND ', $ins) . ') as select_'.$as_count);
			$this->where('select_'.$as_count . '=' . ($filter['not'] ? '0' : '1'));
			$as_count++;
		}

	}


	public function generate_snippet($field, $words='', $limit=200){
		if ($words){
			$limit = intval($limit);
			$this->select('snippet(' . $field . ', ' . $this->quote($words) . ", 'limit=$limit') as snippet");
		}
	}
}
