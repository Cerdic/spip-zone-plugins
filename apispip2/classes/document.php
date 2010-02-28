<?php


class document {
	
	public $id_document;
	public $id_vignette;
	public $extension;
	public $titre;
	public $date;
	public $descriptif;
	public $fichier;
	public $taille;
	public $largeur;
	public $hauteur;
	public $mode="document";
	public $distant="non";
	public $maj;
	
	
	public function __construct($id_document = NULL) {
		if($id_document) {
			$this->id_document = $id_document;
			/* On fait un select est on met a jour les valeurs */
		}
	}
	
	public function delete() {
		// Quand on aura fait le update
		// unlink("IMG/".$this->fichier);
		spip_query("DELETE FROM `".$GLOBALS['table_prefix']."_documents_liens` WHERE `id_document` = ".$this->id_document);
		spip_query("DELETE FROM `".$GLOBALS['table_prefix']."_documents` WHERE `id_document` = ".$this->id_document);
	}
	
	public function add($file) {
			
		include_spip('inc/ajouter_documents');
		
		$infos = fixer_extension_document($file);
		$this->extension = $infos[0];
		$this->fichier = $infos[0]."/".$infos[1];
		$this->taille = filesize($file['tmp_name']);
		$size_image = @getimagesize($file['tmp_name']);
		$this->largeur = intval($size_image[0]);
		$this->hauteur = intval($size_image[1]);
			
		if(move_uploaded_file($file['tmp_name'], "IMG/".$this->fichier)) {
		
			$add_sql = "INSERT INTO `".$GLOBALS['table_prefix']."_documents` (`id_document`, `id_vignette` ,`extension` ,`titre` ,`date` ,`descriptif` ,`fichier` ,`taille` ,`largeur` ,`hauteur` ,`mode` ,`distant` ,`maj`)
			VALUES (NULL , "._q($this->id_vignette).", "._q($this->extension).", "._q($this->titre).", NOW(), "._q($this->descriptif).", "._q($this->fichier).", "._q($this->taille).", "._q($this->largeur).", "._q($this->hauteur).", "._q($this->mode).", "._q($this->distant).",CURRENT_TIMESTAMP)";
			$result = spip_query($add_sql);
			$this->id_document = mysql_insert_id();
			return $result;
		
		} else {  return false;  }

	}
	
	
	
	
}
?>
