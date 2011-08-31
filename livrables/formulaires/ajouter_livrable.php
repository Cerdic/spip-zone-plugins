<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_ajouter_livrable_charger_dist(){
	$valeurs = array(
		'id_projet' => _request('id_projet'),
		'titre'		=> _request('titre'),
		'url'		=> _request('url'),
		'descriptif'=> _request('descriptif')
	);
	return $valeurs;
}

function formulaires_ajouter_livrable_verifier_dist(){
	$erreurs = array();
	
	// on vérifie les données obligatoires
	if (!_request('id_projet'))
		$erreurs['id_projet'] = _T('livrables:erreur_manque_projet');
	if (!_request('titre'))
		$erreurs['titre'] = _T('livrables:erreur_manque_titre');
	if (!_request('url'))
		$erreurs['url'] = _T('livrables:erreur_manque_url');
	if (!_request('descriptif'))
		$erreurs['descriptif'] = _T('livrables:erreur_manque_descriptif');	

	return $erreurs;
}

function formulaires_ajouter_livrable_traiter_dist(){
	$res = array();

	// on récupère les données du formulaire
	$id_projet	= intval(_request('id_projet'));
	$url		= _request('url');
	$titre		= _request('titre');
	$descriptif	= _request('descriptif');
	
	// on saisit une nouvelle date
	$maj		= date('Y-m-d H:i:s');

	// on insère les données dans la table spip_livrables
	$id_livrable = sql_insertq('spip_livrables', array(
		'id_projet'	=> $id_projet,
		'url'		=> $url,
		'titre'		=> $titre,
		'descriptif'=> $descriptif,
		'maj'		=> $maj
	));

	if ($id_livrable)
	{
		// on recharge la page pour faire apparaître le  nouveau livrable
		$res['message_ok'] = _T('livrables:succes_enregistrement', array('id' => $id_livrable));
		$res['redirect'] = generer_url_ecrire('livrables', 'id_projet='.$id_projet);
	}
	else
	{
		$res['message_erreur'] =  _T('livrables:erreur_echec_enregistrement');
	}

	return $res;
}

?>
