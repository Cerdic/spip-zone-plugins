<?php

/*

@argument balise : balise sur lequel est appliquer le filtre appliquer_filtre
@argument filtre : nom du filtre à appliquer
@param : paramètres pour le filtre 

@return : retourn la balise traité par filtre si le filtre existe autrement la balise appelante non traitée

*/

function photospip_appliquer_filtre($balise, $filtre,$param=NULL) {
	if (function_exists($filtre)){
		if ($param){
			return $filtre($balise,$param);			
		}
		else{
			return $filtre($balise);
		}
	} else {
		return balise;
	}
}
?>