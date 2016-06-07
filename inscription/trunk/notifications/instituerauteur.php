<?php
/**
 * Plugin Inscription3 pour SPIP
 * © cmtmt, BoOz, kent1
 * Licence GPL v3
 *
 * Notifications au changement de statut d'un auteur
 */

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 * Notifier lors du changement de statut d'un auteur
 *
 * Basée sur :
 * http://doc.spip.org/@notifications_instituerarticle_dist
 *
 * @param string $quoi
 * @param int $id_auteur
 * @param array $options
 */
function notifications_instituerauteur($quoi, $id_auteur, $options) {
	// ne devrait jamais se produire
	if ($options['statut'] == $options['statut_ancien']) {
		spip_log('statut auteur inchange', 'notifications');
		return;
	}

	include_spip('inc/texte');
	include_spip('inscription3_mes_fonctions');

	$modele = '';

	/**
	 * Si l'ancien statut est 8aconfirmer
	 * - on notifie la validation s'il n'est pas mis à la poubelle
	 * - on notifie l'invalidation s'il est mis à la poubelle
	 *
	 * S'il est validé, on lui recrée un pass que l'on met dans le mail avec son login
	 */
	if ($options['statut_ancien'] == '8aconfirmer') {
		if ($options['statut'] == '5poubelle') {
			$modele = 'notifications/auteur_invalide';
			$modele_admin = 'notifications/auteur_invalide_admin';
		} else {
			/**
			 * Dans le cas d'une validation, on envoit le pass
			 * On regénère le mot de passe également
			 */
			include_spip('inc/acces');
			$pass = creer_pass_aleatoire(8, $id_auteur);
			include_spip('action/editer_auteur');
			instituer_auteur($id_auteur, array('pass' => $pass));

			$modele = 'notifications/auteur_valide';
			$fonction_user = 'auteur_pass';
			$modele_admin = 'notifications/auteur_valide_admin';
		}
	}

	if ($modele or $texte) {
		$options['type'] = 'user';
		$destinataires = array();

		$destinataires = pipeline(
			'notifications_destinataires',
			array(
				'args'=>array('quoi'=>$quoi,'id'=>$id_auteur,'options'=>$options),
				'data'=>$destinataires
			)
		);
		if ($modele) {
			if ($fonction_user == 'auteur_pass') {
				$texte = email_notification_auteur_pass($id_auteur, $modele, $pass);
			} else {
				$texte = email_notification_objet($id_auteur, 'auteur', $modele);
			}
		}
		notifications_envoyer_mails($destinataires, $texte);
	}

	if ($modele_admin) {
		$options['type'] = 'admin';
		$destinataires = array();

		$destinataires = pipeline(
			'notifications_destinataires',
			array(
				'args'=>array('quoi'=>$quoi,'id'=>$id_auteur,'options'=>$options),
				'data'=>$destinataires
			)
		);

		$texte = email_notification_objet($id_auteur, 'auteur', $modele_admin);
		notifications_envoyer_mails($destinataires, $texte);
	}
}

function email_notification_auteur_pass($id_auteur, $modele, $pass) {
	$envoyer_mail = charger_fonction('envoyer_mail', 'inc'); // pour nettoyer_titre_email
	return recuperer_fond($modele, array('id_auteur' => $id_auteur, 'pass' => $pass));
}
