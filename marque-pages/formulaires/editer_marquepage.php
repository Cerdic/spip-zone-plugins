<?php

// Sécurité
if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_editer_marquepage_charger_dist($id_forum, $id_rubrique){
	$valeurs = array();
	
	// On voit si c'est une création
	if ($id_forum == 'new'){
		$valeurs['creation'] = true;
		
		// On peut pré-remplir avec le contexte
		$valeurs['mp_url'] = _request('mp_url');
		$valeurs['mp_titre'] = _request('mp_titre');
		$valeurs['mp_description'] = _request('mp_description');
		$valeurs['mp_visibilite'] = _request('mp_visibilite');
		$valeurs['mp_etiquettes'] = _request('mp_etiquettes');
	}
	// Sinon c'est une modif donc on remplit avec les valeurs de la base
	else{
		$requete = sql_fetsel(
			'url_site, titre, texte, statut',
			'spip_forum',
			'id_forum='.intval($id_forum)
		);
		$valeurs['mp_url'] = $requete['url_site'];
		$valeurs['mp_titre'] = $requete['titre'];
		$valeurs['mp_description'] = $requete['texte'];
		$valeurs['mp_visibilite'] = $requete['statut'];
		
		$valeurs['mp_etiquettes'] = '';
		$valeurs['objet'] = 'forum-'.$id_forum;
	}
	
	$valeurs['id_rubrique'] = $id_rubrique;
	$valeurs['id_forum'] = $id_forum;
	
	// Si on a pas le droit, faut proposer le login
	include_spip('inc/autoriser');
	if (!autoriser('creermarquepagedans', 'rubrique', $id_rubrique)){
		
		$valeurs['proposer_login'] = true;
		$valeurs['message_erreur'] = _T('marquepages:pas_le_droit');
		
	}
	else $valeurs['proposer_login'] = false;
	
	// preciser que le formulaire doit passer dans un pipeline
	$valeurs['_pipeline'] = array('editer_contenu_objet', array('type'=>'marquepage','id'=>$id_forum));
	// preciser que le formulaire doit etre securise auteur/action
	$valeurs['_action'] = array('editer_marquepage', $id_forum);
	
	return $valeurs;
}

function formulaires_editer_marquepage_verifier_dist($id_forum, $id_rubrique){
	$erreurs = array();
	
	include_spip('inc/marquepages_api');
	$titre = marquepages_tester_url(_request('mp_url'));
	
	if (!$titre)
		$erreurs['mp_url'] = _T('form_pet_url_invalide');
	elseif (!_request('mp_titre'))
		$erreurs['mp_titre'] = $titre;
	
	return $erreurs;
}

function formulaires_editer_marquepage_traiter_dist($id_forum, $id_rubrique){
	$retours = array();
	
	$editer_marquepage = charger_fonction("editer_marquepage",'action');
	list($id_forum, $message) = $editer_marquepage();
	
	if ($id_forum) $retours['message_ok'] = $message;
	else $retours['message_erreur'] = $message;
	
	if ($redirect = _request('redirect')){
		$retours['redirect'] = str_replace('&amp;', '&', $redirect);
	}
	
	return $retours;
}

?>
