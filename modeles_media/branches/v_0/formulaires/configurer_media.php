<?php

if (!defined("_ECRIRE_INC_VERSION")) return;

function formulaires_configurer_media_charger_dist(){

	$valeurs = array();

	$valeurs['media_taille_icone_largeur'] = $GLOBALS['meta']['media_taille_icone_largeur'];
	$valeurs['media_taille_icone_hauteur'] = $GLOBALS['meta']['media_taille_icone_hauteur'];
	$valeurs['media_taille_petit_largeur'] = $GLOBALS['meta']['media_taille_petit_largeur'];
	$valeurs['media_taille_petit_hauteur'] = $GLOBALS['meta']['media_taille_petit_hauteur'];
	$valeurs['media_taille_moyen_largeur'] = $GLOBALS['meta']['media_taille_moyen_largeur'];
	$valeurs['media_taille_moyen_hauteur'] = $GLOBALS['meta']['media_taille_moyen_hauteur'];
	$valeurs['media_taille_grand_largeur'] = $GLOBALS['meta']['media_taille_grand_largeur'];
	$valeurs['media_taille_grand_hauteur'] = $GLOBALS['meta']['media_taille_grand_hauteur'];
	$valeurs['media_largeur_min_legende'] = $GLOBALS['meta']['media_largeur_min_legende'];
	$valeurs['media_largeur_max_legende'] = $GLOBALS['meta']['media_largeur_max_legende'];
	
	return $valeurs;
}

function formulaires_configurer_media_verifier_dist(){
	$erreurs = array();
	
	// On vérifie qu'il s'agit d'un nombre entier
	foreach(array(
		'media_taille_icone_largeur','media_taille_icone_hauteur',
		'media_taille_petit_largeur', 'media_taille_petit_hauteur',
		'media_taille_moyen_largeur','media_taille_moyen_hauteur',
		'media_taille_grand_largeur', 'media_taille_grand_hauteur',
		'media_largeur_min_legende', 'media_largeur_max_legende') as $champ)
	{
		if (!is_numeric(_request($champ)) OR intval(_request($champ))<=0)
		{
			$erreurs[$champ] = _T('media:erreur_taille');
		}
	}

	if (count($erreurs)) {
		$erreurs['message_erreur'] = _T('media:erreur_saisies');
	}

	return $erreurs;
}

function formulaires_configurer_media_traiter_dist(){
	include_spip('inc/config');
	appliquer_modifs_config();
	
	return array('message_ok'=>_T('config_info_enregistree'));
}

?>
