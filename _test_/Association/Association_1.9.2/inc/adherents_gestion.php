<?php

include_spip('inc/presentation');

// La fonction qui en appelle une autre et qui va nous modifier notre base de donnee comme on en a envie...

function association_ajouts()
{
// On récupère les globales nécessaires
	global $id_auteur, $redirect, $echec, $initial,
	  $connect_statut, $connect_toutes_rubriques, $connect_id_auteur;

	if (_request('new') == 'oui') {
		$new = true;
	} 
	if ($new) {
		return '';
	} else {

	$id_auteur = intval($id_auteur);

// On crée un array des données associées à un auteur...
	$auteur = spip_fetch_array(spip_query("SELECT * FROM spip_asso_adherents WHERE id_auteur=$id_auteur"));

// On récupère le fichier qui contient ce dont on a besoin
	$legender_auteur_supp = charger_fonction('legender_auteur_supp', 'inc');

// On lui passe en paramètre ce qui nous est nécessaire
	$legender_auteur_supp_total = $legender_auteur_supp($id_auteur, $auteur, $initial, $echec, $redirect);

	if (_request('var_ajaxcharset')) ajax_retour($legender_auteur_supp_total);

// On balance ce dont on a besoin
	return $legender_auteur_supp_total;
	}
}
?>