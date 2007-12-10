<?php
/*
| balises
+----------------------------------+
| 
+----------------------------------+
*/
if (!defined("_ECRIRE_INC_VERSION")) return;

/*
| Balise #TABLEAU_SMILEYS
| gaf 0.6 - 30/09/07
| produit le tableau des smileys sur 'n' colonnes
| [(#TABLEAU_SMILEYS)] (defaut : aff. 2 col.)
| [(#TABLEAU_SMILEYS{x})] (ou x provoque aff. de x col. !))
*/
function balise_TABLEAU_SMILEYS_dist($p) {
	$_nb_col = interprete_argument_balise(1,$p);
	$p->code = "tableau_smileys($_nb_col)";
	$p->interdire_scripts = false;
	return $p;
}


/*
| balise #AFFICHE_AVATAR
| gaf 0.6 - 4/10/07
| Peut recevoir un arg. : nom de classe CSS a appliquer sur avatar.
| Envois en contexte modele (#ENV) ou fonction afficher_avatar[_gaf] :
| arg. classe, passe aussi id_auteur !
*/
function balise_AFFICHE_AVATAR_dist($p) {
	$_id_auteur = champ_sql('id_auteur', $p);
	$_classe = interprete_argument_balise(2,$p);
	$p->code = "afficher_avatar($_id_auteur,'$_classe')";
	$p->interdire_scripts = false;
	return $p;
}



/*
| balise #SIGNATURE_POST
| gaf 0.6 - 12/10/07
*/
function balise_SIGNATURE_POST_dist($p) {
	$_id_auteur = champ_sql('id_auteur', $p);
	$p->code = "afficher_signature_post($_id_auteur)";
	$p->interdire_scripts = false;

	return $p;
}

/*
balise standard gafospip, nom champ en arg
retour donnee brut
| gaf 0.6 - 10/11/07
*/
function balise_SPIPBB_dist($p) {
	$_id_auteur = champ_sql('id_auteur', $p);
	$_champ = interprete_argument_balise(1,$p);
	$p->code = "afficher_champ_spipbb($_id_auteur,$_champ)";
	$p->interdire_scripts = true;
	return $p;
}

?>
