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
spipbb_log("included",3,__FILE__);

// cette notification s'execute quand on valide un message 'prop'ose,
// dans ecrire/inc/forum_insert.php ; ici on va notifier ceux qui ne l'ont
// pas ete a la notification forumposte (sachant que les deux peuvent se
// suivre si le forum est valide directement ('pos' ou 'abo')

// c: 15/4/8
// il ne faudrait utiliser cette fonction que pour les id dans le secteur gere par spipbb et sinon renvoyer sur la fonction de la dist.
// sinon envoyer un email reformatte avec les bonnes URLs

// http://doc.spip.org/@notifications_forumvalide_dist
function notifications_forumvalide($quoi, $id_forum) {
	spipbb_log("notifications_forumvalide: $quoi : $id_forum :",3,__FILE__);
	// c: 10/2/8 compat multibases
	//$s = sql_query("SELECT * FROM spip_forum WHERE id_forum="._q($id_forum));
	$t = sql_fetsel("*", "spip_forum", "id_forum=".sql_quote($id_forum));
	if (!$t)
		return;

	// forum sur un message prive : pas de notification ici (cron)
	if (!@$t['id_article'] OR @$t['statut'] == 'perso') return;

	if (isset($t['id_article'])) {
		$u = sql_fetsel("id_secteur","spip_articles","id_article=".sql_quote($t['id_article']));
		if (@$u['id_secteur'] AND $u['id_secteur']!=$GLOBALS['spipbb']['id_secteur']) {
			// si on n'est pas dans le secteur de spipbb on passe au traitement par defaut de la dist
			spipbb_log("appel de notifications_forumvalide_dist :".$u['id_secteur'].":".$GLOBALS['spipbb']['id_secteur'],3,__FILE__);
			include _DIR_RESTREINT.'inc/notifications.php';
			return notifications_forumvalide_dist($quoi, $id_forum);
		}
		else {
			$t['id_secteur']=$u['id_secteur'];
		}
	} else {
		// ce n'est pas un forum spipbb -> traitement par defaut de la dist
		spipbb_log("appel de notifications_forumvalide_dist : t id_article not set",3,__FILE__);
		include _DIR_RESTREINT.'inc/notifications.php';
		return notifications_forumvalide_dist($quoi, $id_forum);
	}

	// c: 18/12/7 tous ces includes sont ils vraiment necessaires ?
	include_spip('inc/texte');
	include_spip('inc/filtres');
	include_spip('inc/mail');
	include_spip('inc/autoriser');

	// Qui va-t-on prevenir ?
	$tous = array();
	$pasmoi = array();

	// 1. Les auteurs de l'article ; si c'est un article, ceux qui n'ont
	// pas le droit de le moderer (les autres l'ont recu plus tot)
	if ($t['id_article']
	AND $GLOBALS['meta']['prevenir_auteurs'] == 'oui') {
		// c: 10/2/8 compat multibases
		//$result = sql_query("SELECT auteurs.* FROM spip_auteurs AS auteurs, spip_auteurs_articles AS lien WHERE lien.id_article="._q($t['id_article'])." AND auteurs.id_auteur=lien.id_auteur");
		$result = sql_select("auteurs.*",array("spip_auteurs AS auteurs","spip_auteurs_articles AS lien"),"lien.id_article="._q($t['id_article'])." AND auteurs.id_auteur=lien.id_auteur");

		while ($qui = sql_fetch($result)) {
			if (!autoriser('modererforum', 'article', $t['id_article'], $qui['id_auteur']))
				$tous[] = $qui['email'];
			else
				$pasmoi[] = $qui['email'];

		}
	}

	// 2. Tous les participants a ce *thread* (desactive pour l'instant)
	// TODO: proposer une case a cocher ou un lien dans le message
	// pour se retirer d'un troll (hack: replacer @ par % dans l'email)

#### hack gafospip 0.6
# _SUIVI_FORUM_THREAD => reactive par gafospip (gaf_mesoptions) !
#
	include_spip('inc/spipbb_auteur_infos'); // c: 18/12/7 necessaire ?

	if (defined('_SUIVI_FORUM_THREAD') AND (_SUIVI_FORUM_THREAD==true) ) {
		$infos=array();
		// c: 10/2/8 compat multibases
		//$s = sql_query("SELECT DISTINCT(email_auteur), id_auteur FROM spip_forum WHERE id_thread=".$t['id_thread']." AND email_auteur != ''");
		$s = sql_select(array("DISTINCT(email_auteur)","id_auteur"),"spip_forum","id_thread=".$t['id_thread']." AND email_auteur != ''");
		while ($r = sql_fetch($s)) {
			# par defaut visiteur non-inscrit : pas de notif.
			if($r['id_auteur']!='0') {
				$tous[] = $r['email_auteur'];
			}

			# participant au thread refuse de suivre ?
			$infos = spipbb_auteur_infos($r['id_auteur']); // c: 18/12/7 remplace gaf_auteur_infos

			if($infos['refus_suivi_thread'] && $infos['refus_suivi_thread']!='') {
				$refus=explode(",",$infos['refus_suivi_thread']);
				if (in_array($t['id_thread'],$refus) ) {
					$pasmoi[] = $r['email_auteur'];
				}
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
		// c: 10/2/8 compat multibases
		//while ($r = sql_fetch(sql_query("SELECT email_auteur, id_parent FROM spip_forum WHERE id_forum=$id_parent AND statut='publie'"))) {
		while ($r = sql_fetsel(array("email_auteur","id_parent"),"spip_forum","id_forum=$id_parent AND statut='publie'") ) {
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
		AND $m != $GLOBALS['auteur_session']['email']
		AND !in_array($m, $pasmoi))
			$destinataires[$m]++;
	}

	//
	// Envoyer les emails
	//
	// redefinir generer_url_forum d'abord
	foreach (array_keys($destinataires) as $email) {

		$msg = email_notification_forum_spipbb($t, $email);
		envoyer_mail($email, $msg['subject'], $msg['body']);
	}

}

function generer_url_forum_spipbb($id_forum) {
	spipbb_log('generer_url_forum_spipbb :'.$id_forum.":",3,__FILE__);
	if (!function_exists('get_spip_script')) include_spip('inc/utils');
	return get_spip_script('./')."?page=voirsujet&id_forum=".$id_forum;
}

// http://doc.spip.org/@email_notification_forum
function email_notification_forum_spipbb ($t, $email) {
	spipbb_log('email_notification_forum_spipbb :'.serialize($t).":",3,__FILE__);
	// Rechercher eventuellement la langue du destinataire
	if (NULL !== ($l = sql_getfetsel('lang', 'spip_auteurs', "email=" . sql_quote($email))))
		$l = lang_select($l);


	charger_generer_url();

	if ($t['statut'] == 'prop') # forum modere
	{
		$url = generer_url_ecrire('controle_forum', "debut_id_forum=".$t['id_forum']);
	}
	else if ($t['statut'] == 'prive') # forum prive
	{
		if ($t['id_article'])
			$url = generer_url_ecrire('articles', 'id_article='.$t['id_article']).'#id'.$t['id_forum'];
		else if ($t['id_breve'])
			$url = generer_url_ecrire('breves_voir', 'id_breve='.$t['id_breve']).'#id'.$t['id_forum'];
		else if ($t['id_syndic'])
			$url = generer_url_ecrire('sites', 'id_syndic='.$t['id_syndic']).'#id'.$t['id_forum'];
	}
	else if ($t['statut'] == 'privrac') # forum general
	{
		$url = generer_url_ecrire('forum').'#id'.$t['id_forum'];
	}
	else if ($t['statut'] == 'privadm') # forum des admins
	{
		$url = generer_url_ecrire('forum_admin').'#id'.$t['id_forum'];
	}
	else if (function_exists('generer_url_forum_spipbb')) {
		$url = generer_url_forum_spipbb($t['id_forum']);
	}
	else if (function_exists('generer_url_forum')) {
		$url = generer_url_forum($t['id_forum']);
	} else {
		spip_log('inc-urls personnalise : ajoutez generer_url_forum() !');
		if ($t['id_article'])
			$url = generer_url_article($t['id_article']).'#'.$t['id_forum'];
		else
			$url = './';
	}

	if ($t['id_article']) {
		$article = sql_fetsel("titre", "spip_articles", "id_article=".sql_quote($t['id_article']));
		$titre = textebrut(typo($article['titre']));
	}
	if ($t['id_message']) {
		$message = sql_fetsel("titre", "spip_messages", "id_message=".sql_quote($t['id_message']));
		$titre = textebrut(typo($message['titre']));
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

?>