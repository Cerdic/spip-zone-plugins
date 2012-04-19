<?php

// Détermine l'id_parent de la nouvell rubrique traduite
function destination_traduction($lang,$id_trad){

	// on établit l'id_parent
	$id_trad_parent=sql_getfetsel('id_parent','spip_rubriques','id_rubrique='.$id_trad);
	
	//puis sa traduction
	$id_parent_trad=sql_getfetsel('id_trad','spip_rubriques','id_rubrique='.$id_trad_parent);
	
	// S'il il existe une traduction parente dans la langue demandé on retourne son id
	if($id_parent_trad) $trads = sql_getfetsel('id_rubrique','spip_rubriques','id_trad='.$id_parent_trad.' AND lang='.sql_quote($lang));
	//Sinon on cherche à la racine	 et on retourne l'id d'une rubrique non traduite
 	else{
 		$trads = sql_getfetsel('id_rubrique','spip_rubriques','id_parent=0 AND id_trad=0 AND lang='.sql_quote($lang));	
		}
return $trads;
}	 
?>