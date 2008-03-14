<?php
function jeux_ajouter_resulat($id_jeu, $resultat, $resultat_detaille=''){
	$id_auteur = $GLOBALS["auteur_session"]['id_auteur'];
	if (!$id_auteur) return;
	$requete = spip_fetch_array(spip_query("SELECT enregistrer_resultat FROM spip_jeux WHERE id_jeu =".$id_jeu));
	if ($requete['enregistrer_resultat']=='non') return;

	if (function_exists('lire_config')) 
		$ecraser_resultat = lire_config('jeux/ecraser_resultat');
	else
		$ecraser_resultat = 'dernier_resultat';
	
	$resultat = intval($resultat);

	// on insere ou on remplace ?
	$insert = $ecraser_resultat=='non';
	// si on remplace, verifier quel type de resultat : le meilleur ou le dernier
	if (!$insert) {
		$requete = spip_fetch_array(spip_query("SELECT resultat_court,id_resultat FROM spip_jeux_resultats WHERE id_jeu=$id_jeu and id_auteur=$id_auteur"));
		if(!$requete) 
			// rien dans la base => on insere
			$insert = true;
		else {
			// sinon, on remplace...
			$resultat_court = intval($requete['resultat_court']);
			// ... a condition d'avoir fait mieux !
			if ($ecraser_resultat=='meilleur_resultat' && $resultat_court>=$resultat)
				return;
			$id_resultat = $requete['id_resultat'];
		}
	}

	$resultat = _q($resultat);
	$resultat_detaille = _q($resultat_detaille);
	if($insert)
		spip_query("INSERT into spip_jeux_resultats (id_jeu,id_auteur,resultat_court,resultat_long) VALUES ($id_jeu,$id_auteur,$resultat,$resultat_detaille)"); 
	else
		spip_query("UPDATE spip_jeux_resultats SET resultat_court=$resultat,resultat_long=$resultat_detaille WHERE id_resultat=$id_resultat");

}
?>