<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('base/abstract_sql');

function formulaires_sympatic_abonnements_charger_dist($id_auteur){
	if (!intval($id_auteur))
		return false;
	//initialise les variables d'environnement pas défaut
	$valeurs = array();
	$valeurs['editable'] = true;
	$valeurs['id_auteur'] = $id_auteur;
	
	if (!autoriser('modifier','auteur',$id_auteur,null))
		$valeurs['editable'] = false;
	
	return $valeurs;
}

function formulaires_sympatic_abonnements_verifier_dist($id_auteur){

	$erreurs = array();

    return $erreurs;
}

function formulaires_sympatic_abonnements_traiter_dist($id_auteur){
    $message = array();
	$message['editable'] = true;
	$message['message_ok'] = _T('sympatic:message_abonnement_rien');
	$listes_auteur = array();
	
	if (!$listes = _request('listes'))
		$listes = array();
	
	$result = sql_select('id_liste','spip_sympatic_abonnes','id_auteur='.intval($id_auteur));
	while ($row = sql_fetch($result)) {
		$listes_auteur[$row['id_liste']] = $row['id_liste'];
	}
	
	foreach($listes as $cle => $id_liste){
		// si l'auteur est déjà abonné à une des listes envoyées on vire la liste de l'array
		if (in_array($id_liste, $listes_auteur)){
			unset($listes_auteur[$id_liste]);
		}
		else{
			// on abonne l'auteur aux listes demandées
			include_spip('inc/sympatic');
			if (!sympatic_traiter_abonnement($id_liste,$id_auteur,'abonner'))
				$message['message_erreur'] = _T('sympatic:message_abonnement_erreur');
			else
				$message['message_ok'] = _T('sympatic:message_abonnement_ok');
		}
	}
	// on desabonne l'auteur des listes qu'il reste dans l'array
	if (count($listes_auteur)>0){
		foreach($listes_auteur as $id_liste){
			include_spip('inc/sympatic');
			if (!sympatic_traiter_abonnement($id_liste,$id_auteur,'desabonner'))
				$message['message_erreur'] = _T('sympatic:message_abonnement_erreur');
			else
				if (!$message['message_erreur'])
					$message['message_ok'] = _T('sympatic:message_abonnement_ok');
		}
	}
	
    return $message;
}

?>