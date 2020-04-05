<?php

function formulaires_recherche_par_mots_charger_dist($filtre_groupes = null, $url_resultat = null){
	return 
		array(
			'id_groupe' => $filtre_groupes,
			'le_groupe'=>_request('le_groupe'),
			'mots'=>_request('mots')
		);
}

function formulaires_recherche_par_mots_verifier_dist($filtre_groupes = null, $url_resultat = null){
	$erreurs = array();
	if (_request('le_groupe') && _request('choixmot'))
		$erreurs['message_erreur'] = 'Choisissez un mot';
	$mots = _request('mots');
	if (is_array($mots) && (array_search('',$mots) !== false))
		$erreurs['message_erreur'] = 'Changement de groupe';
	return $erreurs;
}

function formulaires_recherche_par_mots_traiter_dist($filtre_groupes = null, $url_resultat = null){
    // le formulaire n'est jamais traite en ajax car il s'acheve par une redirection vers 
 	// la bonne page qui doit etre reaffichee dans son ensemble 
 	refuser_traiter_formulaire_ajax();

 	if (!$url_resultat) 
		$url_resultat = self();
	// Nettoyer l'URL des mots[] qu'elle contiendrait deja
	$url_resultat = parametre_url($url_resultat,'mots','');
	// Mettre dans l'URL 
	$url_resultat = parametre_url(
						$url_resultat,'mots',array_unique(_request('mots'))
						);
	return array('redirect' => $url_resultat);
}
?>