<?php

if (!defined('_ECRIRE_INC_VERSION'))
	return;

include_spip('inc/actions');
include_spip('inc/editer');


function formulaires_editer_autartrole_charger_dist($id_auteur='', $id_article='', $retour='', $type_vue='')
{
	$id_auteur = intval($id_auteur);
	$id_article = intval($id_article);
	$liaisons = sql_allfetsel('*', 'spip_auteurs_articles', "id_auteur=$id_auteur AND id_article=$id_article");
	if (count($liaisons))
	{ // enregistrement existant/editable
		$contexte = $liaisons[0];
		$contexte['editable'] = TRUE;
		$contexte['nombre_auteurs'] = sql_countsel('spip_auteurs_articles', "id_article=$id_article"); // ceci est pour pouvoir proposer une liste deroulante des positions
	}
	else
	{ // enregistrement inexistant
		$contexte['editable'] = FALSE;
		if ($id_auteur && $id_article)
		{ // on tente d'abuser le formulaire ?
			$contexte['message_erreur'] = _T('autartrole:message_erreur_lien');
		}
	}
	$contexte['type_vue'] = $type_vue;

	return $contexte;
}


function formulaires_editer_autartrole_verifier_dist($id_auteur, $id_article, $retour='')
{
	$id_auteur = intval($id_auteur);
	$id_article = intval($id_article);
	$rang = _request('rang');
	$erreurs = array();

	if (!$id_article OR !sql_countsel('spip_articles',"id_article=$id_article") )
	{
		$erreurs['id_article'] = _T('autartrole:message_erreur_article');
	}
	if (!$id_auteur OR !sql_countsel('spip_auteurs',"id_auteur=$id_auteur") )
	{
		$erreurs['id_auteur'] = _T('message_erreur_auteur');
	}
	if (!sql_countsel('spip_auteurs_articles',"id_auteur=$id_auteur") )
	{
		$erreurs['id_auteur'] = $erreurs['id_article'] = _T('autartrole:message_erreur_lien');
	}
	if (!is_numeric($rang) OR $rang<0 )
	{
		$erreurs['rang'] = _T('autartrole:message_erreur_lien');
	}

	if (count($erreurs) )
	{
		$erreurs['message_erreur'] = _L('Votre saisie contient des erreurs !');
	}
	return $erreurs;
}


function formulaires_editer_autartrole_traiter_dist($id_auteur, $id_article, $retour='')
{
	$id_auteur = intval($id_auteur);
	$id_article = intval($id_article);
	$messages = array();
	sql_updateq(
		'spip_auteurs_articles',
		array(
			'role' => _request('role'),
			'rang' => intval(_request('rang')),
		),
		"id_auteur=$id_auteur AND id_article=$id_article"
	);
	if (!$retour)
	{
		$messages['message_ok'] = _T('autartrole:message_succes_changement');
		$messages['editable'] = TRUE;
		return $messages;
	}

}


?>