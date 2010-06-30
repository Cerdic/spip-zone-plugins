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
	$total_ht = 0;
	$sql = "SELECT (prix_unitaire_ht * quantite) AS total_ligne FROM `cym_lignes_facture` WHERE id_facture = (SELECT id_facture FROM `cym_factures` WHERE id_facture ='$id_facture')";
	$query = spip_query($sql);
	while ($lignes = spip_fetch_array($query)){
		$total_ligne = $lignes['total_ligne'];
		$total_ht = $total_ht + $total_ligne;
	}
	
	
	if ($total_ht) {return "$total_ht";} else {return NULL;}

}
?>
