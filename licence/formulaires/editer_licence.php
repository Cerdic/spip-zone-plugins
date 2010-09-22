<?php

include_spip('inc/autoriser');
include_spip('inc/licence');

function formulaires_editer_licence_charger_dist($id_article='new', $retour=''){
	
	$id_licence = sql_getfetsel('id_licence','spip_articles','id_article='.intval($id_article));
	
	$valeurs['_licences'] = $GLOBALS['licence_licences'];
	$valeurs['id_licence'] = $id_licence;
	$valeurs['id_article'] = $id_article;
	$valeurs['editable'] = true;
	
	if (!autoriser('modifier', 'article', $id_article))
		$valeurs['editable'] = false;

	return $valeurs;
}

function formulaires_editer_licence_verifier_dist($id_article='new', $retour=''){
	$erreurs = array();
	return $erreurs;
}

function formulaires_editer_licence_traiter_dist($id_article='new', $retour=''){
	
	$message = array('editable'=>true, 'message_ok'=>'');

	sql_updateq('spip_articles',array('id_licence'=>_request('id_licence')),'id_article='.intval($id_article));
	
	include_spip('inc/invalideur');
	suivre_invalideur("id='id_article/$id_article'");
	
	if ($retour) {
		include_spip('inc/headers');
		$message .= redirige_formulaire($retour);
	}
	
	return $message;
	
}

?>
