<?php

class cfg_depot_dist{
	
	var $depot;
	
	function cfg_depot_dist($depot='metapack', &$cfg, &$params=array()){
		include_spip('inc/depot/'.$depot);
		
		if (class_exists($class = 'cfg_depot_'.$depot)) {
			$this->depot = &new $class($params);
		} elseif (class_exists($class = 'cfg_'.$depot)) {
			$this->depot = &new $class($cfg, $params);
		} else {
			die("CFG ne trouve pas le d&eacute;pot $depot");
		}
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
}
?>
