<?php


class mot {
	
	public $id_mot;
	public $titre;
	public $descriptif;
	public $texte;
	public $id_groupe;
	public $type;
	public $extra;
	public $maj;

	
	
	public function __construct($id_mot = NULL) {
		if($id_mot) {
			$this->$id_mot = $id_mot;
			// On fait un select est on met a jour les valeurs
		}
	}
	
	
	// Creer un mot SPIP
	public function add() {
		$add_sql = "INSERT INTO `".$GLOBALS['table_prefix']."_mots` (`id_mot` ,`titre` ,`descriptif` ,`texte` ,`id_groupe` ,`type` ,`extra` ,`maj`) 
		VALUES (NULL , "._q($this->titre).", "._q($this->descriptif).", "._q($this->texte).", '".$this->id_groupe."', "._q($this->type).", "._q($this->extra).", NOW())";
		$result = spip_query($add_sql);
		$this->id_mot = mysql_insert_id();
		return $result;
	}
	
	
	public function set_groupe($id_groupe) {
		$this->id_groupe = $id_groupe;
		$get_groupe_result = spip_query("SELECT titre FROM `".$GLOBALS['table_prefix']."_groupes_mots` WHERE id_groupe=".$id_groupe);
		$get_groupe_row = spip_fetch_array($get_groupe_result);
		$this->type = $get_groupe_row['titre'];
	}
	
	
	
	
}
?>
