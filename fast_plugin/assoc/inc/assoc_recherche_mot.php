<?php




class assoc_recherche_mots{
	
	private $valid;
	private $table;
	private $type;
	private $select_field;
	private $final_mots;
	
	
	// $table correspond a table sur laquelle on effectue la recherche
	// $mots correspond aux mots rechercher
	// $type determine si la recherche se fait sur tous les mots en meme temps ou pas
	// $select_field correspond a un tableau avec les champs que l'on souhaite exploiter
	// en sachenat que le premier correspond a l'identifiant
	public function __construct($table,$mots,$type,array $select_field){
		
		// On voit tous les mots qui ont été proposés
		$mots = explode(" ",$mots);
		for ($i = 0; $i < count($mots); $i++){
			$a = trim($mots[$i]);
			if (strlen($a)>3) $final_mots[]  = $mots[$i];
		}
		
		if (count($final) > 3 ) {
			$this->valid["valid"]= false;
			$this->valid["erreur"]= "La recherche s'effectue sur 3 mots aux maximun";
		}else{
			
			$this->type = $type;
			if (count($final) == 1 ) $this->type = "seul";
			$this->valid["valid"]= true;
			$this->valid["recherche"]= $this->type;
			$this->table = $table;
			$this->select_field = $select_field;
			$this->final_mots = $final_mots;
		}
	}
	
	
	public function get_valid_search(){
		return $this->valid;
	}
	
	public function recherche_seul(){
		
		$final = $this->final_mots;
		$table = $this->table;
		$select_field = $this->select_field;
		$idt = $select_field[0];
		$champs = implode(",",$select_field);
		
		$retour = array();
		
		for ($a = 0; $a < count($final); $a++){
			// Partie pour les sites references
			// test sur les descriptifs
			$val = $this->sans_accent_mysql($final[$a]);
			
			$sql ="SELECT $champs from $table WHERE `descriptif` LIKE CONVERT(_utf8 '%$val%' USING utf8) COLLATE utf8_general_ci ;";
			$res = spip_query($sql);
			while ($row = spip_fetch_array($res)){
				$id = $row[$idt];
				for ($i=1;$i<count($select_field);$i++)$retour[$id][$select_field[$i]] = $row[$select_field[$i]];
			}
			
			$sql ="SELECT $champs from $table WHERE `titre` LIKE CONVERT(_utf8 '%$val%' USING utf8) COLLATE utf8_general_ci ;";
					
			$res = spip_query($sql);
			while ($row = spip_fetch_array($res)){
				$id = $row[$idt];
				for ($i=1;$i<count($select_field);$i++)$retour[$id][$select_field[$i]] = $row[$select_field[$i]];
			}
		}
		
		return $retour;
	}
	
	
	public function recherche_tous(){
		
		$final = $this->final_mots;
		$table = $this->table;
		$select_field = $this->select_field;
		$idt = $select_field[0];
		$champs = implode(",",$select_field);
		
		$provi = array();
		for ($i = 0; $i < count($final); $i++){
			// test sur les descriptifs
			$val = $this->sans_accent_mysql($final[$i]);
			$sql ="SELECT $champs from $table  WHERE `descriptif` LIKE CONVERT(_utf8 '%$val%' USING utf8) COLLATE utf8_general_ci ;";
			$res = spip_query($sql);
			while ($row = spip_fetch_array($res)){
				$id = $row[$idt];
				for ($a=1;$a<count($select_field);$a++)$provi[$i][$id][$select_field[$a]] = $row[$select_field[$a]];
			}
		
			$sql ="SELECT $champs from $table  WHERE `titre` LIKE CONVERT(_utf8 '%$val%' USING utf8) COLLATE utf8_general_ci ;";
			$res = spip_query($sql);
			while ($row = spip_fetch_array($res)){
				$id = $row[$idt];
				for ($a=1;$a<count($select_field);$a++)$provi[$i][$id][$select_field[$a]] = $row[$select_field[$a]];
			}
		}

		$a = @array_keys($provi[0]);
		
		$trois = false;
		$test = false;
		if (count($final)==3) $trois = true;
		
		for ($i = 0; $i < count($a); $i++){
			if (@array_key_exists($a[$i],$provi[1])) $test = true;
			if ($test && $trois){
				if (@!array_key_exists($a[$i],$provi[2])) $test = false;
			}
			if ($test) $retour[$a[$i]] = $provi[1][$a[$i]];
			$test = false;
		}

		return $retour;
		
	}
	
	
	private function sans_accent_mysql($val){
		$accent = array("a","à", "â" ,"e", "ê" , "é", "è" ,"ë" , "i" ,"î" ,"ï" , "o" ,"ô" ,"u" ,"û" );
		$val = str_replace($accent, "_", $val);
		return $val;
	}
	
	
	
}






?>