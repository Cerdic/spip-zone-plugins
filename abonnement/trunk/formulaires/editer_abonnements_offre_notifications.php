<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

function formulaires_editer_abonnements_offre_notifications_saisies_dist($id_abonnements_offre, $retour=''){
	return array(
		array(
			'saisie' => 'input',
			'options' => array(
				'nom' => 'duree',
				'label' => _T('abonnementsoffre:champ_duree_label'),
				'defaut' => '',
			),
			'verifier' => array(
				'type' => 'entier',
				'options' => array(
					'min' => 1,
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
			),
		),
	);
}

function formulaires_editer_abonnements_offre_notifications_charger_dist($id_abonnements_offre, $retour=''){
	$contexte = array(
		'supprimer' => array(),
	);
	return $contexte;
}

function formulaires_editer_abonnements_offre_notifications_verifier_dist($id_abonnements_offre, $retour=''){
	$erreurs = array();
	
	if (!$supprimer = _request('supprimer')){
		if (!_request('duree')){
			$erreurs['duree'] = _T('info_obligatoire');
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
