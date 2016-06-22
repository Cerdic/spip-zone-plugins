<?php
/**
 * Plugin Abonnements
 * (c) 2012 Les Développements Durables
 * Licence GNU/GPL v3
 */

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/actions');
include_spip('inc/editer');

/*
 * Déclaration des champs du formulaire
 */
function formulaires_editer_abonnements_offre_saisies_dist($id_abonnements_offre='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return array(
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'titre',
				'label' => _T('abonnementsoffre:champ_titre_label'),
				'obligatoire' => 'oui',
			),
		),
		array(
			'saisie' => 'textarea',
			'options' => array(
				'nom' => 'descriptif',
				'label' => _T('abonnementsoffre:champ_descriptif_label'),
				'rows' => 10,
			),
		),
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'duree',
				'label' => _T('abonnementsoffre:champ_duree_label'),
				'obligatoire' => 'oui',
				'defaut' => 0,
			),
			'verifier' => array(
				'type' => 'entier',
				'options' => array(
					'min' => 0,
				),
			),
		),
		array(
			'saisie' => 'selection',
			'options' => array(
				'nom' => 'periode',
				'label' => _T('abonnementsoffre:champ_periode_label'),
				'obligatoire' => 'oui',
				'cacher_option_intro' => 'oui',
				'datas' => array(
					'mois' => _T('abonnementsoffre:champ_periode_choix_mois'),
					'jours' => _T('abonnementsoffre:champ_periode_choix_jours'),
					'heures' => _T('abonnementsoffre:champ_periode_choix_heures'),
				),
				'defaut' => 'mois',
			),
		),
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'prix',
				'label' => _T('abonnementsoffre:champ_prix_label'),
				'obligatoire' => 'oui',
				'defaut' => 0,
			),
			'verifier' => array(
				'type' => 'decimal',
				'options' => array(
					'min' => 0,
				),
			),
		),
	);
}

/**
 * Identifier le formulaire en faisant abstraction des parametres qui ne representent pas l'objet edite
 */
function formulaires_editer_abonnements_offre_identifier_dist($id_abonnements_offre='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return serialize(array(intval($id_abonnements_offre)));
}

/**
 * Declarer les champs postes et y integrer les valeurs par defaut
 */
function formulaires_editer_abonnements_offre_charger_dist($id_abonnements_offre='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$valeurs = formulaires_editer_objet_charger('abonnements_offre',$id_abonnements_offre,'',$lier_trad,$retour,$config_fonc,$row,$hidden);
	unset($valeurs['id_abonnements_offre']);
	return $valeurs;
}

/**
 * Verifier les champs postes et signaler d'eventuelles erreurs
 */
function formulaires_editer_abonnements_offre_verifier_dist($id_abonnements_offre='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return formulaires_editer_objet_verifier('abonnements_offre',$id_abonnements_offre, array('titre'));
}

/**
 * Traiter les champs postes
 */
function formulaires_editer_abonnements_offre_traiter_dist($id_abonnements_offre='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return formulaires_editer_objet_traiter('abonnements_offre',$id_abonnements_offre,'',$lier_trad,$retour,$config_fonc,$row,$hidden);
}


?>
