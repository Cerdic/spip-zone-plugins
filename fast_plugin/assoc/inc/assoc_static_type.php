<?php

class assoc_static_type{
	
	/* definition en statique des differents stype de contenu */
	public static function type(){
		$type["article"] = 1;
		$type["rubrique"] = 2;
		$type["actuphonore"] = 3;
		$type["video"] = 4;
		return $type;
	}
	
	/* definition en statique des differents stype de contenu */
	public static function type_id(){
		$type[1] = "article";
		$type[2] = "rubrique";
		$type[3] = "actuphonore";
		$type[4] = "video";
		return $type;
	}
	
	
	
}


?>