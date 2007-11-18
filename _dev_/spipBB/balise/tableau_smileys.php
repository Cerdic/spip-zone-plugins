<?php
/*
| ex gaf_balises Scoty
+----------------------------------+
| 
+----------------------------------+*/

include_spip('inc/spipbb');

/*
+----------------------------------+
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

?>
