<?
function jeux_ajouter_resulat($id_jeu,$resultat,$resultat_detaille=''){
	
	$id_auteur =  $GLOBALS["auteur_session"]['id_auteur'];
	if (!$id_auteur){return;}
	spip_query('INSERT into  spip_jeux_resultats (id_jeu,id_auteur,score_court,score_long) VALUES ('.$id_jeu.','.$id_auteur.',"'.$resultat.'","'.$resultat_detaille.'")'); 
	}
?>