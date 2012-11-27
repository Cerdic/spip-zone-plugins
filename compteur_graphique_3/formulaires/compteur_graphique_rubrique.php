<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

function formulaires_compteur_graphique_rubrique_charger_dist(){	
	$valeurs=array();
	return $valeurs;
}

function formulaires_compteur_graphique_rubrique_traiter_dist(){
	$CG_nom_table = "spip_compteurgraphique";
	$id_rubrique=_request('id_rubrique');
	$res = array('editable'=>true);
	$res['message_ok'] = 'Aucune modification n\'a &eacute;t&eacute; enregistr&eacute;e';
	if (_request('creer_compteur_rubrique_valide')) {
        $resultat_cree_compteur = sql_insertq($CG_nom_table,
		array("id_rubrique" => $id_rubrique,
		"statut" => 4,
		"longueur" => _request('nouveau_chiffres'),
		"habillage" => _request('nouveau_habillage_creation')));
	}
	elseif (_request('reactive_compteur_rubrique')) {
		sql_delete($CG_nom_table,"id_rubrique=$id_rubrique");
	}
	elseif (_request('interdire_compteur_rubrique')) {
		$resultat_interdiction_compteur = sql_insertq($CG_nom_table,
		array("id_rubrique" => $id_rubrique,
		"statut" => 5));
	}
	elseif(_request('suppr_compteur_rubrique')) {
		sql_delete($CG_nom_table,"id_rubrique=$id_rubrique");
	}
	elseif(_request('modif_compteur_rubrique_valide')) {
		$resultat_cree_compteur = sql_updateq($CG_nom_table,
		array("longueur" => _request('nouveau_chiffres'),
		"habillage" =>_request('habillage_modif')),
		"id_rubrique = $id_rubrique");
	}
	return $res;
}