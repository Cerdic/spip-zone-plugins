<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

//
// Charger
// 
function formulaires_upload_charger_dist($objet, $id_objet, $fond_documents){
	// definition des valeurs de base du formulaire
	$valeurs = array(
		'objet'=>$objet,
		'id_objet'=>$id_objet,
		'fond_documents'=>$fond_documents,
		'editable'=>true
	);

	$id_type_objet = id_table_objet($objet);
	$valeurs[$id_type_objet] = $id_objet;

	if (!intval($GLOBALS['auteur_session']['id_auteur']))
		$valeurs['editable'] = false;

	include_spip('inc/autoriser');
	if (!autoriser('joindredocumentupload', $objet, $id_objet))
		$valeurs['editable'] = false;

	return $valeurs;
}

//
// Verifier
// 
function formulaires_upload_verifier_dist($objet, $id_objet, $fond_documents){
	$erreurs = array();

	return $erreurs;
}

//
// Traiter
// 
function formulaires_upload_traiter_dist($objet, $id_objet, $fond_documents){
	$res = array('editable'=>' ', 'message_ok'=>'');

	$invalider = false;
	$type = objet_type($objet);
	$res['message_ok'] = "";
	$compteur=0;

	// titrer des documents ?
	if (is_array(_request('ref'))) {
		foreach (_request('ref') as $ref) {
		$ref = intval($ref);
			if ($titre = _request("titrer_$ref")) {
			if (formulaireupload_verifier_doc_liaison($ref,$id_objet,$type))
				sql_updateq('spip_documents', array('titre' => $titre) ,'id_document='.$ref);         
			}
		}     
	}

	// supprimer des documents ?   
	if (is_array(_request('supprimer'))) {
	foreach (_request('supprimer') as $supprimer) {
		if ($supprimer = intval($supprimer)) {
			include_spip('inc/autoriser');
		if (formulaireupload_verifier_doc_liaison($supprimer,$id_objet,$type)) {
			sql_delete('spip_documents_liens', 'id_document='.$supprimer);
				$supprimer_document = charger_fonction('supprimer_document','action');
				$supprimer_document($supprimer);
				$invalider = true;
				$compteur++; 			
			spip_log("supprimer document ($type)".$supprimer, 'upload');
		} 			
		}
  	}
	$res['message_ok'] .= _T("formupload:msg_doc_deleted",array("compteur"=>$compteur))."<br />";
	}

	// Ajouter un document (cf plugins-dist/medias)
	include_spip('inc/joindre_document');
	$files = joindre_trouver_fichier_envoye();

	if (is_array($files)) {     
	$compteur = 0; 

	// gestion des quotas ?
	$quota = intval(lire_config("formulaireupload/files_quota"));
	$quota_left = 100;
	if ($quota>0) {
			if ($res_nb_objet = sql_select('id_document', 'spip_documents_liens', array("objet = '$type'",'id_objet='.intval($id_objet))))
						$nb_objet = sql_count($res_nb_objet);
			$quota_left = $quota - $nb_objet;  
			if ($quota_left<1 OR $quota_left<count($files)) 
				$res['message_ok'] .=  _T("formupload:msg_doc_added_max",array("max"=>$quota))."<br />";

		// on reduit les fichiers proposés par le quota restant       
			array_splice($files, $quota_left); 
    }


	// upload des fichiers
	if ($quota_left>0) {
		$ajouter_documents = charger_fonction('ajouter_documents', 'action');
		$nouveaux_doc = $ajouter_documents($id_document,$files,$objet,$id_objet,'document');

		$compteur = count($nouveaux_doc);
	}
	/* A verifier:
		- securite : verifier les extenxions (si forcing)

	*/
		$invalider = true;
		if ($compteur>0)
		$res['message_ok'] .= _T("formupload:msg_doc_added",array("compteur"=>$compteur));
	}

	if ($invalider) {
		include_spip('inc/invalideur');
		suivre_invalideur("0",true);
		spip_log('invalider', 'upload');
	}

	return $res;
}


//
//  fonction de securite
//  verifier la liaison entre objet et le document
//  pour eviter toucher d'autres documents que ceux traiter ds le doc
function formulaireupload_verifier_doc_liaison($id_document, $id_objet, $type) {
	if (sql_countsel('spip_documents_liens', "id_document=".intval($id_document)." AND id_objet=".intval($id_objet)." AND objet='$type'"))
		return true;  

	return false;  
}

?>