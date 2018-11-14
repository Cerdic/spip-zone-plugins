<?php

/**
 * Utilisation de pipelines
 *
 * @package SPIP\Changer_mail_generation_mdp\Pipelines
**/

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

/**
 *  Si le mail envoyé contient un mot de passe
 *  utiliser la personne qui a généré ce mot de passe comme expéditeur.
 *  @param array $flux
 *  @return array $flux
**/
function changer_mail_generation_mdp_notifications_envoyer_mails($flux) {
	include_spip('inc/utils');
	if (stripos($flux['sujet'], _T('form_forum_identifiants')) !== false) {
		$email_session = session_get('email');
		if ($email_session) {
			$email_session = session_get('nom')." <$email_session>";
			$flux['from'] = $email_session;
		}
	}
	return $flux;
}

