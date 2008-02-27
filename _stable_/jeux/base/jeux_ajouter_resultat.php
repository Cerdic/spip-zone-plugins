<?php
function jeux_ajouter_resulat($id_jeu, $resultat, $resultat_detaille=''){
	$id_auteur = $GLOBALS["auteur_session"]['id_auteur'];
	if (!$id_auteur) return;

	$requete = spip_fetch_array(spip_query("SELECT id_resultat,enregistrer_resultat FROM spip_jeux WHERE id_jeu =".$id_jeu));
	$enregistrer_resultat = $requete['enregistrer_resultat'];

	$id_resultat = $requete['id_resultat']; 		
	if ($enregistrer_resultat=='non') return;

	if (function_exists('lire_config')) 
		$ecraser_resultat = lire_config('jeux/ecraser_resultat');
	else
		$ecraser_resultat = 'dernier_resultat';
	
	$resultat_detaille = _q($resultat_detaille);
	$resultat = _q($resultat);
	
	if ($ecraser_resultat=='non') {
		spip_query("INSERT into spip_jeux_resultats (id_jeu,id_auteur,resultat_court,resultat_long) VALUES ($id_jeu,$id_auteur,$resultat,$resultat_detaille)"); 
		
	} else {
		$requete = spip_fetch_array(spip_query("SELECT resultat_court, id_resultat FROM spip_jeux_resultats WHERE id_jeu =$id_jeu and id_auteur=$id_auteur"));
		$id_resultat = $requete['id_resultat'];
		$resultat_court = $requete['resultat_court'];
		if (!$id_resultat and !($ecraser_resultat=='dernier_resultat' or ($ecraser_resultat=='meilleur_resultat' and $resultat_court>$resultat and $resultat_court)))
			spip_query("INSERT into spip_jeux_resultats (id_jeu,id_auteur,resultat_court,resultat_long) VALUES ($id_jeu,$id_auteur,$resultat,$resultat_detaille)"); 
		else
			spip_query("UPDATE spip_jeux_resultats SET resultat_court=$resultat,resultat_long=$resultat_detaille WHERE id_resultat=$id_resultat");
	}
}
?>