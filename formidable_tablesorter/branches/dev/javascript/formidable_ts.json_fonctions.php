<?php
namespace formidable_ts;
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}
include_spip('inc/sql');
include_spip('inc/saisies');
include_spip('inc/cextras');
include_spip('saisies/afficher_si_php');
/*
 * La classe principale
 * qui cherche les données en base
 * et retourne un tableau json si besoin
*/
class table {
	private $size;
	private $page;
	private $id_formulaire;
	private $filter;
	private $sort;
	private $rows;
	private $headers;
	private $saisies;
	private $saisies_finales;
	private $cextras;
	private $statut;
	private $totalRows;
	/**
	 * @param array $env
	 * L'env du squelette
	**/
	public function __construct($env) {
		$this->id_formulaire = sql_quote($env['id_formulaire'] ?? null);
		$this->totalRows=0;
		// Transformer les filtres en pseudo afficher_si
		$this->filter = $env['filter'] ?? array();
		if (!$this->filter) {// Peut être ''
			$this->filter = array();
		}
		$this->filter = array_map(function($a)  {
			if (!preg_match('#(=|>|<|MATCH)#', $a)) {
				$a = " MATCH '/$a/'";
			}
			return $a;
		},
			$this->filter
		);
		$this->sort = $env['sort'];
		if (!$this->sort){
			$this->sort = array();
		}
		$env['statut'] = $env['statut'] ?? null;
		$this->statut = \sql_quote($env['statut'] ? $env['statut'] : '.*');
		$saisies = \unserialize(sql_getfetsel('saisies', 'spip_formulaires', "id_formulaire=$this->id_formulaire"));
		$saisies = \saisies_identifier($saisies);// Au cas où
		$this->saisies = $saisies;
		$this->saisies_finales  = \saisies_lister_finales($saisies);
		if (\test_plugin_actif('cextras')) {
			include_spip('cextras_pipelines');
			$this->cextras = \champs_extras_objet('spip_formulaires_reponses');
		}
		if (!$this->cextras) {
			$this->cextras = array();
		}
		$this->rows = array();
		$this->headers = array();
		$this->page = $env['page_ts'];
		$this->size = $env['size'];
	}

	/**
	 * Peupler headers et rows
	**/
	public function setData() {
		$this->setRows();
		$this->setHeaders();
	}

	/**
	 * Peupler les headers à partir de la base SQL
	**/
	public function setHeaders() {
		$headers = &$this->headers;
		$headers[] = '#';
		$headers[] = _T('info_numero_abbreviation');
		$headers[] = _T('formidable:date_envoi');
		foreach ($this->cextras as $extra) {
			$headers[] = $extra['options']['label'] ?? $extra['options']['label_case'] ?? $extra['options']['nom'];
		}
		foreach ($this->saisies_finales as $saisie) {
			if ($saisie['saisie'] == 'explication') {
continue;
			}
			$chemin = \saisies_chercher($this->saisies, $saisie['identifiant'], true);
			if (count($chemin) > 1) {
				$fieldset = $this->saisies[$chemin[0]];
				$fieldset = $fieldset['options']['label'];
			} else {
				$fieldset = '';
			}
			$label =  $saisie['options']['label'] ?? $saisie['options']['label_case'] ?? $saisie['options']['nom'];
			if ($fieldset) {
				$label .= " <span class='fieldset_label'>($fieldset)</span>";
			}
			$headers[] = $label;
		}
	}
	/**
	 * Peupler les lignes à partir de la base SQL
	**/
	private function setRows() {
		$saisies = &$this->saisies;
		$res_reponse = \sql_select('*',
			'spip_formulaires_reponses',
			array(
				"id_formulaire= $this->id_formulaire",
				"statut REGEXP $this->statut"
			),
			'',
			'DATE DESC'
		);


		while ($row_reponse = \sql_fetch($res_reponse)) {
			$id_formulaires_reponse = $row_reponse['id_formulaires_reponse'];
			$row_ts = [];
			$this->totalRows++;
			// Cell 0 : statut
			$value = \liens_absolus(\appliquer_filtre($row_reponse['statut'], 'puce_statut', 'formulaires_reponse', $row_reponse['id_formulaires_reponse'], true));
			$row_ts[] = new cell([
				'table' => $this,
				'id_formulaires_reponse' => $id_formulaires_reponse,
				'nom' => 'statut',
				'value' => $value,
				'sort_value' => $row_reponse['statut'],
				'filter_value' => $row_reponse['statut'],
				'crayons' => false,
				'type' => 'natif'
			]);

			// Cell 1 : id
			$value = '<a href="'.\generer_url_ecrire('formulaires_reponse', 'id_formulaires_reponse='.$row_reponse['id_formulaires_reponse']).'">'.$row_reponse['id_formulaires_reponse'].'</a>';
			$row_ts[] = new cell([
				'table' => $this,
				'id_formulaires_reponse' => $id_formulaires_reponse,
				'nom' => 'id_formulaires_reponse',
				'value' => $value,
				'sort_value' => $row_reponse['id_formulaires_reponse'],
				'filter_value' => $row_reponse['id_formulaires_reponse'],
				'crayons' => false,
				'type' => 'natif'
			]);

			// Cell 2 : date
			$value = \affdate_heure($row_reponse['date_envoi']);
			$row_ts[] = new cell([
				'table' => $this,
				'id_formulaires_reponse' => $id_formulaires_reponse,
				'nom' => 'date_envoi',
				'value' => $value,
				'sort_value' => \strtotime($row_reponse['date_envoi']),
				'filter_value'  => $value,
				'crayons' => false,
				'type' =>'natif'
			]);

			// Cells suivantes : champs extras
			foreach ($this->cextras as $champ) {
				$crayons = $false;
				$nom = $champ['options']['nom'];
				if (test_plugin_actif('crayons')) {
					$opt = array(
						'saisie' => $champ,
						'type' => 'formulaires_reponse',
						'champ' => $nom,
						'table' => table_objet_sql('formulaires_reponse'),
			);
					if (autoriser('modifierextra', 'formulaires_reponse', $id_formulaires_reponse, '', $opt)) {
						$crayons = true;
		}
		}

		if (isset($champ['options']['traitements'])) {
			$value = \appliquer_traitement_champ($row_reponse[$nom], $nom, 'formulaires_reponse');
		} else {
			$value = implode(\calculer_balise_LISTER_VALEURS('formulaires_reponses', $nom, $row_reponse[$nom]), ', ');
		}
		$row_ts[] = new cell(
			[
				'table' => $this,
				'id_formulaires_reponse' => $id_formulaires_reponse,
				'nom' => $nom,
				'value' => $value,
				'sort_value' => sort_value($value, $champ, 'extra'),
				'filter_value' => null,
				'crayons' => $crayons,
				'type' => 'extra'
			]
			);
		}

		// Derniers cells : la réponse de l'internaute
		foreach ($this->saisies_finales as $saisie) {
			if ($saisie['saisie'] == 'explication') {
				continue;
			}
			$nom = $saisie['options']['nom'];
			$value = \calculer_voir_reponse($id_formulaires_reponse, $this->id_formulaire, $nom, '', 'valeur_uniquement');
			$row_value = \calculer_voir_reponse($id_formulaires_reponse, $this->id_formulaire, $nom, '', 'brut');
			$row_ts[] = new cell(
				[
					'table' => $this,
					'id_formulaires_reponse' => $id_formulaires_reponse,
					'nom' => $nom,
					'value' => $value,
					'sort_value' => sort_value($value, $champ, 'champ'),
					'filter_value' => null,
					'crayons' => true,
					'type' => 'champ'
				]
			);
		}
		// Vérifier si cela passe le filtres:
		if ($this->checkFilter($row_ts)) {
			$this->rows[] = $row_ts;
		}
		}
		$this->sortRows();
	}

	/**
	 * Vérifier si une ligne passe les tests de filtre
	 * @param array $row
	 * @return bool
	 **/
	private function checkFilter($row) {
		foreach ($this->filter as $col=>$filter) {
			$result = saisies_evaluer_afficher_si(
				$filter,
				array(),
				array(),
				$row[$col]->filter_value
			);
			if ($result == false) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Tri les lignes selon les paramètres passée en option
	 **/
	private function sortRows() {
		usort($this->rows, function ($a, $b) {
			// Trouver les cellules sur lesquelles trier
			foreach ($this->sort as $column => $sort) {
				$sort = intval($sort);
				$a_sort = $a[$column]->sort_value;
				$b_sort = $b[$column]->sort_value;
				if ($a_sort == $b_sort) {
					continue;
				} elseif ($sort) {
					return $a_sort < $b_sort;
				} else {
					return $a_sort > $b_sort;
				}
			}
			return 0;// Si tous les tests échoue à comparaison, c'est que nos deux lignes sont identiques, en ce qui concerne les critères de tri
		});
	}
	/**
	 * Retourne le json final
	 * @return string
	 **/
	public function getJson() {
		$json = array(
			'filteredRows' => \count($this->rows),
			'rows' => array_slice($this->rows, $this->page*$this->size, $this->size),
			'headers' => $this->headers,
			'total' => $this->totalRows,
		);
		return \json_encode($json);
	}
	/**
	 * Compte le nombre d'entetes
	 * @return int
	 **/
	public function countHeaders() {
		return count($this->headers);
	}

	/**
	 * Return la propriété, permet d'y accéder mais pas de la modifier
	 * @param string $prop
	**/
	public function __get($prop) {
		return $this->$prop;
	}

}
/**
 * Classe représentant une cellule
 * @var str|int $id_formulaire
 * @var str|int $id_formulaires_reponse
 * @var str|int $nom
 * @var str $value valeur de la cellule,
 * @var str $sort_value valeur de la cellule, pour le tri
 * @var bool $crayons est-ce crayonnable?
 * @var string $type natif|extra|champ
 **/
class cell implements \JsonSerializable{
	private $table;
	private $id_formulaires_reponse;
	private $nom;
	private $value;
	private $sort_value;
	private $filter_value;
	private $crayons;
	private $type;

	public function __construct($param = array()) {
		$this->table = $param['table'] ?? false;
		$this->id_formulaires_reponse = $param['id_formulaires_reponse'] ?? false;
		$this->nom = $param['nom'];
		$this->value = $param['value'];
		$this->sort_value = $param['sort_value'] ?? \textebrut($this->value);
		$this->filter_value = $param['filter_value'] ?? \textebrut($this->value);
		$this->crayons= $param['crayons'] ?? false;
		$this->type = $param['type'] ?? 'champ';
	}

	/**
	 * Returne la valeur string, avec le span englobant, pour les crayons
	**/
	public function jsonSerialize() {
		if ($this->crayons) {
			if ($this->type == 'extra') {
				return '<div class="'.classe_boucle_crayon('formulaires_reponse', $this->nom, $this->id_formulaires_reponse).'">'.$this->value.'</div>';
			} elseif ($this->type == 'champ') {
				return  '<div class="'.\calculer_voir_reponse($this->id_formulaires_reponse, $this->table->id_formulaire, $this->nom, '', 'edit').'">'.$this->value.'</div>';
			}
		} else {
			return $this->value;
		}
	}
	/**
	 * Return la propriété, permet d'y accéder mais pas de la modifier
	 * @param string $prop
	**/
	public function __get($prop) {
		return $this->$prop;
	}
}
/**
 * Depuis le #ENV de l'ajax appelé détermine le json à retourner
 * Un simple wrapper pour une classe en fait
 * @param array $env
 * @return string json
**/
function formidable_ts_json($env) {
	$formidable_ts = new table($env);
	$formidable_ts->setData();
	return $formidable_ts->getJson();
}


/**
 * Appelle le pipeline formidable_ts_sort_value
 * Pour trouver le type de tri
 * @param str|int $valeur valeur brute du champ de formulaire
 * @param array $saisie decrit la saisie
 * @param string $type='champ' ou bien 'extra'
 * @return string valeur du data-sort-attribut
 **/
function sort_value($valeur, $saisie, $type = 'champ') {
	if ($saisie['saisie'] === 'evenements' and $valeur) {
		$data = \sql_getfetsel('date_debut', 'spip_evenements', 'id_evenement='.$flux['args']['valeur']);
	}
	if ($type  === 'extra') {
		if (strpos($saisie['options']['sql'], 'INT') !== false
			or
			strpos($saisie['options']['sql'], 'FLOAT') !== false
			or
			strpos($saisie['options']['sql'], 'DATE') !== false
		)  {
			$data = $valeur;
		}
	}

	return pipeline ('formidable_ts_sort_value', array(
		'args' => array(
			'valeur' => $valeur,
			'saisie' => $saisie,
			'type' => $type
		),
		'data' => $data
	));
}
