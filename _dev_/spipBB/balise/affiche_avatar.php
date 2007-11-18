<?php
/*
| ex gaf_balises Scoty
+----------------------------------+
| 
+----------------------------------+*/

include_spip('inc/spipbb');

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

?>
