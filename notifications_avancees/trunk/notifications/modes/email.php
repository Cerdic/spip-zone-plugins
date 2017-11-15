<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

// Envoi le contenu par email
function notifications_modes_email_envoyer_dist($contact, $contenu, $options) {
	// S'il y a le plugin Facteur, on peut faire un truc plus propre
	if (defined('_DIR_PLUGIN_FACTEUR')) {
		$corps = array(
			'texte' => $contenu['texte'],
		);
		// Si on a une version HTML
		if ($contenu['html']) {
			$corps['html'] = $contenu['html'];
		}
		//si un expéditeur est défini
		if ($contenu['from']) {
			$corps['from'] = $contenu['from'];
		}
		//si un nom d'expéditeur est défini
		if ($contenu['nom_envoyeur']) {
			$corps['nom_envoyeur'] = $contenu['nom_envoyeur'];
		}
		// S'il y a un Reply-to
		if ($options['repondre_a']) {
			$corps['repondre_a'] = $options['repondre_a'];
			if ($options['nom_repondre_a']) {
				$corps['nom_repondre_a'] = $options['nom_repondre_a'];
			}
		}
	} // Sinon c'est juste le texte
	else {
		$corps = $contenu['texte'];
	}

	$envoyer_mail = charger_fonction('envoyer_mail', 'inc/');
	return $envoyer_mail($contact, $contenu['court'], $corps);
}

// Renvoie une adresse e-mail ou rien
function notifications_modes_email_contact_dist($destinataire) {
	include_spip('inc/filtres');

	// Si c'est déjà un mail
	if (email_valide($destinataire)) {
		return $destinataire;
	} // Si c'est un id_auteur
	elseif (intval($destinataire) == $destinataire
		and $destinataire > 0
		and $email = sql_getfetsel('email', 'spip_auteurs', 'id_auteur = '.$destinataire)
	) {
		return $email;
	} // Sinon rien
	else {
		return null;
	}
}
