<?php

class cfg_depot_dist{
	
	var $depot;
	
	function cfg_depot_dist($depot='metapack', &$cfg){
		include_spip('inc/depot/'.$depot);
		if (!class_exists($class = 'cfg_'.$depot))
			die("CFG ne trouve pas le d&eacute;pot $depot");
			
		$this->depot = &new $class($cfg);
	}
	
	function lire(){
		return $this->depot->lire();	
	}
		
	function ecrire(){
		return $this->depot->modifier(false);	
	}
	
	function effacer(){
		return $this->depot->modifier(true);	
	}	
}
?>
