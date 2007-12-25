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

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/spipbb_common');
spipbb_log('included',3,__FILE__);

function balise_SPIPBB_dist($p) {
	$_id_auteur = champ_sql('id_auteur', $p);
	$_champ = interprete_argument_balise(1,$p);
	$p->code = "afficher_champ_spipbb($_id_auteur,$_champ)";
	$p->interdire_scripts = true;
	return $p;
}

?>
