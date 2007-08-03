<?
function jeux_ajouter_resulat($id_jeu,$resultat,$resultat_detaille=''){
	$id_auteur =  $GLOBALS["auteur_session"]['id_auteur'];
	if (!$id_auteur){return;}
	
	$requete	= spip_fetch_array(spip_query("SELECT enregistrer_resultat FROM spip_jeux WHERE id_jeu =".$id_jeu));
	$enregistrer_resultat = $requete['enregistrer_resultat'];
	
	if ($enregistrer_resultat=='non'){return;}
	 
	
	spip_query('INSERT into  spip_jeux_resultats (id_jeu,id_auteur,resultat_court,resultat_long) VALUES ('.$id_jeu.','.$id_auteur.',"'.$resultat.'","'.$resultat_detaille.'")'); 
	}
?>