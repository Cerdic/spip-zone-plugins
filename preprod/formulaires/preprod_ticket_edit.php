<?php

function formulaires_preprod_ticket_edit_charger_dist($adresse, $id_auteur=0, $id_ticket=0, $contexte='')
{
	include_spip('inc/preprod_fonctions');
	$id_ticket = intval($id_ticket);
	
	// on récupère le nom du rapporteur du ticket
	if (!empty($id_ticket))
		$rapporteur = sql_getfetsel('nom', 'spip_tickets LEFT JOIN spip_auteurs USING (id_auteur)', 'id_ticket=' . intval($id_ticket));
	else 
		$rapporteur = sql_getfetsel('nom', 'spip_auteurs', 'id_auteur=' . intval($id_auteur));

	// les données cachées à ajouter au formulaire
	$hidden = '<input type="hidden" name="id_auteur" value="'.$id_auteur.'" />';
	$hidden .= '<input type="hidden" name="exemple" value="'.$adresse.'" />';
	$hidden .= '<input type="hidden" name="id_ticket" value="'.$id_ticket.'" />';

	// si on crée un nouveau ticket, son statut par défaut est 'ouvert'
	if (0==$id_ticket)
		$hidden .= '<input type="hidden" name="statut" value="ouvert" />';

	return array(
		'_hidden'		=> $hidden,
		'id_ticket'		=> $id_ticket,
		'id_auteur'		=> $id_auteur,
		'id_projet'		=> _request('id_projet'),
		'adresse'		=> $adresse,
		'rapporteur' 	=> $rapporteur,
		'saisies_ticket' => saisies_formulaire_ticket_edit($id_ticket)
	);
}

function formulaires_preprod_ticket_edit_verifier_dist($adresse, $id_auteur, $id_ticket=0, $contexte='')
{
	$erreurs = array();
	
	// seulement si l'utilisateur a cliqué sur le bouton "enregistrer"
	if ('oui'==_request('enregistrer_ticket'))
	{
		if (!_request('titre') || !_request('texte'))
			$erreurs['message_erreur'] = _T('preprod:erreur_infos_manquantes');
			
		if (!_request('titre'))
			$erreurs['titre'] = _T('preprod:erreur_manque_titre');
		if (!_request('texte'))
			$erreurs['texte'] = _T('preprod:erreur_manque_texte');
	}
	return $erreurs;
}

function formulaires_preprod_ticket_edit_traiter_dist($adresse, $id_auteur, $id_ticket=0, $contexte='')
{
	$res = array();
	// seulement si l'utilisateur a cliqué sur le bouton "enregistrer"
	if ('oui'==_request('enregistrer_ticket'))
	{
		// on récupère les données du formulaire
		$id_ticket	= intval(_request('id_ticket'));
		$titre		= _request('titre');
		$texte		= _request('texte');
		$severite	= intval(_request('severite'));
		$type		= intval(_request('type'));
		$id_auteur	= intval(_request('id_auteur'));
		$id_assigne	= intval(_request('id_assigne'));
		$exemple	= _request('exemple');
		$id_livrable= intval(_request('id_livrable'));
		$statut		= _request('statut');
		
		// les informations à enregistrer
		$infos_ticket = array(
			'titre'			=> $titre,
			'texte'			=> $texte,
			'severite'		=> $severite,
			'type'			=> $type,
			'statut'		=> $statut,
			'id_auteur'		=> $id_auteur,
			'id_assigne'	=> $id_assigne,
			'exemple'		=> $exemple,
			'id_livrable'	=> $id_livrable
		);

		if (0==$id_ticket)
		{
			// si on n'a pas d'id_ticket, on insère un nouveau ticket dans la base
			$infos_ticket['date'] = date('Y-m-d H:i:s');
			$id_ticket = sql_insertq('spip_tickets', $infos_ticket);

			if ($id_ticket)
			{
				$res['message_ok'] = _T('preprod:succes_ticket_ajoute', array('id' => $id_ticket));
			}
		}
		else
		{
			// si on connaît l'id_ticket, on met à jour ses données
			$date_modif = date('Y-m-d H:i:s');
			$infos_ticket['date_modif'] = $date_modif;
			sql_updateq('spip_tickets', $infos_ticket, 'id_ticket='. $id_ticket);
			$ok = sql_countsel('spip_tickets', 'id_ticket='. $id_ticket.' AND date_modif='.sql_quote($date_modif));
			if (1==$ok)
			{
				$res['message_ok'] = _T('preprod:succes_ticket_modifie', array('id' => $id_ticket));
			}
		}
	}
	return $res;
}
?>