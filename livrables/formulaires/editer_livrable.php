<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_editer_livrable_charger_dist($id_livrable='', $retour=''){

	$row = sql_fetsel('id_projet, url, objet, composition, titre, descriptif', 'spip_livrables', 'id_livrable='.intval($id_livrable));

	$id_projet = _request('id_projet') ? _request('id_projet') : $row['id_projet'];
	$titre = _request('titre') ? _request('titre') : $row['titre'];
	$url = _request('url') ? _request('url') : $row['url'];
	$type = _request('type') ? _request('type') : $row['type'];
	$composition = _request('composition') ? _request('composition') : $row['composition'];
	$descriptif = _request('descriptif') ? _request('descriptif') : $row['descriptif'];
	
	$valeurs = array(
		'id_projet' => $id_projet,
		'titre' => $titre,
		'url' => $url,
		'objet' => $objet,
		'composition' => $composition,
		'descriptif' => $descriptif
	);

	return $valeurs;
}

function formulaires_editer_livrable_verifier_dist($id_livrable='', $retour=''){
	$erreurs = array();
	
	// on vérifie les données obligatoires
	if (!_request('id_projet'))
		$erreurs['id_projet'] = _T('livrables:erreur_manque_projet');
	if (!_request('titre'))
		$erreurs['titre'] = _T('livrables:erreur_manque_titre');
	if (!_request('descriptif'))
		$erreurs['descriptif'] = _T('livrables:erreur_manque_descriptif');	

	return $erreurs;
}

function formulaires_editer_livrable_traiter_dist($id_livrable='', $retour=''){
	
	$res = array();
	$datas = array(
		'id_projet' => _request('id_projet'),
		'titre' => _request('titre'),
		'url' => _request('url'),
		'objet' => _request('objet'),
		'composition' => _request('composition'),
		'descriptif' => _request('descriptif')
	);
	
	sql_updateq('spip_livrables', $datas, 'id_livrable=' . intval($id_livrable));
	$res['message_ok'] = _T('livrables:succes_modification', array('id' => $id_livrable));

	if ($retour)
		$res['redirect'] = $retour;
	return $res;
}

?>
