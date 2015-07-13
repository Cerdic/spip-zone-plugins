<?php

/**
 * Classe pour stocker les paramètres de migration
 *
 * @package SPIP\Migrateur\Data
**/
namespace SPIP\Migrateur;

/**
 * Classe de données (source ou destination) d'une migration
 *
**/
Class Data {

	/**
	 * chemin d'accès sur le serveur
	 * @var string
	 */
	private $dir = '';

	/**
	 * Chemin d'accès SQL
	 * @var Migrateur_SQL|null
	 */
	private $sql = null;


	/**
	 * Constructeur. 
	 *
	 * Permet de passer un tableau de couples de données (cle => valeur)
	**/
	public function __construct($props = array()) {

		$this->dir = realpath(_DIR_RACINE);

		if (is_array($props)) {
			foreach($props as $prop => $val) {
				if (property_exists($this, $prop)) {
					$this->$prop = $val;
				}
			}
		}

		$this->sql = new Sql();
		$this->sql->setParent($this);
		if (!empty($props['sql'])) {
			$this->sql->setInfos($pros['sql']);
		} else {
			$this->sql->setInfosAuto();
		}

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
	 * Retourne le code permettant d'exécuter une commande sur le serveur
	 *
	 * @api
	 * @example
	 *     ```
	 *     $infos = migrateur_infos();
	 *     if ($cmd = $infos->commande('rsync')) {
	 *         exec("$cmd ... ...")
	 *     }
	 *     ```
	 *
	 * @param string $command
	 *     Nom de la commande
	 * @return string Code pour lancer la commande
	**/
	public function commande($command) {
		return $this->obtenir_commande_serveur($command);
	}

	/**
	 * Obtient le chemin d'un executable sur le serveur.
	 *
	 * @example
	 *     ```
	 *     $infos = migrateur_infos();
	 *     $cmd = $infos->obtenir_commande_serveur('rsync');
	 *     if ($cmd) 
	 *         exec("$cmd ... ... ");
	 *     }
	 *     ```
	 * @param string $command
	 *     Nom de la commande
	 * @return string
	 *     Chemin de la commande
	**/
	public function obtenir_commande_serveur($command) {
		static $commands = array();
		if (array_key_exists($command, $commands)) {
			return $commands[$command];
		}

		exec("which $command", $output, $err);
		if (!$err and count($output) and $cmd = trim($output[0])) {
			$this->log("Commande '$command' trouvée dans $cmd");
			return $commands[$command] = $cmd;
		}

		$this->log("/!\ Commande '$command' introuvable sur ce serveur…");
		return $commands[$command] = '';
	}




	/**
	 * Retourne la liste des fichiers d'un répertoire donné
	 *
	 * @link https://github.com/outlandishideas/sync/
	 * 
	 * @param $path string
	 * @return array
	 */
	public function getFileList($path) {
		if (!$path or !is_string($path) or (strpos($path, '..') !== false)) {
			migrateur_log("Path erroné");
			return array();
		}

		$path = rtrim($path, '/') . DIRECTORY_SEPARATOR;
		$path = $this->dir . DIRECTORY_SEPARATOR . $path;

		$iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path,
				\FilesystemIterator::CURRENT_AS_FILEINFO |
				\FilesystemIterator::SKIP_DOTS
		));


		$pathPrefixLength = strlen($path);
		$files = array();
		foreach ($iterator as $fileInfo) {
			$fullPath = str_replace(DIRECTORY_SEPARATOR, '/', substr($fileInfo->getRealPath(), $pathPrefixLength));
			$files[$fullPath] = array($fileInfo->getSize(), $fileInfo->getMTime());
		}

		return $files;
	}


	/**
	 * Ajoute un message de log dans tmp/migrateur/migrateur.log
	 * ainsi que dans tmp/migrateur/etape.log et tmp/log/migrateur.log !
	 *
	 * @uses migrateur_log()
	 * @param string $msg Le message
	 * @param string $type Type de message
	**/
	public function log($msg, $type="") {
		return migrateur_log($msg, $type);
	}

}
