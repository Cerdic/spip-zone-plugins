<?php
/**
 * Plugin Campagnes publicitaires
 * (c) 2012 Les Développements Durables
 * Licence GNU/GPL
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/actions');
include_spip('inc/editer');

/*
 * Déclarer les champs du formulaire avec l'API de Saisies
 */
function formulaires_editer_annonceur_saisies_dist($id_annonceur='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$saisies = array(
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'nom',
				'label' => '<:annonceur:champ_nom_label:>',
				'obligatoire' => 'oui'
			)
		),
	);
	
	// Si on est admin on peut lier à un compte utilisateur
	if (autoriser('configurer')){
		$saisies[] = array(
			'saisie' => 'auteurs',
			'options' => array(
				'nom' => 'id_auteur',
				'label' => '<:annonceur:champ_id_auteur_label:>',
			)
		);
	}
	
	return $saisies;
}

/**
 * Identifier le formulaire en faisant abstraction des parametres qui ne representent pas l'objet edite
 */
function formulaires_editer_annonceur_identifier_dist($id_annonceur='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return serialize(array(intval($id_annonceur)));
}

/**
 * Declarer les champs postes et y integrer les valeurs par defaut
 */
function formulaires_editer_annonceur_charger_dist($id_annonceur='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$valeurs = formulaires_editer_objet_charger('annonceur',$id_annonceur,'',$lier_trad,$retour,$config_fonc,$row,$hidden);
	return $valeurs;
}

/**
 * Verifier les champs postes et signaler d'eventuelles erreurs
 */
function formulaires_editer_annonceur_verifier_dist($id_annonceur='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return formulaires_editer_objet_verifier('annonceur',$id_annonceur, array('nom'));
}

/**
 * Traiter les champs postes
 */
function formulaires_editer_annonceur_traiter_dist($id_annonceur='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return formulaires_editer_objet_traiter('annonceur',$id_annonceur,'',$lier_trad,$retour,$config_fonc,$row,$hidden);
}


?>
