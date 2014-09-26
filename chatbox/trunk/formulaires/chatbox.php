<?php
# protection par nospam
$GLOBALS['formulaires_no_spam'][] = 'chatbox';



function formulaires_chatbox_charger_dist(){
	$valeurs = array('message'=>'');

	return $valeurs;
}


function formulaires_chatbox_verifier_dist(){
	$erreurs = array();

	if (!$GLOBALS["visiteur_session"]['statut']) {
		return array(
			'action' => '', #ne sert pas dans ce cas, on la vide pour mutualiser le cache
			'editable'=>false,
			'login_forum_abo'=>' ',
			'inscription' => generer_url_public('identifiants', 'lang='.$GLOBALS['spip_lang']),
			'oubli' => generer_url_public('spip_pass','lang='.$GLOBALS['spip_lang'],true),
			);
	}

	foreach(array('message') as $obligatoire)
		if (!_request($obligatoire)) $erreurs[$obligatoire] = _T('chatbox:erreur_lors_ajout_message_chatbox');

	$message = _request('message');
	if (strlen($message)>260) $erreurs['message'] = _T('chatbox:erreur_lors_ajout_message_chatbox');

	if (count($erreurs))
		$erreurs['message_erreur'] = _T('chatbox:erreur_lors_ajout_message_chatbox');
	return $erreurs;
}


function formulaires_chatbox_traiter_dist(){
	include_spip('inc/filtres');
	include_spip('inc/texte_mini');
	include_spip('base/abstract_sql');

	$message = _request('message');
	$id_message = sql_insertq(
	    'spip_chatbox_messages',
	    array(
		'message' => safehtml($message),
		'id_auteur' => $GLOBALS['visiteur_session']['id_auteur'],
		'lang' => $GLOBALS['spip_lang'],
		'date' => 'NOW()',
		'statut' => 'publie',
		'composition' => ''
	    )
	);

	if ($id_message) $res=array('message_ok'=>_T('chatbox:message_chatbox_ajoute'));
	else $res=array('message_erreur'=>_T('chatbox:erreur_lors_ajout_message_chatbox'));

	return $res;
}



?>