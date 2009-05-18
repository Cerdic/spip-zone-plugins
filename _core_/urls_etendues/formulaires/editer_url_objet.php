<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2009                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

/**
 * Chargement des donnees du formulaire
 *
 * @param string $type
 * @param int $id
 * @return array
 */
function formulaires_editer_url_objet_charger($type,$id){
	$valeurs = array('url'=>'','url_lock'=>'');

	return $valeurs;
}

function formulaires_editer_url_objet_verifier($type,$id){
	$erreurs = array();
	include_spip('action/editer_url');
	$url = _request('url');
	$url_clean = url_nettoyer($url, 255);
	if ($url!=$url_clean){
		set_request('url',$url_clean);
		$erreurs['url'] = _T('urls:verifier_url_nettoyee');;
	}

	return $erreurs;
}

/**
 * Traitement
 *
 * @param string $type
 * @param int $id
 * @return array
 */
function formulaires_editer_url_objet_traiter($type,$id){
	$valeurs = array('editable'=>true);

	include_spip('action/editer_url');
	$set = array('url' => _request('url'), 'type' => $type, 'id_objet' => $id);
	if (url_insert($set,false,","))
		$valeurs['message_ok'] = _T("urls:url_ajoutee");
	else
		$valeurs['message_erreur'] = _T("urls:url_ajout_impossible");

	if (_request('url_lock'))
		url_verrouiller($set['type'],$set['id_objet'],$set['url']);

	return $valeurs;
}