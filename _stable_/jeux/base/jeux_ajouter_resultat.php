<?
function jeux_ajouter_resulat($id_jeu,$resultat,$resultat_detaille=''){
	$id_auteur =  $GLOBALS["auteur_session"]['id_auteur'];
	if (!$id_auteur){return;}
	
	
	$requete	= spip_fetch_array(spip_query("SELECT id_resultat,enregistrer_resultat FROM spip_jeux WHERE id_jeu =".$id_jeu));
	$enregistrer_resultat 	= $requete['enregistrer_resultat'];
	
	
	$id_resultat			= $requete['id_resultat']; 		
	if ($enregistrer_resultat=='non'){return;}
	 
	if (lire_config('jeux/ecraser_resultat')!='non'){
		
		spip_query('INSERT into  spip_jeux_resultats (id_jeu,id_auteur,resultat_court,resultat_long) VALUES ('.$id_jeu.','.$id_auteur.',"'.$resultat.'","'.$resultat_detaille.'")'); 
		}
	else
		{
		$requete = spip_fetch_array(spip_query("SELECT id_resultat FROM spip_jeux_resultats WHERE id_jeu =".$id_jeu." and id_auteur=".$id_auteur));
		$id_resultat = $requete['id_resultat'];
		
		if (!$id_resultat){
			spip_query('INSERT into  spip_jeux_resultats (id_jeu,id_auteur,resultat_court,resultat_long) VALUES ('.$id_jeu.','.$id_auteur.',"'.$resultat.'","'.$resultat_detaille.'")'); 
			}
		
		else{
			spip_query('UPDATE spip_jeux_resultats SET resultat_court="'.$resultat.'",resultat_long="'.$resultat_detaille.'" WHERE id_resultat='.$id_resultat);
			}
		
		}
	}
?>