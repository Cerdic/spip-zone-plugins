<?php

function formulaires_preprod_ajouter_ticket_charger_dist($adresse, $id_auteur)
{
	$hidden = '<input type="hidden" name="id_auteur" value="'.$id_auteur.'" />';
	$hidden .= '<input type="hidden" name="exemple" value="'.$adresse.'" />';
	return array(
		'_hidden'	=> $hidden,
		'adresse'	=> $adresse
	);
}

function formulaires_preprod_ajouter_ticket_verifier_dist($adresse, $id_auteur)
{
	$erreurs = array();
	if (!_request('titre') || !_request('texte'))
		$erreurs['message_erreur'] = _T('preprod:erreur_infos_manquantes');
		
	if (!_request('titre'))
		$erreurs['titre'] = _T('preprod:erreur_manque_titre');
	if (!_request('texte'))
		$erreurs['texte'] = _T('preprod:erreur_manque_texte');
	return $erreurs;
}

function formulaires_preprod_ajouter_ticket_traiter_dist($adresse, $id_auteur)
{
	$res = array();

	$titre		= _request('titre');
	$texte		= _request('texte');
	$severite	= intval(_request('severite'));
	$type		= intval(_request('type'));
	$id_auteur	= intval(_request('id_auteur'));
	$id_assigne	= intval(_request('id_assigne'));
	$exemple	= _request('exemple');
	$composant	= _request('composant');

	if (empty($composant))
		$composant = _request('nouveau_composant');

	$insertion = array(
		'titre'		=> $titre,
		'texte'		=> $texte,
		'date'		=> date('Y-m-d H:i:s'),
		'severite'	=> $severite,
		'type'		=> $type,
		'statut'	=> 'ouvert',
		'id_auteur'	=> $id_auteur,
		'id_assigne'=> $id_assigne,
		'exemple'	=> $exemple,
		'projet'	=> $projet,
		'composant'	=> $composant
	);

	$id_ticket = sql_insertq('spip_tickets', $insertion);

	if ($id_ticket)
	{
		$res['message_ok'] = _T('preprod:succes_ticket_ajoute', array('id' => $id_ticket));
	}

	return $res;
}
?>