<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_configurer_media_charger_dist(){
	$media_liste_metas = array(
		'media_taille_icone_largeur','media_taille_icone_hauteur',
		'media_taille_petit_largeur', 'media_taille_petit_hauteur',
		'media_taille_moyen_largeur','media_taille_moyen_hauteur',
		'media_taille_grand_largeur', 'media_taille_grand_hauteur',
		'media_taille_defaut_largeur', 'media_taille_defaut_hauteur',
		'media_largeur_min_legende', 'media_largeur_max_legende');
	$valeurs = array();
	foreach ($media_liste_metas as $m)
		$valeurs[$m] = $GLOBALS['meta'][$m];
	return $valeurs;
}

function formulaires_configurer_media_verifier_dist(){
	$erreurs = array();
	
	// On vérifie qu'il s'agit de nombres entiers positifs
	$champs = array(
		'media_taille_icone_largeur','media_taille_icone_hauteur',
		'media_taille_petit_largeur', 'media_taille_petit_hauteur',
		'media_taille_moyen_largeur','media_taille_moyen_hauteur',
		'media_taille_grand_largeur', 'media_taille_grand_hauteur',
		'media_largeur_min_legende', 'media_largeur_max_legende');
	
	if (_request('media_taille_defaut_largeur'))
		$champs[] = 'media_taille_defaut_largeur';
	
	if (_request('media_taille_defaut_hauteur'))
		$champs[] = 'media_taille_defaut_hauteur';
	
	foreach($champs as $champ)
	{
		if (!is_numeric(_request($champ)) OR intval(_request($champ))<=0)
			$erreurs[$champ] = _T('media:erreur_taille');
	}

	if (count($erreurs)) {
		$erreurs['message_erreur'] = _T('media:erreur_saisies');
	}

	return $erreurs;
}

function formulaires_configurer_media_traiter_dist(){
	include_spip('inc/config');
	
	$media_liste_metas = array(
		'media_taille_icone_largeur','media_taille_icone_hauteur',
		'media_taille_petit_largeur', 'media_taille_petit_hauteur',
		'media_taille_moyen_largeur','media_taille_moyen_hauteur',
		'media_taille_grand_largeur', 'media_taille_grand_hauteur',
		'media_taille_defaut_largeur', 'media_taille_defaut_hauteur',
		'media_largeur_min_legende', 'media_largeur_max_legende');
	foreach ($media_liste_metas as $m)
		ecrire_config($m, _request($m));
	
	return array('message_ok'=>_T('config_info_enregistree'));
}

?>
