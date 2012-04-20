<?php

/**
 *  /!\   Partie experimentale.   /!\
 * 
 *  Cette API est susceptible d'évoluer.
**/


/**
 * Ce fichier contient des fonctions utiles pour
 * lancer des actions effectues apres la creation du plugin
 * dans le script "post_creation" de la Fabrique
 *
 * Ces fonctions sont encapsulees dans une classe "Futilitaire"
 * 
**/

// fichier de log des actions.
define('_FABRIQUE_LOG_SCRIPTS', 'fabrique_scripts');



/**
 * Cette classe Futilitaires
 * encapsule les fonctions d'aides 
 *
 * $futil = new Futilitaire($data, $destination_plugin, $destination_ancien_plugin);
**/
Class Futilitaire {

	public $dir_backup = "";
	public $dir_dest = "";
	public $data = array();

	/**
	 * Le constructeur charge les infos utiles :
	 * - le tableau $data contenant les saisies utilisateurs
	 * - le lieu de destination du plugin
	 * - le lieu de sauvegarde de la precedente creation du plugin
	 *   (qui contient donc peut etre des informations/fichiers que l'on veut recuperer)
	 *
	 * @param array $data
	 * 		Information complete des saisies du formulaire de creation de la fabrique
	 * 		completee de quelques raccourcis
	 * @param string $dir_dest
	 * 		Chemin de destination de notre plugin
	 * @param string $dir_backup
	 * 		Chemin de l'ancien plugin (celui que l'on recree et que l'on a copie en sauvegarde avant)
	 * 
	 * @return null
	**/
	public function __construct($data, $dir_dest, $dir_backup) {
		$this->data = $data;
		$this->dir_dest = $dir_dest;
		$this->dir_backup = $dir_backup;
	}

	/**
	 * Log une erreur
	 *
	 * @param string $texte
	 * 		Le texte a logguer.
	 * 
	 * @return null
	**/
	public function log($texte = '') {
		spip_log($texte, _FABRIQUE_LOG_SCRIPTS);
	}


	/**
	 * Deplacer une liste de fichiers
	 * du backup vers le nouveau plugin
	 * et cree les repertoires manquant si besoin dans le nouveau plugin.
	 *
	 * Usage:
	 * $futil->deplacer_fichiers(array(
	 *    'base/importer_spip_villes.php', 
	 *    'base/importer_spip_villes_donnees.gz', 
	 * ));
	 *
	 * @param string $source
	 * 		Répertoire source
	 * @param string $dest
	 * 		Répertoire destination
	 * @param string|array $fichiers
	 * 		Liste des fichiers a déplacer
	 * 
	 * @return null
	**/
	public function deplacer_fichiers($fichiers) {
		static $dirs = array();

		if (!$fichiers OR !$this->dir_dest OR !$this->dir_backup) {
			$this->log("deplacer_fichiers: Info manquante");
			return;
		}

		if (!is_array($fichiers)) {
			$fichiers = array($fichiers);
		}

		foreach ($fichiers as $f) {
			if (!$f) {
				$this->log("deplacer_fichiers: Fichier vide");
				continue;
			}
			$source = $this->dir_backup . $f;
			$dest   = $this->dir_dest   . $f;
			if (!file_exists($source)){
				$this->log("deplacer_fichiers: Fichier introuvable dans le backup : $dest");
				continue;
			}

			// cree l'arborescence depuis le chemin
			$this->creer_arborescence_destination($f);

			if (!copy($source, $dest)) {
				$this->log("deplacer_fichiers: Copie ratee de $source vers $dest");
			}
		}
	}


	/**
	 * Cree les repertoires manquants dans le plugin cree
	 * par rapport au chemin desire
	 *
	 * Usage :
	 * $this->creer_arborescence_destination("inclure/config.php");
	 * 
	 * @param string $chemin
	 * 		Destination depuis la racine du plugin
	 * @return bool
	 * 		Est-ce que c'est bien cree ?
	**/
	public function creer_arborescence_destination($chemin) {
		// repertoire de destination deja crees.
		static $reps = array();

		if (!$this->dir_dest) {
			$this->log("creer_chemin: Destination inconnue");
			return false;
		}

		// on retrouve le nom du fichier et la base du chemin de destination
		$dest = explode('/', $chemin);
		$nom = array_pop($dest);
		$chemin_dest = implode('/', $dest);

		// ne pas creer systematiquement les repertoires tout de meme.
		if (!isset($reps[$chemin_dest])) {
			sous_repertoire_complet($this->dir_dest . $chemin_dest);
			$reps[$chemin_dest] = true;
		}

		return true;
	}


	/**
	 * Insere du code dans un fichier indique
	 *
	 * Usage:
	 * $futil->ajouter_lignes('lang/geoniche_fr.php', -3, 0, fabrique_tabulations($lignes, 1));
	 *
	 * @param string $chemin
	 * 		Chemin du fichier depuis la racine du plugin
	 * @param int $debut
	 * 		Ligne du depart de l'insertion
	 * 		Peut etre negatif : -3 indique 3 lignes avant la fin de fichier
	 * @param int $fin
	 * 		Indique combien de lignes seront supprimees du fichier a partir du debut choisi
	 * 		0 (zero) pour conserver tout le code existant dans le fichier
	 * @param string $code
	 * 		Le code a inserer.
	 * @return bool
	 * 		Operation reussie ou pas.
	**/
	public function ajouter_lignes($chemin, $debut, $fin, $code) {

		if (!$this->dir_dest) {
			$this->log("ajouter_lignes: Destination inconnue");
			return false;
		}

		$dest = $this->dir_dest . $chemin;
		if (!file_exists($dest)) {
			$this->log("ajouter_lignes: Fichier inexistant $dest");
			return false;
		}

		lire_fichier($dest, $contenu);
		if (is_null($contenu)) {
			$this->log("ajouter_lignes: Lecture echouee de $dest");
			return false;
		}

		$contenu = explode("\n", $contenu);
		$code = explode("\n", $code);
		array_splice($contenu, $debut, $fin, $code);
		$contenu = implode("\n", $contenu);
		ecrire_fichier($dest, $contenu);
	}
}

?>
