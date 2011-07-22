<?php

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
	if (!autoriser('joindredocument', $objet, $id_objet))
		$valeurs['editable'] = false;

	return $valeurs;
}

function formulaires_upload_verifier_dist($objet, $id_objet, $fond_documents){
	$erreurs = array();

	return $erreurs;
}

function formulaires_upload_traiter_dist($objet, $id_objet, $fond_documents){
	$res = array('editable'=>' ', 'message_ok'=>'');
	
	$invalider = false;
	$type = objet_type($objet);
	$res['message_ok'] = _T("formupload:msg_nothing_to_do");

	// supprimer des documents ?
	if (is_array(_request('supprimer')))
	foreach (_request('supprimer') as $supprimer) {
		if ($supprimer = intval($supprimer)) {
			include_spip('inc/autoriser');
			sql_delete('spip_documents_liens', 'id_document='.$supprimer);
			$supprimer_document = charger_fonction('supprimer_document','action');
			$supprimer_document($supprimer);
			$invalider = true;
			$res['message_ok'] = _T("formupload:msg_doc_deleted");
			spip_log("supprimer document ($type)".$supprimer, 'upload');
		}
	}

	// Ajouter un document
	if (($files = ($_FILES ? $_FILES : $HTTP_POST_FILES)) && (is_uploaded_file($files['fichier']['tmp_name']))) {
		spip_log($files, 'upload');
		spip_log("joindre sur $type $id_objet", 'upload');
		include_spip('action/joindre');
		$joindre1 = charger_fonction('joindre1', 'inc');
		if(!$joindre1($files, 'document', $type, $id_objet, 0, $hash, $redirect, $documents_actifs, $iframe_redirect))
			$res['message_erreur'] = _T('gis:erreur_copie_impossible');
		$invalider = true;
		$res['message_ok'] = _T("formupload:msg_doc_added");
	}

	if ($invalider) {
		include_spip('inc/invalideur');
		suivre_invalideur("0",true);
		spip_log('invalider', 'upload');
	}

	return $res;
}

?>
