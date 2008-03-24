<?php

class cfg_depot_dist{
	
	var $nom;
	var $depot;
	
	function cfg_depot_dist($depot='metapack', &$cfg=null, &$params=array()){
		include_spip('inc/depot/'.$depot);
		
		if (class_exists($class = 'cfg_depot_'.$depot)) {
			$this->depot = &new $class($params);
		} elseif (class_exists($class = 'cfg_'.$depot)) {
			$this->depot = &new $class($cfg, $params);
		} else {
			die("CFG ne trouve pas le d&eacute;pot $depot");
		}
		
		$this->nom = $depot;
	}
	
	// ajoute les parametres transmis dans l'objet du depot
	function add_params(&$params){
		foreach ($params as $o=>&$v) {
			$this->depot->$o = &$v;
		}	
	}
	
	function lire(&$params = array()){
		$this->add_params($params);
		return $this->depot->lire();	
	}
		
	function ecrire(&$params = array()){
		$this->add_params($params);
		if (method_exists($this->depot, 'ecrire'))
			return $this->depot->ecrire();
		else
			return $this->depot->modifier(false);	
	}
	
	function effacer(&$params = array()){
		$this->add_params($params);
		if (method_exists($this->depot, 'effacer'))
			return $this->depot->effacer();
		else
			return $this->depot->modifier(true);		
	}	
	
	function lire_config(){
		$s = $this->depot->lire();
		if ($nom = $this->nom_champ())
			return $s[$nom];
			
		return null;
	}
	
	function ecrire_config($valeur){
		if ($nom = $this->nom_champ())
			$this->depot->val = array($nom=>$valeur);
		
		return $this->depot->ecrire();	
	}
	
	function effacer_config(){
		if ($nom = $this->nom_champ()){
			$this->depot->val[$nom] = false;
			return $this->depot->effacer();	
		}
	}	
	
	function nom_champ(){
		if (count($this->depot->champs)==1){
			foreach ($this->depot->champs as $nom=>$def){
				return $nom;	
			}
		}
		return false;			
	}
	
	// charge les arguments d'un lire/ecrire/effacer_config
	// dans le depot : lire_config($args = 'metapack::prefixe/casier/champ');
	function charger_args($args){
		if (method_exists($this->depot, 'charger_args')){
			return $this->depot->charger_args($args);	
		}
		return false;
	}
}
?>
