<?php

function i2_inscrit_partiel($id){
	// est-ce un email connu à qui il manque des champs obligatoires (inscrit partiellement par spip listes par ex) ?					
				
	// champs obligatoires
	$chercher_champs = charger_fonction('inscription2_champs_obligatoires','inc');
	$champs = $chercher_champs();

	// champs de l'inscrit
	$res = sql_fetsel('*',"spip_auteurs_elargis","id_auteur = $id");
															
	foreach($champs as $val){
		// s'il en manque un, on pose une session et on laisse passer
		if($res[$val]==''){
		i2_poser_session($id);
		return true ;
		}
	}
	return false ;	
}


?>