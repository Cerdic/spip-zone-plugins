<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;


function formulaires_ajouter_filleul_charger($redirect=''){
	include_spip('inc/session');
	
	$contexte = array(
		'nom' => '',
		'email' => ''
	);
	
	// S'il n'y a pas d'auteur connecté, pas de formulaire
	if (!session_get('id_auteur') > 0)
		return false;
	else
		return $contexte;
}

function formulaires_ajouter_filleul_verifier($redirect=''){
	$erreurs = array();
	
	// Les champs sont obligatoires
	foreach (array('nom', 'email') as $champ){
		if (!_request($champ))
			$erreurs[$champ] = _T('info_obligatoire');
	}
	
	// Le champ email doit être... un email
	include_spip('inc/filtres');
	if (!$erreurs['email'] and !email_valide(_request('email')))
		$erreurs['email'] = _T('info_email_invalide');
	
	return $erreurs;
}

function formulaires_ajouter_filleul_traiter($redirect=''){
	// De toute façon on peut continuer d'ajouter ensuite
	$retours = array('editable' => true);
	
	// On ajoute l'email trouvé
	$ajouter_filleul = charger_fonction('ajouter_filleul', 'action/');
	$id_filleul = $ajouter_filleul('');
	
	// Si ça a marché, on met un petit message et on vide les valeurs
	if ($id_filleul > 0){
		$retours['message_ok'] = _T('parrainage:ajouter_filleul_confirmation');
		set_request('email',''); set_request('nom','');
	}
	
	if ($redirect)
		$retours['redirect'] = $redirect;
	
	return $retours;
}

?>
