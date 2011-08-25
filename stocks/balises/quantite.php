<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/stocks');

// Retourner une quantie pour l'objet en cours
function balise_QUANTITE_dist($p) {
	if (!$_type = interprete_argument_balise(1,$p)){
		$_objet = objet_type($p->type_requete);
		$_id = champ_sql($p->boucles[$p->id_boucle]->primary,$p);
	}
	else {
		$_id = interprete_argument_balise(2,$p);
    }

	$p->code = "get_quantite(".sql_quote($_objet).",$_id)";
	$p->interdire_scripts = false;
	return $p;
}

?>
