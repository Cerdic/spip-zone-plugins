<?php


if (!defined("_ECRIRE_INC_VERSION")) return;

// Initialise les reglages sous forme de tableau
function Notifications_go($x) {
	if (!is_array($GLOBALS['notifications']
	= @unserialize($GLOBALS['meta']['notifications'])))
		$GLOBALS['notifications'] = array();
	return $x;
}


// Regarder si l'auteur est dans la base de donnees, sinon l'ajouter
// comme s'il avait demande a s'inscrire comme visiteur
// Pour l'historique il faut retrouver le nom de la personne,
// pour ca on va regarder dans les forums existants
// Si c'est la personne connectee, c'est plus facile
function Notifications_creer_auteur($email) {
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


// Envoi des notifications
function Notifications_envoi($emails, $subject, $body) {
	$envoyer_mail = charger_fonction('envoyer_mail','inc');
	include_spip('inc/filtres'); # pour email_valide()

	// Attention $email peut etre une liste d'adresses, et on verifie qu'elle n'a pas de doublon
	$emails = array_unique(array_filter(array_map('email_valide',array_map('trim', explode(',',$emails)))));
	foreach ($emails as $email) {
		$bodyc = $body;
		if ($GLOBALS['notifications']['suivi']) {
			$a = Notifications_creer_auteur($email);
			if (is_array($a)
			AND isset($a['id_auteur']))
				$url = url_absolue(generer_url_public('suivi'));

			$bodyc .= "\n\n$url\n";
		}
		if (!function_exists('job_queue_add'))
			$envoyer_mail($email, $subject, $bodyc);
		else
			job_queue_add('envoyer_mail',"->$email : $subject",array($email, $subject, $bodyc),'inc/');
	}
}



function notifications_forumposte($quoi, $id_forum) {
	include_spip('base/abstract_sql');
	$s = sql_select("*","spip_forum","id_forum=".intval($id_forum));
	if (!$t = sql_fetch($s))
		return;

	include_spip('inc/texte');
	include_spip('inc/filtres');
	include_spip('inc/autoriser');


	// Qui va-t-on prevenir ?
	$tous = array();

	// 1. Les auteurs de l'article (si c'est un article), mais
	// seulement s'ils ont le droit de le moderer (les autres seront
	// avertis par la notifications_forumvalide).
	if ($t['id_article']
	AND $GLOBALS['notifications']['prevenir_auteurs']) {
		$result = sql_select("auteurs.*","spip_auteurs AS auteurs, spip_auteurs_articles AS lien","lien.id_article=".intval($t['id_article'])." AND auteurs.id_auteur=lien.id_auteur");

		while ($qui = sql_fetch($result)) {
			if (autoriser('modererforum', 'article', $t['id_article'], $qui['id_auteur']))
				$tous[] = $qui['email'];
		}
	}

	// 2. Les moderateurs definis par mes_options
	if ($GLOBALS['notifications']['moderateurs_forum'])
	foreach (explode(',', $GLOBALS['notifications']['moderateurs_forum']) as $m) {
		$tous[] = $m;
	}


	// Nettoyer le tableau
	// Ne pas ecrire au posteur du message !
	$destinataires = array();
	foreach ($tous as $m) {
		if ($m = email_valide($m)
		AND $m != trim($t['email_auteur']))
			$destinataires[$m]++;
	}

	//
	// Envoyer les emails
	//
	// detecter at afficher les url des liens du forum poste
	$links = array();
	foreach ($t as $champ)
		$links = $links + extraire_balises($champ,'a');
	$links = extraire_attribut($links,'href');
	$links = implode("\n",$links);
	if ($links)
		$links = "\n\n".$links;
	foreach (array_keys($destinataires) as $email) {
		$msg = email_notification_forum($t, $email);
		$msg['body'] .= $links;
		Notifications_envoi($email, $msg['subject'], $msg['body']);
	}

	// Notifier les autres si le forum est valide
	if ($t['statut'] == 'publie') {
		$notifications = charger_fonction('notifications', 'inc');
		$notifications('forumvalide', $id_forum);
	}

/* TODO
	// Envoyer un message de bienvenue/connexion au posteur du forum,
	// dans le cas ou il ne s'est pas authentifie
	// Souci : ne pas notifier comme ca si on est deja present dans le thread
	// (eviter d'avoir deux notificaitons pour ce message qu'on a, dans 99,99%
	// des cas, poste nous-memes !)
	if (strlen(trim($t['email_auteur']))
	AND email_valide($t['email_auteur'])
	AND !$GLOBALS['visiteur_session']['id_auteur']) {
		$msg = Notifications_jeuneposteur($t, $email);
		if ($t['email_auteur'] == 'fil@rezo.net')
			Notifications_envoi($t['email_auteur'], $msg['subject'], $msg['body']);
	}
*/

}

/*
// Creer un mail pour les forums envoyes par quelqu'un qui n'est pas authentifie
// en lui souhaitant la bienvenue et avec un lien suivi&p= de connexion au site
function Notifications_jeuneposteur($t, $email) {
	return array('test', 'coucou');
}
*/

?>
