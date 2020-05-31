<?php
namespace formidable_ts;
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/sql');
include_spip('inc/saisies');
include_spip('inc/cextras');

/*
 * La classe principale
 * qui cherche les données en base
 * et retourne un tableau json si besoin
*/
class table {
	private $id_formulaire;
	private $filter;
	private $column;
	private $data;
	private $statut;

	/**
	 * @param array $env
	 * L'env du squelette
	**/
	public function __construct($env) {
		$this->id_formulaire = sql_quote($env['id_formulaire'] ?? null);
		$this->filter = $env['filter'] ?? null;
		$this->column = $env['column'] ?? null;
		$env['statut'] = $env['statut'] ?? null;
		$this->statut = \sql_quote($env['statut'] ? $env['statut'] : '.*');
	}


	/**
	 * Peupler l'objet à partir de la base SQL
	**/
	public function setData() {
		$this->data = array();
		$saisies = \unserialize(sql_getfetsel('saisies', 'spip_formulaires', "id_formulaire=$this->id_formulaire"));
		$saisie  = \saisies_lister_finales($saisies);
		$cextras = appliquer_filtre('spip_formulaires_reponses', 'champs_extras_objet');

		$res_reponse = \sql_select('*',
			'spip_formulaires_reponses',
			array(
				"id_formulaire= $this->id_formulaire",
				"statut REGEXP $this->statut"
			),
			'',
			'DATE DESC'
		);
		while ($raw_reponse = \sql_fetch($res_reponse)) {
			$raw_ts = [];

			// Cell 0 : statut
			$value = \liens_absolus(\appliquer_filtre($raw_reponse['statut'], 'puce_statut', 'formulaires_reponse', $raw_reponse['id_formulaires_reponse'], true));
			$raw_ts[] = new cell(
				$value,
				$raw_reponse['statut'],
				false,
				'natif');

			// Cell 1 : id
			$value = '<a href="'.\generer_url_ecrire('formulaires_reponse', 'id_formulaires_reponse='.$raw_reponse['id_formulaires_reponse']).'">'.$raw_reponse['id_formulaires_reponse'].'</a>';
			$raw_ts[] = new cell(
				$value,
				$raw_reponse['id_formulaires_reponse'],
				false,
				'natif');

			// Cell 2 : date
			$value = \affdate_heure($raw_reponse['date']);
			$raw_ts[] = new cell(
				$value,
				$raw_reponse['date'],
				false,
				'natif');

			// Stocker tout
			$this->data[] = $raw_ts;
		}
	}

	/**
	 * Retourne le json final
	 * @return string
	**/
	public function getJson() {
		$json = [\count($this->data),$this->data];
		return \json_encode($json);
	}
}

/**
 * Classe représentant une cellule
 * @var str $value valeur de la cellule,
 * @var str $sort_value valeur de la cellule, pour le tri
 * @var bool $crayons est-ce crayonnable?
 * @var string $type natif|extra|champ
**/
class cell implements \JsonSerializable{
	private $value;
	private $sort_value;
	private $crayons;
	private $type;

	public function __construct($value, $sort_value, $crayons, $type) {
		$this->value = $value;
		$this->sort_value = $sort_value;
		$this->crayons= $crayons;
		$this->type = $type;
	}

	/**
	 * Returne la valeur string, avec le span englobant, pour les crayons
	**/
	public function jsonSerialize() {
		return $this->value;
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
