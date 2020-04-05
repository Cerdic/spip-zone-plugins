<?php

/**
 * Classe pour stocker les paramètres SQL
 *
 * @package SPIP\Migrateur\Sql
**/
namespace SPIP\Migrateur;

/**
 * Stocker les données de connexion à un serveur via ssh
**/
class Sql{

	/**
	 * Utilisation de mysql ou mysqldump sans mot de passe
	 * @link http://blog.georgio.fr/mysql_config_editor-un-utilitaire-mysql-en-les-lignes-de-commandes/
	 * @var string
	**/
	private $login_path = '';

	private $server = 'localhost';
	private $bdd = '';
	private $user = '';
	private $pass = '';
	private $prefixe = '';  // souvent 'spip' = prefixe des tables
	private $req = '';      // mysql | sqlite

	/**
	 * Parent de cette instance de classe
	 * @var Spip\Migrateur\Data|null 
	**/
	private $parent = null;

	/**
	 * Constructeur. 
	 *
	 * Permet de passer un tableau de couples de données (cle => valeur)
	**/
	public function __construct($props = array()) {
		$this->setInfos($props);
	}


	/**
	 * Permet de passer un tableau de couples de données (cle => valeur)
	**/
	public function setInfos($props = array()) {
		if (is_array($props) and $props) {
			foreach($props as $prop => $val) {
				if (property_exists($this, $prop)) {
					$this->$prop = $val;
				}
			}
		}
	}

	/**
	 * Calcul automatiquement des identifiants de connexion à partir du fichier config/connect.php
	**/
	public function setInfosAuto() {
		if (!is_readable(_FILE_CONNECT)) {
			$this->parent->log("Impossible de calculer les identifiants de connexion");
			return false;
		}
		include_spip('inc/install');
		$data = analyse_fichier_connection(_FILE_CONNECT);
		if (!$data) {
			$this->parent->log("Impossible de lire les identifiants de connexion");
		}

		$this->serveur = array_shift($data);
		$this->user = array_shift($data);
		$this->pass = array_shift($data);
		$this->bdd = array_shift($data);
		$this->req = array_shift($data);
		$this->prefixe = array_shift($data);
	}


	/**
	 * Permet d'obtenir une propriété de la classe
	**/
	public function __get($prop) {
		if (!property_exists($this, $prop)) {
			throw new \Exception("Paramètre " . $prop . " inconnu");
		}
		return $this->$prop;
	}

	/**
	 * Définit un accès au parent
	 *
	 * @param Spip\Migrateur\Data $data
	**/
	public function setParent(Data $data) {
		$this->parent = $data;
	}
}
