<?php

function formulaires_ajouter_calendrier_charger()
{
	return array(
		'article' => ''
	) ;
}

function formulaires_ajouter_calendrier_verifier()
{
	$erreurs = array() ;

	foreach( array('article') as $obligatoire )
		if( !_request($obligatoire) ) $erreurs[$obligatoire] = _T('resa:form_msg_champ_obligatoire') ;

	if( count($erreurs) )
		$erreurs['message_erreur'] = _T('resa:form_msg_erreurs_saisie') ;

	return $erreurs ;
}

function formulaires_ajouter_calendrier_traiter()
{
	$idCal = sql_insertq(
		'spip_resa_calendrier',
		array(
			'id_article' => _request('article')
		)
	) ;
	$texte = sql_getfetsel('texte', 'spip_articles', 'id_article=' . sql_quote((int) _request('article'))) ;
	sql_updateq(
		'spip_articles',
		array('texte' => $texte . "\n\n" . '<calendrier1|id_calendrier=' . (int) $idCal . '>'),
		'id_article=' . sql_quote((int) _request('article'))
	) ;
   
	return array(
		'message_ok' => _T('resa:form_msg_calendrier_ajoute')
	) ;
}

?>
