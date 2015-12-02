<?php
/**
 * Fonctions du plugin Composer
 *
 * @plugin     Composer
 * @copyright  2015
 * @author     Matthieu Marcillaud
 * @licence    GNU/GPL
 * @package    SPIP\Composer\Fonctions
 */
 
if (!defined('_ECRIRE_INC_VERSION')) return;


/**
 * Décrit le tableau JSON qui servira à génerer le composer.json
**/
class Composer_JSON {

	/** @var array Données du futur json */
	private $data = array();

	/**
	 * Contructeur
	 *
	 * @param array $options
	 *     Valeurs à intégrer au json
	 */
	public function __construct($options = array()) {
		$defaut = array(
			"minimum-stability" => "dev",
			"prefer-stable" => true,
			"require" => array(),
			"config" => array(
				// le composer.json est dans config/
				// mais on veut générer le vendor/ à la racine, tant qu'à faire
				"vendor-dir" => rtrim(_ROOT_VENDOR, '/')
			)
		);
		$this->data = array_merge_recursive($defaut, $options);
	}

	/**
	 * Ajoute un élément au tableau de déclaration
	 *
	 * @param string $name
	 *     Nom de l'attribut ajouté ou modifié
	 * @param mixed $value
	 *     Valeur de l'attribut
	**/
	public function add($attr, $value) {
		$this->data[$attr] = $value;
	}

	/**
	 * Retourne un élément au tableau de déclaration
	 *
	 * @param string $name
	 *     Nom de l'attribut demandé
	 * @param mixed $value
	 *     Valeur de l'attribut
	**/
	public function get($attr) {
		if (array_key_exists($attr, $this->data)) {
			return $this->data[$attr];
		}
		return null;
	}

	/**
	 * Ajoute un require
	 *
	 * @param string $name
	 *     Nom du package
	 * @param string $value
	 *     Ce qu'on souhaite. Ex: "~1.1", "dev-master", ">=2.0" ...
	**/
	public function add_require($name, $value) {
		$this->data['require'][$name] = $value;
	}

	/**
	 * Indique sur un package est requis
	 *
	 * @param string $name
	 *     Nom du package
	 * @return bool
	**/
	public function is_required($name) {
		return !empty($this->data['require'][$name]);
	}

	/**
	 * Retourne la liste de toutes les librairies requises
	 *
	 * @return array
	**/
	public function get_requires() {
		return $this->data['require'];
	}

	/**
	 * Retourne le JSON pour Composer.
	 *
	 * @return string JSON
	**/
	public function get_json() {
		return json_encode($this->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_FORCE_OBJECT );
	}
}
