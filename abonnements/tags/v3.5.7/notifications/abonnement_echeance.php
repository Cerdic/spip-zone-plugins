<?php

if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}

/**
 * Cette notification s'exécute quand un abonnement arrive à échéance
 *
 * @param string $quoi
 *     Événement de notification
 * @param int $id
 *     id de l'objet en relation avec l'événement
 * @param array $options
 *     Options de notification
 */
function notifications_abonnement_echeance_dist($quoi, $id, $options) {
	$sujet = _T(
		"abonnement:notification_echeance_sujet_{$options['periode']}_{$options['quand']}",
		array(
			'duree' => $options['duree'],
		)
	);
	// Destinataires
	$destinataires = pipeline('notifications_destinataires',
		array(
			'args' => array(
				'quoi'    => $quoi,
				'id'      => $id,
				'options' => $options
			),
			'data' => $options['email'],
		)
	);
	// Modèle
	$texte = recuperer_fond(
		'notifications/abonnement_echeance',
		array(
			'id_abonnement' => $id,
			'nom'           => $options['nom'],
			'email'         => $options['email'],
			'duree'         => $options['duree'],
			'periode'       => $options['periode'],
			'quand'         => $options['quand'],
		)
	);
	// Go go go
	notifications_envoyer_mails($destinataires, $texte, $sujet);
}
