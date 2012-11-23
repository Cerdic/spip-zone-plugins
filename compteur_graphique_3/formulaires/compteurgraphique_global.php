<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

function formulaires_compteurgraphique_global_charger_dist(){	
	$valeurs=array();
	return $valeurs;
}

function formulaires_compteurgraphique_global_traiter_dist(){
	$CG_nom_table = "spip_compteurgraphique";
	$res = array('editable'=>true);
	$res['message_ok'] = 'Aucune modification n\'a &eacute;t&eacute; enregistr&eacute;e';
	// Suppression du modèle de compteur pour tous les articles
	if (_request('supprimer_compteur_tous')) {
		$res['message_ok'] = 'Le modèle de compteur affichant le nombre total de visites du site du site a été supprimé';
        $resultat_suppr_tous = sql_delete($CG_nom_table,"statut = 7");
	}
	// Modification du modèle de compteur pour tous les articles
	if (_request('modifier_compteur_tous')) {
		$res['message_ok'] = 'La modification du modèle de compteur affichant le nombre total de visites du site a bien été enregistrée';
		$resultat_changement_habillage_tous = sql_updateq($CG_nom_table,array("habillage" => _request('tous_habillage'),"longueur" => _request('tous_chiffres')),"statut = 7");
	}
	// Création du modèle de compteur pour tous les articles
	if (_request('creer_compteur_tous')) {
		$res['message_ok'] = 'Le modèle de compteur affichant le nombre total de visites du site  a été créé';
		$resultat_nouveau_tous = sql_insertq($CG_nom_table,array("statut" =>7,"longueur" => _request('tous_chiffres'),"habillage" => _request('tous_habillage')));
	}
	return $res;
}

