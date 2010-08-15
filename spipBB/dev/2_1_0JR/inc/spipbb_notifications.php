<?php
#
# FICHIER de BASE inc/notifications.php,
# function : notifications_forumvalide_dist()
/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *  Copyright (c) 2001-2007                                                *
\***************************************************************************/

#
# hack - scoty - gafospip 0.6 - 14/10/07
# Introduire la restriction fournie par chaque visiteur
# dans le champ profil : "refus_suivi_thread"
#
if (!defined("_ECRIRE_INC_VERSION")) return;

if (!defined('_INC_SPIPBB_COMMON')) include_spip('inc/spipbb_common');
spipbb_log("included",3,__FILE__);

// cette notification s'execute quand on valide un message 'prop'ose,
// dans ecrire/inc/forum_insert.php ; ici on va notifier ceux qui ne l'ont
// pas ete a la notification forumposte (sachant que les deux peuvent se
// suivre si le forum est valide directement ('pos' ou 'abo')

// Initialise les notifications "a la SpipBB"
function spipbb_chargespipbb($x) {
	// c'est ici qu'il faut mettre les initialisations eventuelles
	if (!is_array($GLOBALS['spipbb'])) $GLOBALS['spipbb']=@unserialize($GLOBALS['meta']['spipbb']);
    return $x;
}

// inspire de :
// http://doc.spip.org/@notifications_forumvalide_dist
function notifications_forumvalide($quoi, $id_forum) {

	$t = sql_fetsel("*", "spip_forum", "id_forum=".sql_quote($id_forum));
	if (!$t)
		return;

	// forum sur un message prive : pas de notification ici (cron)
	if (!@$t['id_article'] OR @$t['statut'] == 'perso') return;

	$s = sql_getfetsel('accepter_forum','spip_articles',"id_article=" . $t['id_article']);
	if (!$s)  $s = substr($GLOBALS['meta']["forums_publics"],0,3);

	if (strpos(@$GLOBALS['meta']['prevenir_auteurs'],",$s,")===false
	AND @$GLOBALS['meta']['prevenir_auteurs'] !== 'oui') // compat
		return;

	
	/* Cette partie sert à savoir si c'est un forum géré par SpipBB */
	if (isset($t['id_article'])) {
		$u = sql_fetsel("id_secteur","spip_articles","id_article=".sql_quote($t['id_article']));
		if (@$u['id_secteur'] AND $u['id_secteur']!=$GLOBALS['spipbb']['id_secteur']) {
			// si on n'est pas dans le secteur de spipbb on passe au traitement par defaut de la dist
			spipbb_log("appel de notifications_forumvalide_dist :".$u['id_secteur'].":".$GLOBALS['spipbb']['id_secteur'],3,__FILE__);
			if (!function_exists('inc_notifications_dist')) include_once _DIR_RESTREINT.'inc/notifications.php';
			return notifications_forumvalide_dist($quoi, $id_forum);
		}
		else {
			spipbb_log("traitement spipbb :".$u['id_secteur'].":".$GLOBALS['spipbb']['id_secteur'],3,__FILE__);
			$t['id_secteur']=$u['id_secteur'];
		}
	} else {
		// ce n'est pas un forum spipbb -> traitement par defaut de la dist
		spipbb_log("appel de notifications_forumvalide_dist : t id_article not set",3,__FILE__);
		if (!function_exists('inc_notifications_dist')) include_once _DIR_RESTREINT.'inc/notifications.php';
		return notifications_forumvalide_dist($quoi, $id_forum);
	}

	include_spip('inc/texte');
	include_spip('inc/filtres');
	include_spip('inc/autoriser');

	// Qui va-t-on prevenir ?
	$tous = array();
	$pasmoi = array();

	// 1. Les auteurs de l'article qui n'ont pas le droit de le moderer
	// (les autres l'ont recu plus tot)
	// c: 7/12/8 oui mais quand ?

	$result = sql_select("auteurs.id_auteur, auteurs.email", "spip_auteurs AS auteurs, spip_auteurs_articles AS lien", "lien.id_article=".sql_quote($t['id_article'])." AND auteurs.id_auteur=lien.id_auteur");

	while ($qui = sql_fetch($result)) {
		if (!autoriser('modererforum', 'article', $t['id_article'], $qui['id_auteur']))
				$tous[] = $qui['email'];
		else
				$pasmoi[] = $qui['email'];
	}

	// 2. Tous les participants a ce *thread*
	// TODO: proposer une case a cocher ou un lien dans le message
	// pour se retirer d'un troll (hack: replacer @ par % dans l'email)

	# _SUIVI_FORUM_THREAD => reactive par spipbb spipbb_options

	
	// voir par rapport à @$GLOBALS['meta']['prevenir_auteurs']

	if (defined('_SUIVI_FORUM_THREAD') AND (_SUIVI_FORUM_THREAD==true) ) {
		$infos=array();
		$s = sql_select("DISTINCT(email_auteur) AS email_auteur, id_auteur","spip_forum","id_thread=".$t['id_thread']." AND email_auteur != ''");
		while ($r = sql_fetch($s)) {
			# par defaut visiteur non-inscrit : pas de notif.
			if($r['id_auteur']!='0') {
				$tous[] = $r['email_auteur'];
			}

		}
	}

####

	// 3. Tous les auteurs des messages qui precedent (desactive egalement)
	// (possibilite exclusive de la possibilite precedente)
	// TODO: est-ce utile, par rapport au thread ?
	else if (defined('_SUIVI_FORUMS_REPONSES')
	AND _SUIVI_FORUMS_REPONSES
	AND $t['statut'] == 'publie') {
		$id_parent = $id_forum;
		while ($r = sql_fetsel("email_auteur , id_parent","spip_forum","id_forum=$id_parent AND statut='publie'") ) {
			$tous[] = $r['email_auteur'];
			$id_parent = $r['id_parent'];
		}
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

		$msg = email_notification_forum_spipbb($t, $email);
		$envoyer_mail($email, $msg['subject'], $msg['body']);
	}

}

// http://doc.spip.org/@notifications_forumposte_dist
function notifications_forumposte($quoi, $id_forum) {
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
	
	
		/* Cette partie sert à savoir si c'est un forum géré par SpipBB */
		$u = sql_fetsel("id_secteur","spip_articles","id_article=".sql_quote($id_article));
		if (@$u['id_secteur'] AND $u['id_secteur']!=$GLOBALS['spipbb']['id_secteur']) {
			// si on n'est pas dans le secteur de spipbb on passe au traitement par defaut de la dist
			spipbb_log("appel de notifications_forumposte_dist :".$u['id_secteur'].":".$GLOBALS['spipbb']['id_secteur'],3,__FILE__);
			if (!function_exists('inc_notifications_dist')) include_once _DIR_RESTREINT.'inc/notifications.php';
			return notifications_forumposte_dist($quoi, $id_forum);
		}
		else {
			$t['id_secteur']=$u['id_secteur'];
		}
		/* fin du controle */
		
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
		$msg = email_notification_forum_spipbb($t, $email);
		$envoyer_mail($email, $msg['subject'], $msg['body']);
	}

	// Notifier les autres si le forum est valide
	if ($t['statut'] == 'publie') {
		$notifications = charger_fonction('notifications', 'inc');
		$notifications('forumvalide', $id_forum);
	}
}


function generer_url_forum_spipbb($id_forum) {
	spipbb_log('generer_url_forum_spipbb :'.$id_forum.":",3,__FILE__);
	if (!function_exists('get_spip_script')) include_spip('inc/utils');
	return get_spip_script('./')
		. "?"._SPIP_PAGE."=voirsujet&id_forum=".$id_forum;
}

// http://doc.spip.org/@email_notification_forum
function email_notification_forum_spipbb ($t, $email) {
	spipbb_log('email_notification_forum_spipbb :'.serialize($t).":",3,__FILE__);

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
	// normalement ce qui precede ne devrait jamais arrive car traite par la fonction habituelle...
	else if ($t['statut'] == 'publie') # forum publie
	{
		// c'est là qu'on introduit url spipbb
		//$url = generer_url_entite($id_forum, 'forum');
		$url = generer_url_forum_spipbb($id_forum);
	}
	else #  forum modere, spam, poubelle direct ....
	{
		// idem pour le spam
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
	if (!function_exists('recuperer_fond')) include_spip('inc/utils');

	/*
	$corps = _T('form_forum_message_auto') . "\n\n"
		. $forum_poste_par
		. (($t['statut'] == 'publie') ? _T('forum_ne_repondez_pas')."\n" : '')
		. url_absolue($url)
		. "\n\n\n** ".textebrut(typo($t['titre']))
		."\n\n* ".textebrut(propre($t['texte']))
		. "\n\n".$t['nom_site']."\n".$t['url_site']."\n"
		. "\n\n SpipBB ".$GLOBALS['spipbb']['version'];
	*/

	$corps = recuperer_fond("prive/spipbb_notification_forum_email_body",
						array(
							'forum_poste_par' => $forum_poste_par,
							'reponse' => (($t['statut'] == 'publie') ? _T('forum_ne_repondez_pas')."\n" : '') ,
							'url_consultation' => url_absolue($url),
							'titre' => textebrut(typo($t['titre'])),
							'texte' => textebrut(propre($t['texte']))
							) 
						);

	if ($l)
		lang_select();

	return array('subject' => $sujet, 'body' => $corps);
}

?>