<?php


if (!defined("_ECRIRE_INC_VERSION")) return;

// Initialise les reglages sous forme de tableau
function Notifications_go($x) {
	if (!is_array($GLOBALS['notifications']
	= @unserialize($GLOBALS['meta']['notifications'])))
		$GLOBALS['notifications'] = array();
	return $x;
}

function Notifications_taches_generales_cron($taches_generales){
	$taches_generales['notifications'] = 60 * 20; // 20 minutes
	return $taches_generales;
}

function Notifications_pre_edition($x) {
	spip_log($x);

	if (isset($x['args']['table'])) {
		$notif = 'Notifications_'.$x['args']['table'];
		if (function_exists($notif)) {
			$x = $notif($x);
		}
	}

	return $x;
}

function notifications_instituerarticle($quoi, $id_article, $options) {

	// ne devrait jamais se produire
	if ($options['statut'] == $options['statut_ancien']) {
		spip_log("statut inchange");
		return;
	}

	include_spip('inc/texte');

	if ($options['statut'] == 'publie') {
		notifier_publication_article($id_article);
		notifier_publication_auteurs_article($id_article);
		}
		
	if ($options['statut'] == 'prop' AND $options['statut_ancien'] != 'publie')
		notifier_proposition_article($id_article);
}

function notifier_publication_auteurs_article($id_article) {

	$envoyer_mail = charger_fonction('envoyer_mail','inc');
	$nom_site_spip = nettoyer_titre_email($GLOBALS['meta']["nom_site"]);
	$suivi_edito = $GLOBALS['meta']["suivi_edito"];
	
	if ($suivi_edito == "oui" AND $GLOBALS['notifications']['prevenir_auteurs_articles']) {
		$row = sql_fetsel("*", "spip_articles", "id_article = $id_article");
		if ($row) {

			$l = lang_select($row['lang']);

			// URL de l'article
			charger_generer_url(false);
			$url = url_absolue(_DIR_RACINE.generer_url_article($id_article, '','', 'publie'));

			$titre = nettoyer_titre_email($row['titre']);

			$sujet = _T('info_publie_1', array('nom_site_spip' => $nom_site_spip, 'titre' => $titre));
			$sujet = _T('info_publie_1', array('nom_site_spip' => $debut_sujet, 'titre' => $titre));
			$courr = _T('info_publie_2')."\n\n";

			$nom = $GLOBALS['visiteur_session']['nom'];
			$nom = trim(supprimer_tags(typo($nom)));
			$courr .= _T('info_publie_01', array('titre' => $titre, 'connect_nom' => $nom))
				. "\n\n"
				. "-> " . $url
				. "\n";
				
		// Qui va-t-on prevenir ?
			$tous = array();
			
			$result_email = sql_select("auteurs.email", "spip_auteurs AS auteurs, spip_auteurs_articles AS lien", "lien.id_article=$id_article AND auteurs.id_auteur=lien.id_auteur");

			while ($qui = sql_fetch($result_email)) {
				$tous[] = $qui['email'];
			}

	// Nettoyer le tableau
	// Ne pas ecrire au posteur du message !
	$destinataires = array();
	foreach ($tous as $m) {
		if ($m = email_valide($m))
			$destinataires[$m]++;
	}

	//
	// Envoyer les emails
	//
	foreach (array_keys($destinataires) as $email) {

		$envoyer_mail($email, $sujet, $courr);
	}

			if ($l) lang_select();
		}
	}
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
		include_spip('balise/formulaire_inscription');
		$a = message_inscription($email, $nom, '6forum');
		if (!is_array($a)) {
			spip_log("erreur sur la creation d'auteur: $a");
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
	include_spip('inc/mail'); # un peu obsolete, cf. envoyer_mail
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
		envoyer_mail($email, $subject, $bodyc);
	}
}

// insertion d'une nouvelle signature => mail aux moderateurs
// definis par la constante _SPIP_MODERATEURS_PETITION
function Notifications_spip_signatures($x) {
	if (!$GLOBALS['notifications']['moderateurs_signatures'])
		return $x;

	$id_signature = $x['args']['id_objet'];

	$s = spip_query($q = "SELECT * FROM spip_signatures WHERE id_signature="._q($id_signature));

	if ($t = spip_fetch_array($s)) {

		$a = spip_fetch_array(spip_query("SELECT titre,lang FROM spip_articles WHERE id_article="._q($t['id_article'])));
		lang_select($a['lang']);

		$url = generer_url_article($t['id_article']);


		// creer la cle de suppression de la signature
		include_spip('inc/securiser_action');
		$cle = _action_auteur("supprimer signature $id_signature", '', '', 'alea_ephemere');
		$url_suppr =
			parametre_url(
			parametre_url($url,
				'var_confirm', $t['id_signature'], '&'),
				'refus', $cle, '&').'#sp'.$t['id_article'];

		$sujet = _L("Nouvelle signature pour la petition ").typo(supprimer_numero($a['titre']));

		$texte = $sujet
			. "\n\n** "
			. url_absolue($url)
			. "\n"
			. "\nNom: ". $x['data']['nom_email']
			. "\nemail: ". $x['data']['ad_email']
			. "\nmessage: ". $x['data']['message']
			. "\n\nnom_site: ". $x['data']['nom_site']
			. "\nurl_site: ". $x['data']['url_site']
			. "\n\n"
			. "Cette signature n'a pas encore ete validee ;"
			. "\nsi vous souhaitez la supprimer directement :"
			. "\n"
			. url_absolue($url_suppr)
			;

		Notifications_envoi($GLOBALS['notifications']['moderateurs_signatures'],
			$sujet, $texte);

		lang_dselect();

	}

	return $x;
}




function notifications_forumprive($quoi, $id_forum) {
	$s = spip_query("SELECT * FROM spip_forum WHERE id_forum="._q($id_forum));
	if (!$t = spip_fetch_array($s))
		return;

	include_spip('inc/texte');
	include_spip('inc/filtres');
	include_spip('inc/autoriser');


	// Qui va-t-on prevenir ?
	$tous = array();

	// 1. Prevenir les auteurs
	if ($GLOBALS['notifications']['prevenir_auteurs_prive']) {

		// 1.1. Les auteurs du message (si c'est un message)
		if ($t['id_message']) {
			$result = spip_query("SELECT auteurs.email FROM spip_auteurs AS auteurs, spip_auteurs_messages AS lien WHERE lien.id_message="._q($t['id_message'])." AND auteurs.id_auteur=lien.id_auteur");
	
			while ($qui = spip_fetch_array($result))
				$tous[] = $qui['email'];
	
			charger_generer_url();
			$url = generer_url_ecrire('message', 'id_message='.$t['id_message']) .'#id'.$t['id_forum'];
			$t['texte'] .= "\n\n"._T('forum_ne_repondez_pas')."\n<html>$url</html>";
		}
	
		// 1.2. Les auteurs de l'article (si c'est un article)
		elseif ($t['id_article']) {
			$result = spip_query("SELECT auteurs.email FROM spip_auteurs AS auteurs, spip_auteurs_articles AS lien WHERE lien.id_article="._q($t['id_article'])." AND auteurs.id_auteur=lien.id_auteur");
	
			while ($qui = spip_fetch_array($result))
				$tous[] = $qui['email'];
		}
	}

	// 2. Les moderateurs
	if ($GLOBALS['notifications']['moderateurs_forum_prive'])
	foreach (explode(',', $GLOBALS['notifications']['moderateurs_forum_prive']) as $m) {
		$tous[] = trim($m);
	}

	// 2. Tous les participants a ce *thread* (desactive pour l'instant)
	// TODO: proposer une case a cocher ou un lien dans le message
	// pour se retirer d'un troll (hack: replacer @ par % dans l'email)
	if ($GLOBALS['notifications']['thread_forum_prive']) {
		$s = spip_query("SELECT DISTINCT(email_auteur) FROM spip_forum WHERE id_thread=".$t['id_thread']." AND email_auteur != ''");
		while ($r = spip_fetch_array($s))
			$tous[] = $r['email_auteur'];
	}

/*
	// 3. Tous les auteurs des messages qui precedent (desactive egalement)
	// (possibilite exclusive de la possibilite precedente)
	// TODO: est-ce utile, par rapport au thread ?
	else if (defined('_SUIVI_FORUMS_REPONSES')
	AND _SUIVI_FORUMS_REPONSES) {
		$id_parent = $id_forum;
		while ($r = spip_fetch_array(spip_query("SELECT email_auteur, id_parent FROM spip_forum WHERE id_forum=$id_parent AND statut='publie'"))) {
			$tous[] = $r['email_auteur'];
			$id_parent = $r['id_parent'];
		}
	}
*/


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
	foreach (array_keys($destinataires) as $email) {
		$msg = email_notification_forum($t, $email);
		Notifications_envoi($email, $msg['subject'], $msg['body']);
	}
}


// cette notification s'execute quand on valide un message 'prop'ose,
// dans ecrire/inc/forum_insert.php ; ici on va notifier ceux qui ne l'ont
// pas ete a la notification forumposte (sachant que les deux peuvent se
// suivre si le forum est valide directement ('pos' ou 'abo')
function notifications_forumvalide($quoi, $id_forum) {
	$s = spip_query("SELECT * FROM spip_forum WHERE id_forum="._q($id_forum));
	if (!$t = spip_fetch_array($s))
		return;

	// forum sur un message prive : pas de notification ici (cron)
	if ($t['statut'] == 'perso')
		return;

	include_spip('inc/texte');
	include_spip('inc/filtres');
	include_spip('inc/autoriser');


	// Qui va-t-on prevenir ?
	$tous = array();
	$pasmoi = array();

	// 1. Les auteurs de l'article ; si c'est un article, ceux qui n'ont
	// pas le droit de le moderer (les autres l'ont recu plus tot)
	if ($t['id_article']
	AND $GLOBALS['notifications']['prevenir_auteurs']) {
		$result = spip_query("SELECT auteurs.* FROM spip_auteurs AS auteurs, spip_auteurs_articles AS lien WHERE lien.id_article="._q($t['id_article'])." AND auteurs.id_auteur=lien.id_auteur");

		while ($qui = spip_fetch_array($result)) {
			if (!autoriser('modererforum', 'article', $t['id_article'], $qui['id_auteur']))
				$tous[] = $qui['email'];
			else
				$pasmoi[] = $qui['email'];

		}
	}

	// 2. Tous les participants a ce *thread* (desactive pour l'instant)
	// TODO: proposer une case a cocher ou un lien dans le message
	// pour se retirer d'un troll (hack: replacer @ par % dans l'email)
	if ($GLOBALS['notifications']['thread_forum']) {
		$s = spip_query("SELECT DISTINCT(email_auteur) FROM spip_forum WHERE id_thread=".$t['id_thread']." AND email_auteur != ''");
		while ($r = spip_fetch_array($s))
			$tous[] = $r['email_auteur'];
	}

/*
	// 3. Tous les auteurs des messages qui precedent (desactive egalement)
	// (possibilite exclusive de la possibilite precedente)
	// TODO: est-ce utile, par rapport au thread ?
	else if (defined('_SUIVI_FORUMS_REPONSES')
	AND _SUIVI_FORUMS_REPONSES
	AND $t['statut'] == 'publie') {
		$id_parent = $id_forum;
		while ($r = spip_fetch_array(spip_query("SELECT email_auteur, id_parent FROM spip_forum WHERE id_forum=$id_parent AND statut='publie'"))) {
			$tous[] = $r['email_auteur'];
			$id_parent = $r['id_parent'];
		}
	}
*/

	// Nettoyer le tableau
	// Ne pas ecrire au posteur du message, ni au moderateur qui active le mail,
	// ni aux auteurs deja notifies precedemment
	$destinataires = array();
	foreach ($tous as $m) {
		if ($m = email_valide($m)
		AND $m != trim($t['email_auteur'])
		AND $m != $GLOBALS['auteur_session']['email']
		AND !in_array($m, $pasmoi))
			$destinataires[$m]++;
	}

	//
	// Envoyer les emails
	//
	foreach (array_keys($destinataires) as $email) {
		$msg = email_notification_forum($t, $email);
		Notifications_envoi($email, $msg['subject'], $msg['body']);
	}
}



function notifications_forumposte($quoi, $id_forum) {
	$s = spip_query("SELECT * FROM spip_forum WHERE id_forum="._q($id_forum));
	if (!$t = spip_fetch_array($s))
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
		$result = spip_query("SELECT auteurs.* FROM spip_auteurs AS auteurs, spip_auteurs_articles AS lien WHERE lien.id_article="._q($t['id_article'])." AND auteurs.id_auteur=lien.id_auteur");

		while ($qui = spip_fetch_array($result)) {
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
	foreach (array_keys($destinataires) as $email) {
		$msg = email_notification_forum($t, $email);
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
