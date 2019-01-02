<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/actions');
include_spip('inc/editer');

function formulaires_editer_banniere_charger_dist($id_banniere='new', $retour='', $choix_diffusion) {
	$valeurs = formulaires_editer_objet_charger('banniere', $id_banniere, '', '', $retour, '');
	// si c'est une nouvelle banniere, on recupere son choix_diffusion
	if ($choix_diffusion != '') {
		foreach($choix_diffusion as $clef => $valeur) {
			$valeurs[$clef] = $valeur;
		}
	}
	return $valeurs;
}

function formulaires_editer_banniere_verifier_dist($id_banniere='new', $retour='', $choix_diffusion) {
	$erreurs = array();
	$verifier = charger_fonction('verifier', 'inc', true);
	// verifier que les champs obligatoires sont bien la :
	foreach(array('nom') as $obligatoire)
		if (!_request($obligatoire)) $erreurs[$obligatoire] = _T('info_obligatoire');
	if ($email = _request('email') AND $erreur_email = $verifier($email, 'email', array('mode' => 'rfc5322')))
		$erreurs['email'] = $erreur_email;
	if ($site = _request('site') AND $erreur_site = $verifier($site, 'url', array('type_protocole' => 'web')))
		$erreurs['site'] = $erreur_site;
	return $erreurs;
}

function formulaires_editer_banniere_traiter_dist($id_banniere='new', $retour='', $choix_diffusion) {	
	return formulaires_editer_objet_traiter('banniere', $id_banniere, '', '', $retour, '');
}
