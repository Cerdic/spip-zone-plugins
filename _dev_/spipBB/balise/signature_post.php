<?php
/*
| ex gaf_balises Scoty
+----------------------------------+
| 
+----------------------------------+*/

if (!defined("_ECRIRE_INC_VERSION")) return;
spip_log(__FILE__.' : included','spipbb');

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

?>
