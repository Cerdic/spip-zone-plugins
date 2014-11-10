<?php

/**
 * Classe pour stocker les paramètres SSH
 *
 * @package SPIP\Migrateur\Migrateur_SSH
**/

if (!defined("_ECRIRE_INC_VERSION")) return;



/**
 * Stocker les données de connexion à un serveur via ssh
**/
class Migrateur_SSH {
	private $server = '';
	private $port = 22;
	private $user = '';

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
	 * Retourne la commande d'exécution pour se connecter à ce serveur
	 *
	 * Retourne 'ssh -o StrictHostKeyChecking=no -p XX user@serveur.tld'
	 * @return string|bool
	 *     false si erreur.
	**/
	public function obtenir_commande_connexion() {
		if (!$this->parent) return false;
		$cmd = $this->parent->obtenir_commande_serveur('ssh');
		if (!$cmd) return false;
		return "$cmd -o StrictHostKeyChecking=no -p {$this->port} {$this->user}@{$this->server}";
	}

	/**
	 * Retourne les options à passer à rsync pour se connecter en ssh (la source)
	 *
	 * Retourne 'ssh -o StrictHostKeyChecking=no -p XX user@serveur.tld'
	 * @return string
	**/
	public function obtenir_rysnc_parametres() {
		return " --no-o --no-p -e 'ssh -o StrictHostKeyChecking=no -p {$this->port}' {$this->user}@{$this->server}:";
	}

	/**
	 * Obtient le chemin d'un executable sur un serveur distant.
	 *
	 * @example
	 *     ```
	 *     $source = migrateur_source();
	 *     if ($source->ssh) {
	 *         $cmd = $source->ssh->obtenir_commande_serveur('mysqldump');
	 *     } else {
	 *         $cmd = $source->obtenir_commande_serveur('mysqldump');
	 *     }
	 *     if ($cmd) 
	 *         exec("$cmd ...");
	 *     }
	 *     ```
	 * @param string $command
	 *     Nom de la commande
	 * @return string
	 *     Chemin de la commande
	**/
	function obtenir_commande_serveur($command) {
		$ssh_cmd = $this->obtenir_commande_connexion();
		if (!$ssh_cmd) {
			return '';
		}
		$cmd = $this->obtenir_chemin_commande_serveur($command);
		if (!$cmd) {
			return '';
		}
		return "$ssh_cmd $cmd";
	}

	/**
	 * Obtient le chemin d'un executable sur un serveur distant.
	 *
	 * @note Préférer la méthode obtenir_commande_serveur()
	 * @example
	 *     ```
	 *     $source = migrateur_source();
	 *     $cmd_ssh = $source->ssh->obtenir_commande_connexion();
	 *     $cmd = $ssh->obtenir_chemin_commande_serveur('mysqldump');
	 *     if ($cmd) 
	 *         exec("$cmd_ssh $cmd ...");
	 *     }
	 *     ```
	 * @param string $command
	 *     Nom de la commande
	 * @return string
	 *     Chemin de la commande
	**/
	function obtenir_chemin_commande_serveur($command) {
		static $commands = array();
		if (array_key_exists($command, $commands)) {
			return $commands[$command];
		}
	
		$ssh_cmd = $this->obtenir_commande_connexion();
		if (!$ssh_cmd) {
			return $commands[$command] = '';
		}
		exec("$ssh_cmd which $command", $output, $err);
		if (!$err and count($output) and $cmd = trim($output[0])) {
			$this->log("Commande distante '$command' trouvée dans $cmd");
			return $commands[$command] = $cmd;
		}
		$this->log("/!\ Commande distante '$command' introuvable sur le serveur…");
		return $commands[$command] = '';
	}

	/**
	 * Définit un accès au parent
	 *
	 * @param Migrateur_DATA $data
	**/
	public function setParent(Migrateur_DATA $data) {
		$this->parent = $data;
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
