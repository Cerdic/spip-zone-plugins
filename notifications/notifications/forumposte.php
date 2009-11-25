<?php
/*
 * Plugin xxx
 * (c) 2009 xxx
 * Distribue sous licence GPL
 *
 */


/**
 * cette notification s'execute quand un message est poste,
 *
 * @param string $quoi
 * @param int $id_forum
 */
function notifications_forumposte_dist($quoi, $id_forum) {
	$t = sql_fetsel("*", "spip_forum", "id_forum=".sql_quote($id_forum));
	if (!$t
	OR !$id_article = $t['id_article'])
		return;

	include_spip('inc/texte');
	include_spip('inc/filtres');
	include_spip('inc/autoriser');

	// Qui va-t-on prevenir ?
	$tous = array();

	// 1. Les auteurs de l'article (si c'est un article), mais
	// seulement s'ils ont le droit de le moderer (les autres seront
	// avertis par la notifications_forumvalide).
	if ($id_article) {
		$s = sql_getfetsel('accepter_forum','spip_articles',"id_article=" . $id_article);
		if (!$s)  $s = substr($GLOBALS['meta']["forums_publics"],0,3);

		if (strpos(@$GLOBALS['meta']['prevenir_auteurs'],",$s,")!==false
		OR @$GLOBALS['meta']['prevenir_auteurs'] === 'oui') // compat
		  {
			$result = sql_select("auteurs.id_auteur, auteurs.email", "spip_auteurs AS auteurs, spip_auteurs_articles AS lien", "lien.id_article=".sql_quote($id_article)." AND auteurs.id_auteur=lien.id_auteur");

			while ($qui = sql_fetch($result)) {
			  if (autoriser('modererforum', 'article', $id_article, $qui['id_auteur']))
				$tous[] = $qui['email'];
			}
		  }
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
		notifier_envoyer_mails($email, $msg['subject'], $msg['body']);
	}

	// Notifier les autres si le forum est valide
	if ($t['statut'] == 'publie') {
		$notifications = charger_fonction('notifications', 'inc');
		$notifications('forumvalide', $id_forum);
	}
}
?>