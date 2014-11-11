<?php
if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_couleur_objet_charger_dist($id_objet,$objet,$couleur_objet){
	// autorisation : #ENV{editable} est evite car on veut toujours voir le formulaire meme apres validation
	$editable = true;
	if ($GLOBALS['visiteur_session']['statut']!=='0minirezo')
		$editable = false;
	else {
		include_spip("inc/config");
		if (lire_config("couleur_objet/bloquer")=="oui")
			$editable = false;
	}
	// chargement des valeurs du formulaire
	$valeurs = array(
		'id_objet' => $id_objet,
		'objet' => $objet,
		'couleur_objet' => $couleur_objet,
		'supprimer' => '',
		"editable" => $editable,
	);
	return $valeurs;
}

function formulaires_couleur_objet_traiter_dist($id_objet,$objet,$couleur_objet){
	$res = array();
	if (_request('supprimer')){
		// requête sql dans spip_couleur_objet_liens pour supprimer la ligne où #ID_OBJET = $id_objet et #OBJET = $objet
		sql_delete("spip_couleur_objet_liens", "id_objet=".intval($id_objet)." AND objet=".sql_quote($objet));
		$res = array("message_ok"=>_T('couleur_objet:message_couleur_supprimee'));
		set_request('couleur_objet');
	}
	else {
		$couleur_objet = _request('couleur_objet');
		$where =  "id_objet=".intval($id_objet)." AND objet=".sql_quote($objet);
		// si la ligne $id_objet / $objet existe dans la table spip_couleur_objet_liens alors on fait sql_updateq
		if (sql_countsel('spip_couleur_objet_liens', array(
			"id_objet=" . sql_quote($id_objet),
			"objet=" . sql_quote($objet)
		))) {
			sql_updateq('spip_couleur_objet_liens', array('couleur_objet' => $couleur_objet), $where);
		}else{
			// si la ligne $id_objet / $objet n'existe pas dans la table spip_couleur_objet_liens alors on fait sql_insertq
			sql_insertq('spip_couleur_objet_liens', array('id_objet' => $id_objet, 'objet' => $objet, 'couleur_objet' => $couleur_objet));
		}
		$res = array("message_ok"=>_T('couleur_objet:message_couleur_enregistree'));
	}
	return $res;

}