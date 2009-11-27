<?php


if (!defined("_ECRIRE_INC_VERSION")) return;


// Regarder si l'auteur est dans la base de donnees, sinon l'ajouter
// comme s'il avait demande a s'inscrire comme visiteur
// Pour l'historique il faut retrouver le nom de la personne,
// pour ca on va regarder dans les forums existants
// Si c'est la personne connectee, c'est plus facile
function Notifications_creer_auteur($email) {
			if ($GLOBALS['notifications']['suivi']) {
			$a = Notifications_creer_auteur($email);
			if (is_array($a)
			AND isset($a['id_auteur']))
				$url = url_absolue(generer_url_public('suivi'));

			$bodyc .= "\n\n$url\n";
		}

	include_spip('base/abstract_sql');
	if (!$a = sql_fetsel('*', 'spip_auteurs', 'email='.sql_quote($email))) {
		if ($GLOBALS['visiteur_session']['session_email'] === $email
		AND isset($GLOBALS['visiteur_session']['session_nom'])) {
			$nom = $GLOBALS['visiteur_session']['session_nom'];
		} else {
			if ($b = sql_fetsel('auteur', 'spip_forum',
				'email_auteur='.sql_quote($email).' AND auteur!=""',
				/* groupby */'', /* orderby */ array('date_heure DESC'),
				/* limit */ '1')
			) {
				$nom = $b['auteur'];
			} else {
				$nom = $email;
			}
		}
		// charger message_inscription()
		include_spip('balise/formulaire_inscription'); # pour SPIP 1.9.2
		include_spip('formulaires/inscription'); # pour SPIP 2.0
		if (function_exists('message_inscription')) {
			$a = message_inscription($email, $nom, '6forum');
		} else if (function_exists('formulaires_inscription_traiter_dist')) {
			// "pirater" les globals
			$_GET['nom_inscription'] = $nom;
			$_GET['email_inscription'] = $email;
			$a = formulaires_inscription_traiter_dist('6forum', null);
		}
		if (!is_array($a)) {
			spip_log("erreur sur la creation d'auteur: $a",'notifications');
			next;
		}
	}

	// lui donner un cookie_oubli s'il n'en a pas deja un
	if (!isset($a['cookie_oubli'])) {
		include_spip('inc/acces'); # pour creer_uniqid
		$a['cookie_oubli'] = creer_uniqid();
		sql_updateq('spip_auteurs',
			array('cookie_oubli' => $a['cookie_oubli']),
			'id_auteur='.$a['id_auteur']
		);
	}

	return $a;
}


/*
// Creer un mail pour les forums envoyes par quelqu'un qui n'est pas authentifie
// en lui souhaitant la bienvenue et avec un lien suivi&p= de connexion au site
function Notifications_jeuneposteur($t, $email) {
	return array('test', 'coucou');
}
*/

?>
