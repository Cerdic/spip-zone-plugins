<?php

//**************** Charger *************


function formulaires_attache_momo_charger_dist($id_mot) {

	$id_auteur = verifier_session();

	// Si la session est valide et l'utilisateur est autorisé
	include_spip('inc/autoriser');
	if (!autoriser("webmestre")) {
		return false;
	}
	$valeurs = array(
		'id_mot' => $id_mot,
		'id_mot_enfant' => '',
		'liste_mots_parents' => '',
		'mots' => '',
	);
	return $valeurs;
}

//**************** Vérifier *************

function formulaires_attache_momo_verifier_dist() {

	// Si la session est valide et l'utilisateur est autorisé
	include_spip('inc/autoriser');
	if (!autoriser("webmestre")) {
		return false;
	}

	$erreurs=array();

	return $erreurs;

}

//**************** Traiter *************

function formulaires_attache_momo_traiter_dist() {

	$id_auteur = verifier_session();

	// Si la session est valide et l'utilisateur est autorisé
	include_spip('inc/autoriser');
	if (!autoriser("webmestre")) {
		return false;
	}
	include_spip('base/abstract_sql');

	$tableau_mots_deja_parents = explode(",",_request('liste_mots_parents'));
	$tableau_mots_candidats_parents = _request('mots');
	$id_mot_enfant = _request('id_mot_enfant');

	//on ajoute les mots parents candidats
	$key = key($tableau_mots_candidats_parents);
	$val = current($tableau_mots_candidats_parents);
	$insert='';
	while (list($key,$val)=each($tableau_mots_candidats_parents)) {
			//debug: echo " tratitement ajout id_parent => intval($val), id_mot => intval($id_mot_enfant)<br/>";
		if (!in_array($val,$tableau_mots_deja_parents) and (intval($val)>0)) {
			//debug: echo " ajout id_parent => intval($val), id_mot => intval($id_mot_enfant)<br/>";
			$insert[] = array('id_parent' => intval($val), "id_mot" => intval($id_mot_enfant));
		}
	}
	sql_insertq_multi("spip_momo",$insert);

	//on retire les mots parents décochés
	$key = key($tableau_mots_deja_parents);
	$val = current($tableau_mots_deja_parents);
	while (list($key,$val)=each($tableau_mots_deja_parents)) {
			//debug: echo " traitement retrait id_mot='$id_mot_enfant' AND id_parent='$val'<br/>";
		if ( !in_array($val,$tableau_mots_candidats_parents) and (intval($val)>0)) {
			//debug: echo " retrait id_mot='$id_mot_enfant' AND id_parent='$val'<br/>";
			$result=sql_delete("spip_momo", "id_mot='$id_mot_enfant' AND id_parent='$val'");
		}
	}

	return $message;
}

?>