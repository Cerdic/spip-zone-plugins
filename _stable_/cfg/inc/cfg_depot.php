<?php

class cfg_depot{
	
	var $depot;
	
	function cfg_depot($depot='metapack', &$cfg, $opt=array()){
		include_spip('inc/depot/'.$depot);
		if (!class_exists($class = 'cfg_'.$depot))
			die("CFG ne trouve pas le d&eacute;pot $depot");
			
		$this->depot = &new $class($cfg, $opt);
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
