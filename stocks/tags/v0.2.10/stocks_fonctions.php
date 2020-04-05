<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

include_spip('inc/stocks');


function balise_EN_STOCK_dist($p) {

	if (!$_objet = interprete_argument_balise(1, $p)) {
		$_objet = objet_type($p->type_requete);
		$_id_objet = $p->boucles[$p->id_boucle]->primary;
		$_id= champ_sql($_id_objet, $p);
	} else {
		$_objet = interprete_argument_balise(1, $p);
		$_id = interprete_argument_balise(2, $p);
	}
	$p->code = 'quantite_champ_ou_stock('.champ_sql('quantite', $p).','.sql_quote($_objet).",$_id)";
	$p->interdire_scripts = false;

	return $p;
}



// Retourner une quantie pour l'objet en cours
//function balise_QUANTITE_dist($p) {
//	if (!$_objet = interprete_argument_balise(1, $p)) {
//		$_objet = objet_type($p->type_requete);
//		$_id = champ_sql($p->boucles[$p->id_boucle]->primary, $p);
//	} else {
//		$_objet = interprete_argument_balise(1, $p);
//		$_id = interprete_argument_balise(2, $p);
//	}
//
//	$p->code = 'quantite_champ_ou_stock('.champ_sql('quantite', $p).','.sql_quote($_objet).',$_id)';
//	$p->interdire_scripts = false;
//
//	return $p;
//}

function quantite_champ_ou_stock($quantite, $objet, $id_objet) {
	include_spip('inc/stocks');
	if (is_null($quantite)) {
		return get_quantite($objet, $id_objet);
	} else {
		spip_log($quantite, 'stocks');
		return $quantite;
	}
}
