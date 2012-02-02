<?php
// Auteur : JLuc
//
// inc/formitable.php
// - fonctions de traitement des champs
// - petites fonctions utilitaires

//==============================
// Fonctions de traitements des champs

// convertir le format des dates issu de formidable à un format SQL de type DATE
// si la date est un tableau, c'est dans l'ordre jour mois an (31, 01, 2012)
function traitement_date_fr_vers_sql ($date) {
    spip_log ("traitement_date_fr_vers_sql de ".print_r($date,true),"formitable");
    if (is_array($date)) {
        return $date[2]."-".$date[1]."-".$date[0];
    }
    $jour=$mois=$an="";
    if (sscanf ($date, "%2d/%2d/%4d", $jour, $mois, $an))
	    return "$an-$mois-$jour";
};

// function traitement_champ_bateau_couleur ($valeur, $table, $champ, $nom)
// function traitement_champ_date_debut ($valeur, $table, $champ, $nom)
// function traitement_champ_evenement_date_debut ($valeur, $table, $champ, $nom)
// function traitement_champ ($valeur, $table, $nom)
//
// - $valeur : valeur renvoyee par la saisie. string ou array.
// - $table : table SQL utilisateur destinatrice des données
// - $champ : champ de la table SQL
// - $nom : id de l'input dans le formulaire généré par spip

/* exemples d'usage :
    function traitement_champ_naissance ($valeur, $table, $champ, $nom) {
    	return traitement_date_fr_vers_sql($valeur);
    };

    function traitement_champ_lestrois ($valeur, $table, $champ, $nom) {
    	return traitement_date_fr_vers_sql($valeur);
    };

// pour que toutes les saisies DATES soient stockées dans un format un champt de type DATE
function traitement_champ ($valeur, $table, $champ, $nom) {
	if (strpos($nom,'date')===0) {
		$valeur = traitement_date_fr_vers_sql($valeur);
//		spip_log ("Reçu date fr et convertit vers SQL = $valeur","formitable");
	};
	return $valeur;
}; */

//==============================
// Autres utilités
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