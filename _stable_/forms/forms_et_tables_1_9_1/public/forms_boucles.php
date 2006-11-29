<?php

/*
 * forms
 * Gestion de formulaires editables dynamiques
 *
 * Auteurs :
 * Antoine Pitrou
 * Cedric Morin
 * Renato
 * © 2005,2006 - Distribue sous licence GNU/GPL
 *
 */

	include_spip('base/forms');
	//
	// <BOUCLE(FORMS)>
	//
	/*function boucle_FORMS_dist($id_boucle, &$boucles) {
		$boucle = &$boucles[$id_boucle];
		$id_table = $boucle->id_table;
		$boucle->from[$id_table] =  "spip_forms";
	
		if (!isset($boucle->modificateur['tout'])){
			$boucle->where[]= array("'='", "'$id_table.public'", "'oui'");
			$boucle->group[] = $boucle->id_table . '.champ'; // ?  
		}
		return calculer_boucle($id_boucle, $boucles); 
	}*/
	
	//
	// <BOUCLE(FORMS_DONNEES)>
	//
	function boucle_FORMS_DONNEES_dist($id_boucle, &$boucles) {
		$boucle = &$boucles[$id_boucle];
		$id_table = $boucle->id_table;
		$boucle->from[$id_table] =  "spip_forms_donnees";
	
		if (!isset($boucle->modificateur['tout']) && !$boucle->tout)
			$boucle->where[]= array("'='", "'$id_table.confirmation'", "'\"valide\"'");
		if (!$boucle->statut && !isset($boucle->modificateur['tout']) && !$boucle->tout)
			$boucle->where[]= array("'='", "'$id_table.statut'", "'\"publie\"'");
	
		return calculer_boucle($id_boucle, $boucles); 
	}

	//
	// <BOUCLE(FORMS_CHAMPS)>
	//
	function boucle_FORMS_CHAMPS_dist($id_boucle, &$boucles) {
		$boucle = &$boucles[$id_boucle];
		$id_table = $boucle->id_table;
		$boucle->from[$id_table] =  "spip_forms_champs";
	
		if (!isset($boucle->modificateur['tout']) && !$boucle->tout){
			$boucle->where[]= array("'='", "'$id_table.public'", "'\"oui\"'");
		}
	
		return calculer_boucle($id_boucle, $boucles); 
	}
	
	//
	// <BOUCLE(FORMS_DONNEES_CHAMPS)>
	//
	function boucle_FORMS_DONNEES_CHAMPS_dist($id_boucle, &$boucles) {
		$boucle = &$boucles[$id_boucle];
		$id_table = $boucle->id_table;
		$boucle->from[$id_table] =  "spip_forms_donnees_champs";
	
		if (!isset($boucle->modificateur['tout']) && !$boucle->tout){
			$boucle->from["champs"] =  "spip_forms_champs";
			$boucle->from["donnees"] =  "spip_forms_donnees";
			$boucle->where[]= array("'='", "'$id_table.id_donnee'", "'donnees.id_donnee'");
			$boucle->where[]= array("'='", "'$id_table.champ'", "'champs.champ'");
			$boucle->where[]= array("'='", "'donnees.id_form'", "'champs.id_form'");
			$boucle->where[]= array("'='", "'champs.public'", "'\"oui\"'");
			$boucle->group[] = $boucle->id_table . '.champ'; // ?  
		}
		if (!$boucle->statut && !isset($boucle->modificateur['tout']) && !$boucle->tout)
			$boucle->where[]= array("'='", "'donnees.statut'", "'\"publie\"'");

		return calculer_boucle($id_boucle, $boucles); 
	}
	
?>