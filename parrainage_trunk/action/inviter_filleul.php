<?php

// Sécurité
if (!defined('_ECRIRE_INC_VERSION')) return;

/**
 * Inviter un filleul
 * @param unknown_type $arg
 * @return unknown_type
 */
function action_inviter_filleul_dist($arg=null, $message='') {
	if (is_null($arg)){
		$securiser_action = charger_fonction('securiser_action', 'inc');
		$arg = $securiser_action();
	}

	// On fait quelque chose seulement si on trouve le filleul
	// Et qu'il y a une raison de le contacter
	if ($id_filleul = intval($arg)
		and $id_filleul > 0
		and $filleul = sql_fetsel('*', 'spip_filleuls', 'id_filleul = '.$id_filleul)
		and in_array($filleul['statut'], array('en_cours', 'contact', 'sans_nouvelles'))
	){
		// On récupère les infos dont on va avoir besoin
		include_spip('inc/config');
		$envoyer_mail = charger_fonction('envoyer_mail', 'inc/');
		$parrain = sql_fetsel('nom, email', 'spip_auteurs', 'id_auteur = '.$filleul['id_parrain']);
		
		// On construit les différents paramètres
		$destinataire = $filleul['email'];
		$sujet = _T('parrainage:invitation_sujet', array('nom'=>$parrain['nom'], 'site'=>$GLOBALS['meta']['nom_site']));
		$email_envoyeur = $parrain['email'];
		$nom_envoyeur = $parrain['nom'];
		$url_invitation = lire_config('parrainage/url_inscription', generer_url_public('inscription'));
		$url_invitation = parametre_url($url_invitation, 'invitation', $filleul['code_invitation']);
		$infos = array(
			'id_filleul' => $id_filleul,
			'nom_filleul' => $filleul['nom'],
			'nom_parrain' => $parrain['nom'],
			'code_invitation' => $filleul['code_invitation'],
			'url_invitation' => $url_invitation,
			'message' => $message
		);
		
		// On génère le mail HTML avec le fond
		$html = recuperer_fond(
			'notifications/inviter_filleul',
			$infos
		);
		// Si le fond existe, on génère la version texte avec le squelette
		if (find_in_path('notifications/inviter_filleul_texte.html')){
			$texte = recuperer_fond(
				'notifications/inviter_filleul_texte',
				$infos
			);
		}
		// Sinon on le génère à partir du HTML
		else{
			include_spip('classes/facteur');
			$texte = Facteur::html2text($html);
		}
		
		// On utilise la forme avancé de Facteur
		$corps = array(
			'html' => $html,
			'texte' => $texte,
			'nom_envoyeur' => $nom_envoyeur
		);
		
		// On envoie enfin le courriel
		$ok = $envoyer_mail(
			$destinataire,
			$sujet,
			$corps,
			$email_envoyeur,
			'X-Originating-IP: '.$GLOBALS['ip']
		);
		
		// Si ça a marché, on modifie les infos du filleul
		if ($ok){
			sql_updateq(
				'spip_filleuls',
				array(
					'statut' => 'invite',
					'date_invitation' => date('Y-m-d H:i:s')
				),
				'id_filleul = '.$id_filleul
			);
		}
	}
}

?>
