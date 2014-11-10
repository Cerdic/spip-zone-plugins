<?php

/**
 * Classe pour stocker les paramètres SQL
 *
 * @package SPIP\Migrateur\Migrateur_SQL
**/

if (!defined("_ECRIRE_INC_VERSION")) return;



/**
 * Stocker les données de connexion à un serveur via ssh
**/
class Migrateur_SQL {
	/**
	 * Utilisation de mysql ou mysqldump sans mot de passe
	 * @link http://blog.georgio.fr/mysql_config_editor-un-utilitaire-mysql-en-les-lignes-de-commandes/
	 * @var string
	**/
	private $login_path = '';
	
	#private $server = 'localhost';
	private $bdd = '';
	private $user = '';
	private $pass = '';

	/**
	 * Parent de cette instance de classe
	 * @var Migrateur_DATA|null 
	**/
	private $parent = null;

	/**
	 * Constructeur. 
	 *
	 * Permet de passer un tableau de couples de données (cle => valeur)
	**/
	public function __construct($props = array()) {
		if (is_array($props)) {
			foreach($props as $prop => $val) {
				if (property_exists($this, $prop)) {
					$this->$prop = $val;
				}
			}
		}
	}

	/**
	 * Permet d'obtenir une propriété de la classe
	**/
	public function __get($prop) {
		if (!property_exists($this, $prop)) {
			throw new Exception("Paramètre " . $prop . " inconnu");
		}
		return $this->$prop;
	}

	/**
	 * Définit un accès au parent
	 *
	 * @param Migrateur_DATA $data
	**/
	public function setParent(Migrateur_DATA $data) {
		$this->parent = $data;
	}
}
