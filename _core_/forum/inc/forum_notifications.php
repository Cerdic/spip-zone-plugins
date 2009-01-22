<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2009                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;

// http://doc.spip.org/@email_notification_forum
function email_notification_forum ($t, $email) {

	// Rechercher eventuellement la langue du destinataire
	if (NULL !== ($l = sql_getfetsel('lang', 'spip_auteurs', "email=" . sql_quote($email))))
		$l = lang_select($l);

	$url = '';
	$id_forum = $t['id_forum'];

	if ($t['statut'] == 'prive') # forum prive
	{
		if ($t['id_article'])
			$url = generer_url_ecrire('articles', 'id_article='.$t['id_article']).'#id'.$id_forum;
		else if ($t['id_breve'])
			$url = generer_url_ecrire('breves_voir', 'id_breve='.$t['id_breve']).'#id'.$id_forum;
		else if ($t['id_syndic'])
			$url = generer_url_ecrire('sites', 'id_syndic='.$t['id_syndic']).'#id'.$id_forum;
	}
	else if ($t['statut'] == 'privrac') # forum general
	{
		$url = generer_url_ecrire('forum').'#id'.$id_forum;
	}
	else if ($t['statut'] == 'privadm') # forum des admins
	{
		$url = generer_url_ecrire('forum_admin').'#id'.$id_forum;
	}
	else if ($t['statut'] == 'publie') # forum publie
	{
		$url = generer_url_entite($id_forum, 'forum');
	}
	else #  forum modere, spam, poubelle direct ....
	{
		$url = generer_url_ecrire('controle_forum', "debut_id_forum=".$id_forum);
	}
	
	if (!$url) {
		spip_log("forum $id_forum sans referent");
		$url = './';
	}
	if ($t['id_article']) {
		$titre = textebrut(typo(sql_getfetsel("titre", "spip_articles", "id_article=".sql_quote($t['id_article']))));
	}
	if ($t['id_message']) {
		$titre = textebrut(typo(sql_getfetsel("titre", "spip_messages", "id_message=".sql_quote($t['id_message']))));
	}

	$sujet = "[" .
	  entites_html(textebrut(typo($GLOBALS['meta']["nom_site"]))) .
	  "] ["._T('forum_forum')."] ".typo($t['titre']);

	$parauteur = (strlen($t['auteur']) <= 2) ? '' :
	  (" " ._T('forum_par_auteur', array(
	  	'auteur' => $t['auteur'])
	  ) . 
	   ($t['email_auteur'] ? ' <' . $t['email_auteur'] . '>' : ''));

	$forum_poste_par = $t['id_article']
		? _T('forum_poste_par', array(
			'parauteur' => $parauteur, 'titre' => $titre)). "\n\n"
		: $parauteur . ' (' . $titre . ')';

	// TODO: squelettiser
	$corps = _T('form_forum_message_auto') . "\n\n"
		. $forum_poste_par
		. (($t['statut'] == 'publie') ? _T('forum_ne_repondez_pas')."\n" : '')
		. url_absolue($url)
		. "\n\n\n** ".textebrut(typo($t['titre']))
		."\n\n* ".textebrut(propre($t['texte']))
		. "\n\n".$t['nom_site']."\n".$t['url_site']."\n";

	if ($l)
		lang_select();

	return array('subject' => $sujet, 'body' => $corps);
}

// cette notification s'execute quand on valide un message 'prop'ose,
// dans ecrire/inc/forum_insert.php ; ici on va notifier ceux qui ne l'ont
// pas ete a la notification forumposte (sachant que les deux peuvent se
// suivre si le forum est valide directement ('pos' ou 'abo')
// http://doc.spip.org/@notifications_forumvalide_dist
function notifications_forumvalide_dist($quoi, $id_forum) {

	$t = sql_fetsel("*", "spip_forum", "id_forum=".sql_quote($id_forum));

	// forum sur un message prive : pas de notification ici (cron)
	if (!@$t['id_article'] OR @$t['statut'] == 'perso') return;

	$s = sql_getfetsel('accepter_forum','spip_articles',"id_article=" . $t['id_article']);
	if (!$s)  $s = substr($GLOBALS['meta']["forums_publics"],0,3);

	if (strpos(@$GLOBALS['meta']['prevenir_auteurs'],",$s,")===false
	AND @$GLOBALS['meta']['prevenir_auteurs'] !== 'oui') // compat
		return;

	include_spip('inc/texte');
	include_spip('inc/filtres');
	include_spip('inc/autoriser');

	// Qui va-t-on prevenir ?
	$tous = array();
	$pasmoi = array();

	// 1. Les auteurs de l'article qui n'ont pas le droit de le moderer
	// (les autres l'ont recu plus tot)

	$result = sql_select("auteurs.id_auteur, auteurs.email", "spip_auteurs AS auteurs, spip_auteurs_articles AS lien", "lien.id_article=".sql_quote($t['id_article'])." AND auteurs.id_auteur=lien.id_auteur");

	while ($qui = sql_fetch($result)) {
		if (!autoriser('modererforum', 'article', $t['id_article'], $qui['id_auteur']))
				$tous[] = $qui['email'];
		else
				$pasmoi[] = $qui['email'];
	}

	// Nettoyer le tableau
	// Ne pas ecrire au posteur du message, ni au moderateur qui active le mail,
	// ni aux auteurs deja notifies precedemment
	$destinataires = array();
	foreach ($tous as $m) {
		if ($m = email_valide($m)
		AND $m != trim($t['email_auteur'])
		AND $m != $GLOBALS['visiteur_session']['email']
		AND !in_array($m, $pasmoi))
			$destinataires[$m]++;
	}

	//
	// Envoyer les emails
	//
	$envoyer_mail = charger_fonction('envoyer_mail','inc');
	foreach (array_keys($destinataires) as $email) {
		$msg = email_notification_forum($t, $email);
		$envoyer_mail($email, $msg['subject'], $msg['body']);
	}
}


// http://doc.spip.org/@notifications_forumposte_dist
function notifications_forumposte_dist($quoi, $id_forum) {
	$t = sql_fetsel("*", "spip_forum", "id_forum=".sql_quote($id_forum));
	if (!$t) return;
	$id_article = $t['id_article'];

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
	$envoyer_mail = charger_fonction('envoyer_mail','inc');
	foreach (array_keys($destinataires) as $email) {
		$msg = email_notification_forum($t, $email);
		$envoyer_mail($email, $msg['subject'], $msg['body']);
	}

	// Notifier les autres si le forum est valide
	if ($t['statut'] == 'publie') {
		$notifications = charger_fonction('notifications', 'inc');
		$notifications('forumvalide', $id_forum);
	}
}