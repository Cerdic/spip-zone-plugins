<?php

include_spip('inc/presentation');

// La fonction qui en appelle une autre et qui va nous modifier notre base de donnee comme on en a envie...

function auteurs_complets_ajouts() {

	// On récupère les globales nécessaires
	global $id_auteur, $redirect, $echec, $initial,
	  $connect_statut, $connect_toutes_rubriques, $connect_id_auteur;

	$id_auteur = intval(_request('id_auteur'));

// On crée un array des données associées à un auteur...
	$auteur = spip_fetch_array(spip_query("SELECT * FROM spip_auteurs_elargis WHERE id_auteur=$id_auteur"));

// On récupère le fichier qui contient ce dont on a besoin
	$legender_auteur_supp = charger_fonction('legender_auteur_supp', 'inc');
	$fiche_supp = $legender_auteur_supp($auteur);
	
// On lui passe en paramètre ce qui nous est nécessaire
	//return $legender_auteur_supp($id_auteur, $auteur);
	return $fiche_supp;
}
?>
