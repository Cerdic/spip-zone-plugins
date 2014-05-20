<?php

/* Retourne le chemin correct du repertoire de sauvegarde en fonction de
* a) ce qui est passé comme argument
* b) le fait d'être ou pas dans l'espace privé. Ajout / ou suppression du ..
*/
function saveauto_repertoire_save($rep){
	if (test_espace_prive()){
		if(substr($rep,0,3)!="../"){
			$rep = "../".$rep;
		}
	}
	else{
		$rep = str_replace("../","",$rep);
	}
	return $rep;
}


?>
