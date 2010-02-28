<?php

class article {
	
	public $id_article;
	public $surtitre;
	public $titre = "Nouvel article";
	public $soustitre;
	public $id_rubrique = 1;
	public $descriptif;
	public $chapo;
	public $texte;
	public $ps;
	public $date;
	public $statut = "prepa";
	public $id_secteur = "1";
	public $maj;
	public $export = "oui";
	public $date_redac;
	public $visites;
	public $referers;
	public $popularite;
	public $accepter_forum = "pos";
	public $date_modif;
	public $lang ="fr";
	public $langue_choisie ="non";
	public $id_trad;
	public $extra;
	public $id_version;
	public $nom_site;
	public $url_site;
	
	public function __construct($id_article = NULL) {
		$this->date = date("Y-m-d H:i:s");
		if($id_article) {
			$this->id_article = $id_article;
			/* On fait un select est on met a jour les valeurs */
            $result = spip_query("SELECT * FROM `".$GLOBALS['table_prefix']."_articles` WHERE id_article='".$this->id_article."'");
            $row = spip_fetch_array($result); 
			$this->surtitre = $row ["surtitre"];
			$this->titre  = $row ["titre"];
			$this->soustitre = $row ["soustitre"];
			$this->id_rubrique  = $row ["id_rubrique"];
			$this->descriptif = $row ["descriptif"];
			$this->chapo = $row ["chapo"];
			$this->texte = $row ["texte"];
			$this->ps = $row ["ps"];
			$this->date = $row ["date"];
			$this->statut  = $row ["statut"];
			$this->id_secteur  = $row ["id_secteur"];
			$this->maj = $row ["maj"];
			$this->export  = $row ["export"];
			$this->date_redac = $row ["date_redac"];
			$this->visites = $row ["visites"];
			$this->referers = $row ["referers"];
			$this->popularite = $row ["popularite"];
			$this->accepter_forum  = $row ["accepter_forum"];
			$this->date_modif = $row ["date_modif"];
			$this->lang  = $row ["lang"];
			$this->langue_choisie  = $row ["langue_choisie"];
			$this->id_trad = $row ["id_trad"];
			$this->extra = $row ["extra"];
			$this->id_version = $row ["id_version"];
			$this->nom_site = $row ["nom_site"];
			$this->url_site = $row ["url_site"];
		}
	}
		
	/* Creer un article SPIP */	
	public function add() {
		$add_sql = "INSERT INTO `".$GLOBALS['table_prefix']."_articles` 
		(`id_article`, `surtitre`, `titre`, `soustitre`, `id_rubrique`, `descriptif`, `chapo`, `texte`, `ps`, `date`, `statut`, `id_secteur`, `maj`, `export`, `date_redac`, `visites`, `referers`, `popularite`, `accepter_forum`, `date_modif`, `lang`, `langue_choisie`, `id_trad`, `extra`, `id_version`, `nom_site`, `url_site`) 
		VALUES ('', "._q($this->surtitre).", "._q($this->titre).", "._q($this->soustitre).", "._q($this->id_rubrique).", "._q($this->descriptif).", "._q($this->chapo).", "._q($this->texte).", "._q($this->ps).", "._q($this->date).", "._q($this->statut).", "._q($this->id_secteur).", CURRENT_TIMESTAMP, "._q($this->export).", "._q($this->date_redac).", '0', '0', '0', "._q($this->accepter_forum).", NOW(), "._q($this->lang).", "._q($this->langue_choisie).", "._q($this->id_trad).", "._q($this->extra).", "._q($this->id_version).", "._q($this->nom_site).", "._q($this->url_site).")";
		$result = spip_query($add_sql);
		$this->id_article = mysql_insert_id();
		return $result;
	}
	
	
	/* Update un article SPIP */	
	public function update() {
		$update_sql = "UPDATE `".$GLOBALS['table_prefix']."_articles`  SET
		`surtitre` =  "._q($this->surtitre).", `titre` = "._q($this->titre).", `soustitre` = "._q($this->soustitre).", `id_rubrique` = "._q($this->id_rubrique).", `descriptif` = "._q($this->descriptif).", `chapo` = "._q($this->chapo).", `texte` = "._q($this->texte).", `ps` = "._q($this->ps).", `date` = "._q($this->date).", `statut` = "._q($this->statut).", `id_secteur` = "._q($this->id_secteur).", `maj` = CURRENT_TIMESTAMP, `export` = "._q($this->export).", `date_redac` = "._q($this->date_redac).",	`visites`  = "._q($this->visites).", `referers`  = "._q($this->referers).", `popularite`  = "._q($this->popularite).", `accepter_forum`   = "._q($this->accepter_forum).", `date_modif`  = NOW(),  `lang` = "._q($this->lang).", `langue_choisie` = "._q($this->langue_choisie).", `id_trad` = "._q($this->id_trad).", `extra` = "._q($this->extra).", `id_version` = "._q($this->id_version).", `nom_site` = "._q($this->nom_site).", `url_site` = "._q($this->url_site)." WHERE `id_article` =".$this->id_article;
		$result = spip_query($update_sql);
		return $result;
	}
	
	
	/* Lie un auteur a cet article SPIP */
	public function add_auteur($id_auteur) {
		$add_auteur_sql = "INSERT INTO `".$GLOBALS['table_prefix']."_auteurs_articles` (`id_auteur` ,`id_article`) VALUES ('".$id_auteur."', '".$this->id_article."')";
		return spip_query($add_auteur_sql);
	}
	
	/* Lie un document a cet article SPIP */
	public function add_document($id_document) {
		$add_document_sql = "INSERT INTO `".$GLOBALS['table_prefix']."_documents_liens` (`id_document`, `id_objet`, `objet`, `vu`)	VALUES ('".$id_document."', '".$this->id_article."', 'article', 'non'	)";
		return spip_query($add_document_sql);
	}
	
	/* Fixe la rubrique a cet article SPIP, et en déduit l'id secteur */
	public function set_rubrique($id_rubrique) {
		$this->id_rubrique = $id_rubrique;
		$get_rubrique_result = spip_query("SELECT id_secteur FROM `".$GLOBALS['table_prefix']."_rubriques` WHERE  id_rubrique=".$id_rubrique);
		$get_rubrique_row = spip_fetch_array($get_rubrique_result);
		$this->id_secteur = $get_rubrique_row['id_secteur'];
		// Vu qu'il y a un article, on publie la rubrique
		spip_query("UPDATE `".$GLOBALS['table_prefix']."_rubriques` SET `statut` = 'publie' WHERE `id_rubrique` =".$id_rubrique);
	}
	
	/* Ajouter un logo */
	public function add_logo($file) {
		if (!file_exists($file)) {
			include_spip('inc/ajouter_documents');
			$infos = fixer_extension_document($file);
			return move_uploaded_file($file['tmp_name'], "IMG/arton".$this->id_article.".".$infos[0]);
		} else {
			return copy($file, "IMG/arton".$this->id_article.".".substr($file, -3));
		}
	}
	
	/* Ajouter un mot */
	public function add_mot($id_mot) {
		$add_mot_sql = "INSERT INTO `".$GLOBALS['table_prefix']."_mots_articles` (`id_mot` ,`id_article`) VALUES ('".$id_mot."', '".$this->id_article."')";
		return spip_query($add_mot_sql);
	}
	
	
	/* Retourne l'url du logo */
	public function get_logo() {
		$extensions = array("jpg", "png", "gif", "tiff", "bmp");

		foreach($extensions as $extension) {
			$file = "IMG/arton".$this->id_article.".".$extension;
			if (file_exists($file)) return $file;
		}
		return false;

	}

		
}


?>
