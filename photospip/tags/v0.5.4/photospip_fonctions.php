<?php
/**
 * PhotoSPIP
 * Modification d'images dans SPIP
 *
 * Auteurs :
 * kent1 (kent1@arscenic.info -  http://www.kent1.info)
 *
 * © 2008-2012 - Distribue sous licence GNU/GPL
 * Pour plus de details voir le fichier COPYING.txt
 *
 */

if (!defined('_ECRIRE_INC_VERSION')) return;
/*

@argument balise : balise sur lequel est appliquer le filtre appliquer_filtre
@argument filtre : nom du filtre à appliquer
@param : paramètres pour le filtre 

@return : retourn la balise traité par filtre si le filtre existe autrement la balise appelante non traitée

*/

function photospip_appliquer_filtre($balise, $filtre,$param1=null,$param2=null,$param3=null) {
	$filtre = chercher_filtre($filtre);
	if (function_exists($filtre)){
		if (isset($param1) && $param1){
			if($filtre == 'image_sepia')
				$param1 = str_replace('#','',$param1);
			return $filtre($balise,$param1,$param2,$param3);
		}
		else{
			return $filtre($balise);
		}
	} else {
		return $balise;
	}
}
?>