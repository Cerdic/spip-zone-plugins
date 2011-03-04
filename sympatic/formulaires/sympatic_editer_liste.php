<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('base/abstract_sql');

// chargement des valeurs par defaut des champs du formulaire
function formulaires_sympatic_editer_liste_charger_dist($id_liste='new', $retour=''){
	//initialise les variables d'environnement pas défaut
	$valeurs = array();
	$valeurs['editable'] = true; 
	
	include_spip('inc/autoriser');
	if (!autoriser('modifier', 'sympatic'))
		$valeurs['editable'] = false; 
	
	// On verifie que la liste existe
	if ($id_liste){
		$valeurs = sql_fetsel('*','spip_sympatic_listes','id_liste='.intval($id_liste));
		if (!$valeurs['id_liste']){
			$valeurs['editable'] = false;
			$valeurs['message_erreur'] = _T('sympatic:liste_non_existante');
		}
	}
	
	return $valeurs;
}

function formulaires_sympatic_editer_liste_verifier_dist($id_liste='new', $retour=''){

	$erreurs = array();

	// verifier les champs obligatoires
	foreach (array(
		'titre', 'email_liste', 'email_robot'
	) as $champ) {
		if (_request($champ) == '') {
			$erreurs[$champ] = _T('saisies:option_obligatoire_label');
		}
	}

    return $erreurs;
}

function formulaires_sympatic_editer_liste_traiter_dist($id_liste='new', $retour=''){
	$message = array();
	$message['editable'] = true;

	// Récupération des données
	$datas = array();
	foreach (array(
		'titre', 'descriptif', 'email_liste', 'email_robot'
	) as $champ) {
		if (($a = _request($champ)) !== null) {
			$datas[$champ] = $a;
		}
	}

	if (intval($id_liste)){
		// maj d'une liste
		sql_updateq("spip_sympatic_listes",$datas,"id_liste = $id_liste");
		$message['message_ok'] = _T('sympatic:message_liste_maj');
	}
	else{
		// creation d'une liste
		$id_liste = sql_insertq("spip_sympatic_listes",$datas);
		$message['message_ok'] = _T('sympatic:message_liste_creee');
		$message['editable'] = false;
		$message['redirect'] = parametre_url($retour,'id_liste',$id_liste);
	}

	return $message;
}

?>