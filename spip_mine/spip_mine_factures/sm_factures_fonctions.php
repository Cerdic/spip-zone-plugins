<?php

/**
 * Critère "solde"
 *
 * Ne renvoit que les factures sur lesquelles il reste du crédit
 * C'est à dire les factures avec TOTAL_HEURES_RESTANTES > 0
 *
 */
#function critere_solde($idb, &$boucles, $crit) {
#	/* 
#	Critère utilisé dans une  boucle FACTURE.
#	Si on est pas sur une facture on retourne un message d'erreur
#	Le critère par défaut est "solde >0" (si solde est employé sans paramètre)
#	Le critère inverse ne peut exister : {!solde} quooi que on puisse le traduire par "solde <0"
#	Les paramètres qu'on peut lui passer sont {solde >0}, {solde <0}, {solde = 0}
#	Ajoute un critère dans la requete sql
#	*/
#	
#	global $table_des_tables;
#	$not = $crit->not;
#	$boucle = &$boucles[$idb];

#	$boucle->where[]= array("'>'", "'$boucle->id_table." . "date_facture'", "2010");
#}


/******************************************************************/
/*   TRAITEMENTS SPIP
/******************************************************************/
global  $table_des_traitements;

$table_des_traitements['DATE_FACTURE'][]= 'normaliser_date(%s)';
$table_des_traitements['DATE_LIVRAISON'][]= 'normaliser_date(%s)';
$table_des_traitements['DATE_LIVRAISON_PREVUE'][]= 'normaliser_date(%s)';
$table_des_traitements['DATE_PAYEMENT'][]= 'normaliser_date(%s)';
$table_des_traitements['DATE_REGLEMENT'][]= 'normaliser_date(%s)';
$table_des_traitements['DATE_SAISIE'][]= 'normaliser_date(%s)';
$table_des_traitements['FIN_VALIDITE'][]= 'normaliser_date(%s)';

?>
