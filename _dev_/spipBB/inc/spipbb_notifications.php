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

// cette notification s'execute quand on valide un message 'prop'ose,
// dans ecrire/inc/forum_insert.php ; ici on va notifier ceux qui ne l'ont
// pas ete a la notification forumposte (sachant que les deux peuvent se
// suivre si le forum est valide directement ('pos' ou 'abo')
// http://doc.spip.org/@notifications_forumvalide_dist
function notifications_forumvalide($quoi, $id_forum) {

	$s = sql_query("SELECT * FROM spip_forum WHERE id_forum="._q($id_forum));
	if (!$t = sql_fetch($s))
		return;

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
		$result = sql_query("SELECT auteurs.* FROM spip_auteurs AS auteurs, spip_auteurs_articles AS lien WHERE lien.id_article="._q($t['id_article'])." AND auteurs.id_auteur=lien.id_auteur");

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
		$s = sql_query("SELECT DISTINCT(email_auteur), id_auteur FROM spip_forum WHERE id_thread=".$t['id_thread']." AND email_auteur != ''");
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
		while ($r = sql_fetch(sql_query("SELECT email_auteur, id_parent FROM spip_forum WHERE id_forum=$id_parent AND statut='publie'"))) {
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
	foreach (array_keys($destinataires) as $email) {
		$msg = email_notification_forum($t, $email);
		envoyer_mail($email, $msg['subject'], $msg['body']);
	}

}

?>
