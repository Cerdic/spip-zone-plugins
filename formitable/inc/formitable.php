<?php


// convertir le format des dates issu de formidable � un format SQL de type DATE
function traitement_date_fr_vers_sql ($date) {
	sscanf ($date, "%2d/%2d/%4d%s",$jour,$mois,$an,$s);
	return "$an-$mois-$jour";
};

// Exemples :
// - $valeur : valeur renvoy�e par la saisie
// - $table : table SQL utilisateur destinatrice des donn�es
// - $champ : champ de la table SQL
// - $nom : id de l'input dans le formulaire g�n�r� par spip
//
// function traitement_champ_bateau_couleur ($valeur, $table, $champ, $nom)
// function traitement_champ_date_debut ($valeur, $table, $champ, $nom)
// function traitement_champ_evenement_date_debut ($valeur, $table, $champ, $nom)
// function traitement_champ ($valeur, $table, $nom)

function traitement_champ_naissance ($valeur, $table, $champ, $nom) {
	return traitement_date_fr_vers_sql($valeur);
};

/* exemple d'usage :
function traitement_champ ($valeur, $table, $champ, $nom) {
	// Toutes les saisies DATES sont stock�es dans un format un champt de type DATE
	if (strpos($nom,'date')===0) {
		$valeur = traitement_date_fr_vers_sql($valeur);
//		echo "<br>Re�u date fr et convertit vers SQL = $valeur";
	};
	return $valeur;
}; */


/*
 * Génère le nom du cookie qui sera utilisé par le plugin lors d'une réponse
 * par un visiteur non-identifié.
 *
 * @param int $id_formulaire L'identifiant du formulaire
 * @return string Retourne le nom du cookie
 */
function formitable_generer_nom_cookie($id_formulaire){
	return $GLOBALS['cookie_prefix'].'cookie_formitable_'.$id_formulaire;
}

?>