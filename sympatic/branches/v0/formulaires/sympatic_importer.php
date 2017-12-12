<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('base/abstract_sql');

function formulaires_sympatic_importer_charger_dist($retour=''){
	
	$valeurs = array();
	$valeurs['editable'] = true; 
	
	include_spip('inc/autoriser');
	if (!autoriser('modifier', 'sympatic'))
		$valeurs['editable'] = false; 
	
	return $valeurs;
}

function formulaires_sympatic_importer_verifier_dist($retour=''){

	$erreurs = array();
	
	// verifier les champs obligatoires
	foreach (array(
		'liste', 'emails'
	) as $champ) {
		if (_request($champ) == '') {
			$erreurs[$champ] = _T('saisies:option_obligatoire_label');
		}
	}
	
    return $erreurs;
}

function formulaires_sympatic_importer_traiter_dist($retour=''){
	$message = array();
	$message['editable'] = true;
	
	$emails = explode("\n",_request('emails'));
	$id_liste = intval(_request('liste'));
	
	include_spip('inc/sympatic');
	
	$nb_imports = 0;
	// pour chaque email
	// on check la validite de l'adresse
	// on cherche l'id_auteur correspondant
	// on check que l'auteur n'est pas deja abonne a la liste
	foreach($emails as $email_auteur){
		if (($email_auteur = email_valide($email_auteur))
			AND ($id_auteur = sql_getfetsel('id_auteur','spip_auteurs','email='.sql_quote($email_auteur)))
			AND (!sql_getfetsel("id_auteur","spip_sympatic_abonnes","id_auteur=$id_auteur AND id_liste=$id_liste"))
		){
			//if (sympatic_traiter_abonnement($id_liste,$id_auteur,'abonner'))
				//$message['message_ok'] = _T('sympatic:message_abonnement_ok');
			sql_insertq('spip_sympatic_abonnes', array('id_liste' => intval($id_liste), 'id_auteur' => intval($id_auteur)));
			++$nb_imports;
		}
	}
	spip_log("import $nb_imports auteurs dans la liste $id_liste","sympatic");
	$message['message_ok'] = _T('sympatic:message_import_ok', array('nb'=>$nb_imports));
	return $message;
}

?>