<?php


function formulaires_doc2img_convertir_document_charger($id_article,$redirect=''){
	$valeurs = array();
	if(!intval($id_article)){
		return;
	}
	$valeurs['id_article'] = intval($id_article);
	$types_autorises = explode(',',lire_config("doc2img/format_document",null,true));
	$in_ext = sql_in('doc.extension',$types_autorises);
	$docs = sql_countsel('spip_documents AS doc INNER JOIN spip_documents_liens AS liens ON ( doc.id_document = liens.id_document )',"liens.id_objet=$id_article AND liens.objet='article' AND ".$in_ext);
	if($docs == 0){
		$valeurs['message_erreur'] = _T('doc2img:formulaire_erreur_pas_doc');
	}
	return $valeurs;
}

function formulaires_doc2img_convertir_document_verifier($id_article,$redirect=''){
	$erreurs = array();
	include_spip('inc/autoriser');
	if(!intval(_request('id_document')) OR !autoriser('modifier','document',_request('id_document'))){
		$erreurs['id_document'] = _T('doc2img:erreur_autorisation');
	}
	return $erreurs;
}

function formulaires_doc2img_convertir_document_traiter($id_article,$redirect=''){
	$id_document = _request('id_document');
	$infos_doc = sql_fetsel('extension,mode,fichier,mode,distant','spip_documents','id_document='.intval($id_document));
	$types_autorises = explode(',',lire_config("doc2img/format_document",null,true));

	if(($infos_doc['mode'] != 'vignette')
		&& ($infos_doc['distant'] == 'non')
		&& in_array($infos_doc['extension'],$types_autorises)){
    	$convertir = charger_fonction('doc2img_convertir','inc');
    	$convertir($id_document);
	}
	if($redirect){
		$res['redirect'] = $redirect;
	}else{
		$res['redirect'] = $self;
		//$res['message_ok'] = '';
	}
	return $res;

}
?>