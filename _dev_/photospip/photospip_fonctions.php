<?php

/*

@argument balise : balise sur lequel est appliquer le filtre appliquer_filtre
@argument filtre : nom du filtre à appliquer
@param : paramètres pour le filtre 

@return : retourn la balise traité par filtre si le filtre existe autrement la balise appelante non traitée

*/

function photospip_appliquer_filtre($balise, $filtre,$param1=NULL,$param2=NULL,$param3=NULL) {
	if (function_exists($filtre)){
		spip_log("$filtre($balise,$param1,$param2,$param3);","photospip");
		if ($param1){
			return $filtre($balise,$param1,$param2,$param3);
			spip_log("$filtre($balise,$param1,$param2,$param3);","photospip");
		}
		else{
			return $filtre($balise);
			spip_log("$filtre($balise,$param1,$param2,$param3);","photospip");
		}
	} else {
		return balise;
	}
}
?>