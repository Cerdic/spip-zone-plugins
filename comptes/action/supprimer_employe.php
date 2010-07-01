<?php
/**
 * Plugin Comptes & Contacts pour Spip 2.0
 * Licence GPL (c) 2010 - Apsulis
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Supprimer un employé comptes
 *
 */
/*
	TODO Peut etre gerer en deux temps :
	1) Modale pour dire "etes vous surs"
	2) Bouton d'action dans la modale
*/

function action_supprimer_employe_dist() {
	$securiser_action = charger_fonction('securiser_action','inc');
	$id = $securiser_action();
	supprimer_employe($id);
	return true;
}

function supprimer_employe($comptes){
	include_spip('base/abstract_sql');

	$n = sql_delete("spip_auteurs_comptes_specifique", "id_comptes=".intval($comptes));

	if($n){
		spip_log("Suppression de l'employé $comptes");
		echo _T('gestion:suppression_employe', array('id'=>$comptes));
		return true;
	}	
	return false;
}

?>