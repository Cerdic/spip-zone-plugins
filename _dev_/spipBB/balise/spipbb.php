<?php
/*
| ex gaf_balises Scoty
+----------------------------------+
| 
+----------------------------------+

/*
balise standard gafospip, nom champ en arg
retour donnee brut
| gaf 0.6 - 10/11/07
*/

include_spip('inc/spipbb');

function balise_SPIPBB_dist($p) {
	$_id_auteur = champ_sql('id_auteur', $p);
	$_champ = interprete_argument_balise(1,$p);
	$p->code = "afficher_champ_spipbb($_id_auteur,$_champ)";
	$p->interdire_scripts = true;
	return $p;
}

?>
