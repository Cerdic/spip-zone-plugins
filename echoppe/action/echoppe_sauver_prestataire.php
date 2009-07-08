<?php

function action_echoppe_sauver_prestataire(){
	include_spip('inc/utils');
	$contexte = Array();
	$contexte['new'] = _request('new');
	$contexte['id_prestataire'] = _request('id_prestataire');
	$contexte['titre'] = _request('titre');
	$contexte['texte'] = _request('texte');
	$contexte['modele'] = _request('modele');
	$contexte['statut'] = _request('statut');
	$contexte['version'] = _request('version');
	$contexte['type'] = _request('type');
	$infos_prestataire = array(
			'titre' => $contexte['titre'],
			'texte' => $contexte['texte'],
			'modele' => $contexte['modele'],
			'statut' => $contexte['statut'],
			//'version' => $contexte['version'],
			'type' => 'paiement'
		);
	if ($contexte['new'] == "oui"){
		//$sql_sauver_prestataire = "INSERT INTO spip_echoppe_prestataires_paiement VALUES ('', '".addslashes($contexte['titre'])."', '"._request('version')."', '".addslashes($contexte['description'])."', '".addslashes($contexte['squelette'])."', '".$contexte['statut']."');";
		//$res_sauver_prestataire = spip_query($sql_sauver_prestataire);
		
		$contexte['id_prestataire'] = sql_insertq('spip_echoppe_prestataires', $infos_prestataire);
		//$contexte['id_prestataire'] == spip_insert_id();
		
	}else{
		/*$sql_sauver_prestataire = "UPDATE spip_echoppe_prestataires SET titre='".addslashes($contexte['titre'])."', descriptif='".addslashes($contexte['description'])."', version = '"._request('version')."', mdl='".addslashes($contexte['squelette'])."', statut = '".$contexte['statut']."' WHERE id_prestataire ='".$contexte['id_prestataire']."';";
		
		$res_sauver_prestataire = spip_query($sql_sauver_prestataire);*/
		$res_sauver_prestataire = sql_updateq("spip_echoppe_prestataires", $infos_prestataire, "id_prestataire = '".$contexte['id_prestataire']."'");
	}

	//spip_log($sql_sauver_prestataire,'echoppe');
	
	$redirect = generer_url_ecrire('echoppe_gerer_prestataire_paiement');
	redirige_par_entete($redirect);
}

?>
