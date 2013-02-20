<?php

/**
 * Ce fichier contient des fonctions utiles pour
 * la génération d'un diff (unix/windows) entre 2 dossiers.
 *
 * Nécessite la commande `exec()`
 *
 * Ces fonctions sont encapsulées dans une classe "Fdiff"
 * 
 * Cette API est perfectible...
 *
 * @author Marcimat
 * @author Julien Lanfray (le fonctionnement sous windows)
 * @package SPIP\Fabrique\Fdiff
**/



/**
 * Encapsule les fonctions permettant de réaliser un diff entre 2 dossiers 
 *
 * @example
 *     ```
 *     $fdiff = new Fdiff($ancien_repertoire, $nouveau_repertoire);
 *     ```
**/
Class Fdiff {

	/**
	 * Dossier source de la comparaison (l'ancien)
	 * @var string */
	private $dossier1 = "";

	/**
	 * Dossier destination de la comparaison (le nouveau)
	 * @var string */
	private $dossier2 = "";

	/**
	 * Fichiers ignorés
	 * @var array */
	private $ignorer = array(".", "..");

	/**
	 * Fichiers ignorés (par extension)
	 * @var array */
	private $ignorer_extensions = array("svn", "db");

	/**
	 * Commande diff/fc non realisée sur ces fichiers (par extension)
	 * @var array */
	private $ignorer_extensions_complementaires = array("png", "jpg", "jpeg", "gif");


	/**
	 * Constructeur
	 *
	 * @param string $dossier1
	 *     Chemin du repertoire source de la comparaison (l'ancien)
	 * @param string $dossier2
	 *     Chemin du repertoire destination de la comparaison (le nouveau)
	 * 
	**/
	public function __construct($dossier1, $dossier2) {
		$this->dossier1 = $dossier1;
		$this->dossier2 = $dossier2;
	}



	/**
	 * Ajoute des noms de fichiers/dossiers à la liste d'exclusion.
	 *
	 * Ces fichiers ne seront pas du tout traités.
	 *
	 * @param array $tab
	 *     tableau de nom de fichiers/dossiers
	 * 
	**/
	public function add_ignorer($tab){
		if (!$tab) return;

		if (!is_array($tab)) {
			$tab = array($tab);
		}

		$this->ignorer = array_merge($this->ignorer, $tab);
	}


	/**
	 * Ajoute des extensions de fichiers/dossiers à la liste d'exclusion.
	 * 
	 * Les fichiers portants ces extensions ne seront pas du tout traités.
	 *
	 * @param array $tab
	 *     tableau d'extensions - ex: array('dat')
	 * 
	**/
	public function add_ignorer_extensions($tab){
		if (!$tab) return;

		if (!is_array($tab)) {
			$tab = array($tab);
		}

		$this->ignorer_extensions = array_merge($this->ignorer_extensions, $tab);
	}



	/**
	 * Ajoute des extensions de fichiers/dossiers à la liste d'exclusion.
	 * 
	 * Les fichiers portants ces extensions peuvent être pris en compte
	 * dans le calcul des ajouts/suppressions 
	 * mais aucune comparaison ne sera fait sur ces fichiers.
	 *
	 * @param array $tab
	 *     tableau d'extensions - ex: array('dat')
	 * 
	**/
	public function add_ignorer_extensions_complementaires($tab){
		if (!$tab) return;

		if (!is_array($tab)) {
			$tab = array($tab);
		}

		$this->ignorer_extensions_complementaires = array_merge($this->ignorer_extensions_complementaires, $tab);
	}


	/**
	 * Construit une expression regulière reconnaissant des extensions de fichiers
	 * 
	 * @param array $tab
	 *    La liste des extensions de fichiers
	 * @return string
	 *    Expression reguliere. ex: `"/(\.svn|\.db)$/i"`
	 * 
	**/
	private function ereg_filtre_extensions($tab){
		if (!is_array($tab)) {
			$tab = array($tab);
		}

		$s = array();
		foreach($tab as $ext){
			$s[] = "\\." . $ext;
		}
		$s = implode('|', $s);

		return "/(".$s.")$/i";
	}



	/**
	 * Retourne la liste des sous-dossiers et fichiers (racine comprise)
	 * 
	 * Fonction recursive.
	 * 
	 * @param array $dossier
	 *     La racine dans laquelle la recherche est lancee
	 * @return array
	 *     Des chemins disques (dossiers & fichiers)
	 * 
	**/
	private function get_chemins_fichiers($dossier){
		$chemins = array();
		$ereg_filtre_extensions = $this->ereg_filtre_extensions($this->ignorer_extensions);
		if ($handle = opendir($dossier)) {
			$chemins[] = $dossier;
			while (false !== ($file = readdir($handle))) {
				if (!in_array($file, $this->ignorer) && !preg_match($ereg_filtre_extensions, $file)) {
					// securiser...
					if (substr($dossier, -1, 1) == "/"){
						$path = $dossier.$file;
					} else {
						$path = $dossier."/".$file;
					}
					// ---
					if (is_dir($path)){
						$chemins = array_merge($chemins, $this->get_chemins_fichiers($path));
					} else {
						$chemins[] = $path;
					}
				}
			}
			closedir($handle);
		}
		return $chemins;
	}



	/**
	 * Retourne la liste des chemins disques 
	 * pour lesquelles chemin_base a été retiré.
	 * 
	 * @param string $chemin_base
	 *     Chemin de base sur lequel calculer la partie relative
	 * @param array $chemins
	 *     Liste de chemins disque
	 * @return array
	 *     Des chemins relatifs au chemin_base
	**/
	private function get_chemins_relatifs($chemin_base, $chemins){
		$log = "";
		$lg = strlen($chemin_base);

		$files = array();
		foreach($chemins as $path){
			$f = substr($path, $lg);
			if (!in_array($f, $this->ignorer_extensions)){
				$files[] = $f;
			}
		}

		return $files;
	}



	/**
	 * Retourne un tableau des differences entre dossier1 et dossier2
	 * base sur la commande "fc" de Windows
	 * 
	 * @return array
	 *    Tableau de la forme :
	 *    `array("diff"=>Texte, "affiche"=>Texte, "suppressions"=>array(noms de fichier))`
	 * 
	**/
	private function get_diff_windows(){
		// Liste des chemins vers les fichiers
		$ldossier1 = $this->get_chemins_fichiers($this->dossier1);
		$ldossier2 = $this->get_chemins_fichiers($this->dossier2);

		// Liste des chemins relatifs
		$files_dossier1 = $this->get_chemins_relatifs($this->dossier1, $ldossier1);
		$files_dossier2 = $this->get_chemins_relatifs($this->dossier2, $ldossier2);


		// Les fichiers qui ont ete supprimes (que dans dossier1)
		$que_dossier1 = array_diff($files_dossier1, $files_dossier2);
		// Les nouveaux fichiers crees (que dans dossier2)
		$que_dossier2 = array_diff($files_dossier2, $files_dossier1);
		// Les fichiers present des 2 cotes, qu'il faudra comparer
		$les_deux = array_intersect($files_dossier2, $files_dossier1);

		$tab = array();

		// Liste des fichiers supprimes
		$suppressions = "";
		if (count($que_dossier1)>0){
			$tmp = implode("\n- ", $que_dossier1);
			$suppressions .= "Fichiers supprimés : ";
			$suppressions .= "\n- ".$tmp; 
			$tab[] = $suppressions."\n";
		}
		
		// Liste des fichiers crees
		$ajouts = "";
		if (count($que_dossier2)>0){
			$tmp = implode("\n- ", $que_dossier2);
			$ajouts .= "Fichiers créés : ";
			$ajouts .= "\n- ".$tmp; 
			$tab[] = $ajouts."\n";
		}
		
		// Liste des fichiers modifies
		$diffs = "";
		if (count($les_deux)>0){
			$diffs .= "Fichiers modifiés : ";
			$cwd = getcwd() . "\\";
			$ereg_filtre_extensions = $this->ereg_filtre_extensions($this->ignorer_extensions_complementaires);
			foreach($les_deux as $f){
				$path_dossier1 = str_replace("/", "\\", $cwd.$this->dossier1.$f);
				$path_dossier2 = str_replace("/", "\\", $cwd.$this->dossier2.$f);

				if (is_file($path_dossier2) && !preg_match($ereg_filtre_extensions, $f)){
					$commande_diff = "fc /L /N $path_dossier1 $path_dossier2";
					$diff = "";
					exec($commande_diff, $diff);
					// Depend surement de la langue de l'OS ??
					if (!preg_match("/aucune diff.rence trouv/i", $diff[1])){
						$tmp = implode("\n", $diff);
						// Supprimer la partie absolue des path (pour plus de legerete...)
						$tmp = str_replace(strtoupper($cwd), "", $tmp);
						$di = "";
						$di .= "\n\n".$f. " :";
						$di .= "\n--------------------------------------------------------------------";
						$di .= "\n".$tmp;
						$diffs .= "\n".$di;
					}
				}
			}
			$tab[] = $diffs."\n";
		}

		$diff = implode("\n", $tab);

		$tab = array(
			"diff" => $diff,
			"affiche" => $diff, // diff plus humainement lisible
			"suppressions" => $que_dossier1
		);
		
		return $tab;
	}


	/**
	 * Retourne un tableau des différences entre dossier1 et dossier2
	 * base sur la commande "diff" des systemes Unix
	 * 
	 * @return array
	 *     Tableau de la forme : 
	 *     `array("diff"=>Texte, "affiche"=>Texte, "suppressions"=>array(noms de fichier))`
	 * 
	**/
	private function get_diff_unix(){
		$diff = "";
		
		$options_ignorer = "";
		foreach($this->ignorer as $ignore){
			$options_ignorer .= " -x ".$ignore;
		}
		$commande_diff = "diff -r".$options_ignorer." ".$this->dossier1." ".$this->dossier2;
		exec($commande_diff, $diff);

		// chaque ligne contient une info
		// on cherche les fichiers presents dans l'ancienne version
		// supprimes de la nouvelle pour avertir
		$suppressions = array();
		// on en profite pour raccourcir la ligne diff
		// pour un retour plus humainement lisible
		$affiche = $diff;
		foreach($diff as $k => $l) {
			// trouver les suppressions
			// Only in ../plugins/fabrique_auto/.backup/prefixe/dir: fichier.php
			if ($l[0] == 'O' AND substr($l, 0, 7) == 'Only in') {
				if (strpos($l, $this->dossier1)) {
					$suppressions[] = str_replace(': ', '/', trim(substr($l, 8 + strlen($this->dossier1))));
				}
				$affiche[$k] = "\n\n$l";
			}
			// rendre le diff plus lisible
			if ($l[0] == 'd' AND substr($l, 0, 4) == 'diff') {
				// ne garder que le chemin relatif du fichier
				$fichier = explode(' ', $l);
				$fichier = array_pop($fichier);
				$fichier = substr($fichier, strlen($this->dossier2));
				$affiche[$k] = "\n\n$fichier";
			}
		}
		$diff = implode("\n", $diff);
		$affiche = implode("\n", $affiche);

		$tab = array(
			"diff" => $diff,
			"affiche" => $affiche, // diff plus humainement lisible
			"suppressions" => $suppressions
		);

		return $tab;
	}


	/**
	 * Retourne un tableau des différences entre dossier1 et dossier2
	 *
	 * @exemple
	 *     ```
	 *     $fdiff->get_diff();
	 *     ```
	 * 
	 * @return array
	 *     Tableau de la forme :
	 *     ```
	 *     array(
	 *       "diff"=>Texte,
	 *       "affiche"=>Texte, // diff plus lisible pour affichage
	 *       "suppressions"=>array(noms de fichier)
	 *     )
	 *     ```
	 * 
	**/
	public function get_diff() {
		if (_OS_SERVEUR == 'windows') {
			$tab = $this->get_diff_windows();
		} else {
			$tab = $this->get_diff_unix();
		}

		return $tab;
	}
}

?>
