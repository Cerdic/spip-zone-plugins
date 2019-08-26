<?php

if (!defined('_ECRIRE_INC_VERSION')) return;

function notifications_instituerevenement_dist($quoi, $id_evenement, $options) {

	// ne devrait jamais se produire
	if ($options['statut'] == $options['statut_ancien']) {
		spip_log('statut inchange','notifications');
		return;
	}

	include_spip('inc/texte');

	$modele = '';
	if ($options['statut'] == 'publie') {
		$modele = 'notifications/evenement_publie';
	}

	if ($options['statut'] == 'prop' AND $options['statut_ancien'] != 'publie') {
		$modele = 'notifications/evenement_propose';
	}

	if ($modele) {
		$destinataires = array();
		if ($GLOBALS['meta']["suivi_edito"] == 'oui') {
			$destinataires = explode(',', $GLOBALS['meta']['adresse_suivi']);
		}

		$destinataires = pipeline('notifications_destinataires', array(
				'args' => array(
					'quoi' => $quoi,
					'id' => $id_evenement,
					'options' => $options
				),
				'data' => $destinataires
			)
		);

		$texte = email_notification_objet($id_evenement, 'evenement', $modele);
		notifications_envoyer_mails($destinataires, $texte);
	}
}
