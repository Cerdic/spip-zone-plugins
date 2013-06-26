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
function formulaires_editer_abonnement_saisies_dist($id_abonnement='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$id_abonnement = intval($id_abonnement);
	
	// Si c'est une demande de création et qu'on trouve une offre, on ne doit donner que l'utilisateur et l'abonnement est défini caché
	if ($id_abonnement == 0 and $id_abonnements_offre = _request('id_abonnements_offre')) {
		$saisies = array(
			array(
				'saisie' => 'hidden',
				'options' => array(
					'nom' => 'id_abonnements_offre',
					'defaut' => $id_abonnements_offre,
				),
			),
			array(
				'saisie' => 'auteurs',
				'options' => array(
					'nom' => 'id_auteur',
					'label' => _T('abonnement:champ_id_auteur_label'),
					'obligatoire' => 'oui',
					'cacher_option_intro' => 'oui',
				),
			),
		);
	}
	// Sinon si c'est une demande de création et qu'on trouve un auteur, on ne doit donner que les offres et l'utilisateur est défini caché
	elseif ($id_abonnement == 0 and $id_auteur = _request('id_auteur')) {
		$saisies = array(
			array(
				'saisie' => 'hidden',
				'options' => array(
					'nom' => 'id_auteur',
					'defaut' => $id_auteur,
				),
			),
			array(
				'saisie' => 'abonnements_offres',
				'options' => array(
					'nom' => 'id_abonnements_offre',
					'label' => _T('abonnement:champ_id_abonnements_offre_label'),
					'obligatoire' => 'oui',
					'cacher_option_intro' => 'oui',
				),
			),
		);
	}
	// Sinon si c'est une demande de modification, on ne peut modifier que l'échéance
	// Et uniquement en ajoutant avec durée et période
	elseif ($id_abonnement > 0) {
		$saisies = array(
			array(
				'saisie' => 'date',
				'options' => array(
					'nom' => 'date_debut',
					'label' => _T('abonnement:champ_date_debut_label'),
					'horaire' => 'on',
					'heure_pas' => 5,
				),
				'verifier' => array(
					'type' => 'date',
					'options' => array(
						'normaliser' => 'datetime',
					),
				),
			),
			array(
				'saisie' => 'date',
				'options' => array(
					'nom' => 'date_fin',
					'label' => _T('abonnement:champ_date_fin_label'),
					'horaire' => 'on',
					'heure_pas' => 5,
				),
				'verifier' => array(
					'type' => 'date',
					'options' => array(
						'normaliser' => 'datetime',
					),
				),
			),
		);
#		$saisies = array(
#			array(
#				'saisie' => 'fieldset',
#				'options' => array(
#					'nom' => 'date_fin_allonger',
#					'label' => _T('abonnement:champ_date_fin_allonger_label'),
#				),
#				'saisies' => array(
#					array(
#						'saisie' => 'input',
#						'options' => array(
#							'nom' => 'duree',
#							'label' => _T('abonnementsoffre:champ_duree_label'),
#							'defaut' => 0,
#						),
#						'verifier' => array(
#							'type' => 'entier',
#						),
#					),
#					array(
#						'saisie' => 'selection',
#						'options' => array(
#							'nom' => 'periode',
#							'label' => _T('abonnementsoffre:champ_periode_label'),
#							'cacher_option_intro' => 'oui',
#							'datas' => array(
#								'mois' => _T('abonnementsoffre:champ_periode_choix_mois'),
#								'jours' => _T('abonnementsoffre:champ_periode_choix_jours'),
#								'heures' => _T('abonnementsoffre:champ_periode_choix_heures'),
#							),
#						),
#					),
#				),
#			),
#		);
	}
	
	return $saisies;
}

/**
 * Identifier le formulaire en faisant abstraction des parametres qui ne representent pas l'objet edite
 */
function formulaires_editer_abonnement_identifier_dist($id_abonnement='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	return serialize(array(intval($id_abonnement)));
}

/**
 * Declarer les champs postes et y integrer les valeurs par defaut
 */
function formulaires_editer_abonnement_charger_dist($id_abonnement='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$valeurs = formulaires_editer_objet_charger('abonnement',$id_abonnement,'',$lier_trad,$retour,$config_fonc,$row,$hidden);
	unset($valeurs['id_abonnement']);
	return $valeurs;
}

/**
 * Verifier les champs postes et signaler d'eventuelles erreurs
 */
function formulaires_editer_abonnement_verifier_dist($id_abonnement='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
	$erreurs = array();//formulaires_editer_objet_verifier('abonnement',$id_abonnement);
	
	// Pour une création on vérifie l'offre
	if (
		!intval($id_abonnement)
		and (
			!$id_abonnements_offre = _request('id_abonnements_offre')
			or !sql_getfetsel('id_abonnements_offre', 'spip_abonnements_offres', 'id_abonnements_offre = '.intval($id_abonnements_offre))
		)
	) {
		$erreurs['message_erreur'] = _T('abonnement:erreur_id_abonnements_offre');
	}
	
	return $erreurs;
}

/**
 * Traiter les champs postes
 */
function formulaires_editer_abonnement_traiter_dist($id_abonnement='new', $retour='', $lier_trad=0, $config_fonc='', $row=array(), $hidden=''){
#	// Si c'est pour une modif on appelle l'allongement
#	if (intval($id_abonnement) > 0) {
#		// On récupère les champs qui ne font pas partie de la table
#		$duree = _request('duree');
#		$periode = _request('periode');
#		// On appelle l'action de modif d'échéance
#		$modifier_echeance = charger_fonction('modifier_echeance_abonnement', 'action/');
#		$modifier_echeance("$id_abonnement/$duree/$periode");
#	}
	
	$retours = formulaires_editer_objet_traiter('abonnement',$id_abonnement,'',$lier_trad,$retour,$config_fonc,$row,$hidden);
	
	return $retours;
}


?>
