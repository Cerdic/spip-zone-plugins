<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

/******************************************************************/
/*   FONCTIONS DE CALCUL POUR LES FACTURES
/******************************************************************/
function balise_TOTAL_HT_dist($p) {
	// retourne le montant total HT d'une facture
	// prend toutes les lignes d'une facture
	// pour chaque ligne effectue la multiplication "quantité" * "prix unitaire_ht"
	// fait le total pour l'ensemble des lignes
	
	$_id_facture = $p->boucles[$p->id_boucle]->primary;
	$id_facture = champ_sql($_id_facture, $p);
	
	$p->code = "calculer_balise_TOTAL_HT($id_facture)";
	return $p;
}

function calculer_balise_TOTAL_HT($id_facture) {
    $table_lignes_facture = "spipmine_lignes_facture"; 
	$total_ht = 0;
	$total_ht = sql_getfetsel(
	    "SUM(quantite * prix_unitaire_ht)",
	    $table_lignes_facture,
	    "id_facture=1"
	);
	
	return $total_ht ? $total_ht : NULL;

}
?>
