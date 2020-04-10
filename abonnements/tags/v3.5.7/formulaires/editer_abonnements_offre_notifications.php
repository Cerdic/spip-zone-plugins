<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

function formulaires_editer_abonnements_offre_notifications_saisies_dist($id_abonnements_offre, $retour=''){
	return array(
		array(
			'saisie' => 'fieldset',
			'options' => array(
				'nom' => 'fieldset_ajouter',
				'label' => _T('abonnementsoffre:champ_ajouter_notification_label'),
				'pliable' => 'oui',
				'plie' => 'oui',
			),
			'saisies' => array(
				array(
					'saisie' => 'selection',
					'options' => array(
						'nom' => 'quand',
						'label' => _T('abonnementsoffre:champ_quand_label'),
						'cacher_option_intro' => 'oui',
						'datas' => array(
							'apres' => _T('abonnementsoffre:champ_quand_choix_apres'),
							'avant' => _T('abonnementsoffre:champ_quand_choix_avant'),
							'pendant' => _T('abonnementsoffre:champ_quand_choix_pendant'),
						),
						'defaut' => 'avant',
					),
				),
				array(
					'saisie' => 'input',
					'options' => array(
						'nom' => 'duree',
						'label' => _T('abonnementsoffre:champ_duree_label'),
						'defaut' => '',
						'afficher_si_remplissage' => '@quand@ !== "pendant"',
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
						'cacher_option_intro' => 'oui',
						'datas' => array(
							'mois' => _T('abonnementsoffre:champ_periode_choix_mois'),
							'jours' => _T('abonnementsoffre:champ_periode_choix_jours'),
						),
						'defaut' => 'mois',
						'afficher_si_remplissage' => '@quand@ !== "pendant"',
					),
				),
			),
		),
	);
}

function formulaires_editer_abonnements_offre_notifications_charger_dist($id_abonnements_offre, $retour=''){
	if (!$id_abonnements_offre or !autoriser('configurer')){
		return false;
	}
	
	$contexte = array(
		'supprimer' => array(),
		'id_abonnements_offre' => $id_abonnements_offre,
		'_hidden' => '<input type="hidden" name="id_abonnements_offre" value="'.$id_abonnements_offre.'" />',
	);
	return $contexte;
}

function formulaires_editer_abonnements_offre_notifications_verifier_dist($id_abonnements_offre, $retour=''){
	$erreurs = array();
	
	if (!$supprimer = _request('supprimer')){
		$duree   = _request('duree');
		$periode = _request('periode');
		$quand   = _request('quand');
		// Normalisons les valeurs pour les notifs le jour même
		if ($quand == 'pendant'
			or (
					$quand != 'pendant'
					and $duree == 0
				)
		){
			$duree   = 0;
			$periode = 'jours';
			$quand   = 'pendant';
			set_request('duree', $duree);
			set_request('periode', $periode);
			set_request('quand', $quand);
		}
		// Durée obligatoire
		// (ne pas utiliser l'option "obligatoire" des saisies car on ne pourrait pas supprimer)
		if (!strlen($duree)){
			$erreurs['duree'] = _T('info_obligatoire');
		}
		// Vérifier les doublons
		if (sql_countsel('spip_abonnements_offres_notifications', array(
			'id_abonnements_offre = ' . intval($id_abonnements_offre),
			'duree = ' . intval($duree),
			'periode = ' . sql_quote($periode),
			'quand = ' . sql_quote($quand),
		))){
			$erreurs['message_erreur'] = _T('abonnementsoffre:erreur_notification_doublon');
		}
	}
	
	return $erreurs;
}

function formulaires_editer_abonnements_offre_notifications_traiter_dist($id_abonnements_offre, $retour=''){
	// Si on demande à enregistrer une nouvelle notif
	if (!$supprimer = _request('supprimer')){
		include_spip('inc/editer');
		$retours = formulaires_editer_objet_traiter('abonnements_offres_notification','new','','',$retour,'','','');
	}
	// Sinon c'est pour en supprimer
	elseif (is_array($supprimer)){
		foreach ($supprimer as $id_notification=>$valeur){
			if ($id_notification = intval($id_notification)){
				sql_delete('spip_abonnements_offres_notifications', 'id_abonnements_offres_notification = '.$id_notification);
				$retours = array('redirect' => $retour);
			}
		}
	}
	
	return $retours;
}
