<?php

/**
 * Ce fichier contient des fonctions utiles pour
 * lancer des actions effectués après la création du plugin
 * dans le script "post_creation" de la Fabrique
 *
 * Ces fonctions sont encapsulées dans une classe "Futilitaire"
 *
 * @note
 *     /!\   Partie experimentale.   /!\
 *     Cette API est susceptible d'évoluer.
 *
 * @package SPIP\Fabrique\Futilitaire
**/

/** Fichier de log des actions. */
define('_FABRIQUE_LOG_SCRIPTS', 'fabrique_scripts');


/**
 * Encapsule les fonctions d'aides 
 *
 * @example
 *     ```
 *     $futil = new Futilitaire($data, $destination_plugin, $destination_ancien_plugin);
 *     ```
**/
Class Futilitaire {
	/**
	 * Chemin de l'ancien plugin
	 *
	 * Celui que l'on recrée et que l'on a copié en sauvegarde avant
	 * 
	 * @var string
	 */
	public $dir_backup = "";
	
	/**
	 * Chemin de destination de notre plugin
	 * @var string
	 */
	public $dir_dest = "";

	/**
	 * Information complète des saisies du formulaire de création de la fabrique
	 * complétee de quelques raccourcis
	 * @var array
	 */
	public $data = array();

	/**
	 * @var Futilitaire_Lignes[] Stockage des modifications de lignes à réaliser
	 */
	public $lignes = array();

	/**
	 * Le constructeur charge les infos utiles :
	 *
	 * - le tableau $data contenant les saisies utilisateurs
	 * - le lieu de destination du plugin
	 * - le lieu de sauvegarde de la precedente creation du plugin
	 *   (qui contient donc peut etre des informations/fichiers que l'on veut recuperer)
	 *
	 * @param array $data
	 *     Information complète des saisies du formulaire de création de la fabrique
	 *     complétee de quelques raccourcis
	 * @param string $dir_dest
	 *     Chemin de destination de notre plugin
	 * @param string $dir_backup
	 *     Chemin de l'ancien plugin (celui que l'on recrée et que l'on a copié en sauvegarde avant)
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
	 *     Le texte à logguer.
	**/
	public function log($texte = '') {
		spip_log($texte, _FABRIQUE_LOG_SCRIPTS);
	}


	/**
	 * Déplacer une liste de fichiers du backup vers le nouveau plugin
	 * et créer les repertoires manquants si besoin dans le nouveau plugin.
	 *
	 * @example
	 *     ```
	 *     $futil->deplacer_fichiers(array(
	 *        'base/importer_spip_villes.php', 
	 *        'base/importer_spip_villes_donnees.gz', 
	 *     ));
	 *     ```
	 * 
	 * @param string|array $fichiers
	 *     Liste des fichiers à déplacer
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
				$this->log("deplacer_fichiers: Fichier $f introuvable dans le backup : $dest");
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
	 * Déplacer une liste de dossiers/répertoires
	 * du backup vers le nouveau plugin
	 * et créer les repertoires manquants si besoin dans le nouveau plugin.
	 *
	 * @example
	 *     ```
	 *     $futil->deplacer_dossiers('lib');
	 *     $futil->deplacer_dossiers(array('lib','actions'));
	 *     ```
	 *
	 * @param string|array $dossiers
	 *     Liste des fichiers a déplacer
	**/
	public function deplacer_dossiers($dossiers) {
		static $dirs = array();

		if (!$dossiers OR !$this->dir_dest OR !$this->dir_backup) {
			$this->log("deplacer_dossiers: Info manquante");
			return;
		}

		if (!is_array($dossiers)) {
			$dossiers = array($dossiers);
		}

		foreach ($dossiers as $d) {
			if (!$d) {
				$this->log("deplacer_dossiers: Dossier vide");
				continue;
			}
			$source = $this->dir_backup . $d;
			$dest   = $this->dir_dest   . $d;
			if (!is_dir($source)){
				$this->log("deplacer_dossiers: Dossier $d introuvable dans le backup : $dest");
				continue;
			}

			// cree l'arborescence depuis le chemin
			$this->creer_arborescence_destination($d, false);

			// copie recursive
			// http://stackoverflow.com/a/7775949
			foreach ($iterator =
					new RecursiveIteratorIterator(
						new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
							RecursiveIteratorIterator::SELF_FIRST) as $item
				) {
				if ($item->isDir()) {
					if (!mkdir($dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName())) {
						$this->log("deplacer_dossiers: Creation ratee de " . $iterator->getSubPathName());
					}
				} else {
					if (!copy($item, $dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName())) {
						$this->log("deplacer_dossiers: Creation ratee de " . $iterator->getSubPathName());
					}
				}
			}
		}
	}



	/**
	 * Crée les répertoires manquants dans le plugin crée
	 * par rapport au chemin désiré
	 *
	 * @example
	 *     ```
	 *     $this->creer_arborescence_destination("inclure/config.php");
	 *     ```
	 * 
	 * @param string $chemin
	 *      Destination depuis la racine du plugin
	 * @param bool $is_file
	 *      Est-ce un fichier (true) ou un répertoire (false) ?
	 * @return bool
	 *     Est-ce que c'est bien crée ?
	**/
	public function creer_arborescence_destination($chemin, $is_file = true) {
		// repertoire de destination deja crees.
		static $reps = array();

		if (!$this->dir_dest) {
			$this->log("creer_chemin: Destination inconnue");
			return false;
		}

		// si c'est un fichier, 
		// on retrouve le nom du fichier et la base du chemin de destination
		if ($is_file) {
			$dest = explode('/', $chemin);
			$nom = array_pop($dest);
			$chemin_dest = implode('/', $dest);
		} else {
			$chemin_dest = $chemin;
		}

		// ne pas creer systematiquement les repertoires tout de meme.
		if (!isset($reps[$chemin_dest])) {
			sous_repertoire_complet($this->dir_dest . $chemin_dest);
			$reps[$chemin_dest] = true;
		}

		return true;
	}


	/**
	 * Insère du code dans un fichier indiqué
	 *
	 * @example
	 *     ```
	 *     $futil->ajouter_lignes('lang/geoniche_fr.php', -3, 0, fabrique_tabulations($lignes, 1));
	 *     ```
	 *
	 * @param string $chemin
	 *     Chemin du fichier depuis la racine du plugin
	 * @param int $debut
	 *      Ligne du départ de l'insertion
	 *      Peut être négatif : -3 indique 3 lignes avant la fin de fichier
	 * @param int $fin
	 *      Indique combien de lignes seront supprimées du fichier à partir du début choisi
	 *      0 (zero) pour conserver tout le code existant dans le fichier
	 * @param string $code
	 *      Le code à insérer.
	 * @return bool
	 *      Operation reussie ou pas.
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

	/**
	 * Facilitateur d'écriture
	 *
	 * Crée une nouvelle ligne, la stocke et retourne l'objet crée pour modifications
	 *
	 * @example
	 *     ```
	 *     $futil->inserer($chemin, $debut, $fin, $tabulation)->contenu = X;
	 *     $futil->inserer($chemin, $debut, $fin, $tabulation)->contenu = <<<EOT
	 *     contenu
	 *     EOT;
	 * 
	 *     $futil->appliquer_lignes();
	 *     ```
	 * 
	 * @param string $chemin
	 *     Chemin du fichier à traiter
	 * @param int $debut
	 *     Numero de la ligne à modifier
	 * @param int $fin
	 *     Nombre de lignes supprimées
	 * @param int $tabulation
	 *     Ajout de n caractères de tabulations en début de chaque ligne
	 * @param string $contenu
	 *     Contenu à insérer
	 * @return Futilitaire_Ligne
	 *     Retourne l'objet créé, ce qui permet de le modifier, tout en gardant
	 *     ici au passage ce qui a été modifié.
	**/
	public function inserer($chemin, $debut, $fin=0, $tabulation=0, $contenu="") {
		return $this->lignes[] = new Futilitaire_Lignes($chemin, $debut, $fin, $tabulation, $contenu);
	}

	/**
	 * Applique les modifications de lignes qui ont été définies avec `set_lignes()`
	**/
	public function appliquer_lignes() {
		// insertion des parties
		foreach ($this->lignes as $ligne) {
			$this->ajouter_lignes(
				$ligne->fichier,
				$ligne->numero,
				$ligne->remplace,
				fabrique_tabulations($ligne->contenu, $ligne->tabulation));
		}
		// ces lignes ont été prises en compte !
		$this->lignes = array();
	}
}


/**
 * Espace de rangement d'information pour la modification de contenu d'un fichier
 *
**/
class Futilitaire_Lignes {

	/**
	 * Contenu à insérer
	 * @var string */
	public $contenu = "";
	/**
	 * Chemin du fichier à traiter 
	 * @var string */
	public $fichier = "";
	/**
	 * Numero de la ligne à modifier
	 * @var int */
	public $numero  = 0;
	/**
	 * Nombre de lignes supprimées
	 * @var int */
	public $remplace = 0;
	/**
	 * Ajout de n caractères de tabulations en début de chaque ligne
	 * @var int */
	public $tabulation = 0;

	/**
	 * Constructeur
	 *
	 * Définit toutes les propriétés
	 *
	 * @param string $fichier
	 *     Chemin du fichier à traiter
	 * @param int $numero
	 *     Numero de la ligne à modifier
	 * @param int $remplace
	 *     Nombre de lignes supprimées
	 * @param int $tabulation
	 *     Ajout de n caractères de tabulations en début de chaque ligne
	 * @param string $contenu
	 *     Contenu à insérer
	 */
	public function __construct($fichier, $numero, $remplace=0, $tabulation=0, $contenu="") {
		$this->fichier    = $fichier;
		$this->numero     = $numero;
		$this->remplace   = $remplace;
		$this->tabulation = $tabulation;
		$this->contenu    = $contenu;
	}
}


?>
