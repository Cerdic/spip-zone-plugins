<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * Supprimer un auteur comptes
 *
 */
/*
	TODO Peut etre gerer en deux temps :
	1) Modale pour dire "etes vous surs"
	2) Bouton d'action dans la modale
*/

function action_supprimer_auteur_comptes_dist() {
	$securiser_action = charger_fonction('securiser_action','inc');
	$id = $securiser_action();
	supprimer_auteur_comptes($id);
	return true;
}

function supprimer_auteur_comptes($comptes){
	include_spip('base/abstract_sql');

	$auteur = sql_allfetsel('id_auteur','spip_auteurs_comptes_specifique',"id_comptes='$comptes'");
	$id_auteur = $auteur[0]['id_auteur'];
	
	$n1 = sql_delete("spip_auteurs", "id_auteur=".intval($id_auteur));
	if($n1)
		$n = sql_delete("spip_auteurs_comptes_specifique", "id_auteur=".intval($id_auteur));
	if($n)
		if(defined('_DIR_PLUGIN_ACCESRESTREINT'))
			$n2 = sql_delete("spip_zones_auteurs", "id_auteur=".intval($id_auteur));

	if($n){
		spip_log("Suppression de l'auteur $id_auteur");
		echo _T('gestion:suppression_auteur', array('id'=>$id_auteur));
		return true;
	}	
	return false;
}