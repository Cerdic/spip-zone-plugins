<?php

/**
 * Classe pour stocker les paramètres de migration
 *
 * @package SPIP\Migrateur\Migrateur_DATA
**/

if (!defined("_ECRIRE_INC_VERSION")) return;


/**
 * Classe de données (source ou destination) d'une migration
 *
**/
Class Migrateur_DATA {

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
	 * Chemin d'accès SSH
	 * @var Migrateur_SSH|null
	 */
	private $ssh = null;

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
		if (isset($props['ssh']) and isset($props['ssh']['server']) and $props['ssh']['server']) {
			$this->ssh = new Migrateur_SSH($props['ssh']);
			$this->ssh->setParent($this);
		}
		if (isset($props['sql']) and $props['sql']) {
			$this->sql = new Migrateur_SQL($props['sql']);
			$this->sql->setParent($this);
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
	 * Retourne le code permettant d'exécuter une commande sur le serveur
	 *
	 * Le code retourné dépend à la fois du fait si la connexion au serveur
	 * est locale ou par SSH ; et du chemin vers la commande sur le serveur
	 * (très souvent /usr/bin/$command, mais pas toujours)
	 *
	 * @api
	 * @example
	 *     ```
	 *     $source = migrateur_source();
	 *     if ($cmd = $source->commande('rsync')) {
	 *         exec("$cmd ... ...")
	 *     }
	 *     ```
	 *
	 * @param string $command
	 *     Nom de la commande
	 * @return string Code pour lancer la commande
	**/
	function commande($command) {
		if ($this->ssh) {
			return $this->ssh->obtenir_commande_serveur($command);
		}
		return $this->obtenir_commande_serveur($command);
	}

	/**
	 * Obtient le chemin d'un executable sur le serveur.
	 *
	 * @example
	 *     ```
	 *     $source = migrateur_source();
	 *     $cmd = $source->obtenir_commande_serveur('rsync');
	 *     if ($cmd) 
	 *         exec("$cmd ... ... ");
	 *     }
	 *     ```
	 * @param string $command
	 *     Nom de la commande
	 * @return string
	 *     Chemin de la commande
	**/
	function obtenir_commande_serveur($command) {
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
