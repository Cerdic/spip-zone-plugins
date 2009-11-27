<?php
/*
 * Plugin Notifications
 * (c) 2009 SPIP
 * Distribue sous licence GPL
 *
 */


/**
 * cette notification s'execute quand on valide un message 'prop'ose,
 * dans ecrire/inc/forum_insert.php ; ici on va notifier ceux qui ne l'ont
 * pas ete a la notification forumposte (sachant que les deux peuvent se
 * suivre si le forum est valide directement ('pos' ou 'abo')
 * http://doc.spip.org/@notifications_forumvalide_dist
 *
 * @param string $quoi
 * @param int $id_forum
 */
function notifications_forumvalide_dist($quoi, $id_forum, $options) {

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
	// Ne pas ecrire au posteur du message, ni au moderateur qui active le mail,
	$pasmoi = array($t['email_auteur'],$GLOBALS['visiteur_session']['email']);

	// 1. Les auteurs de l'article qui n'ont pas le droit de le moderer
	// (les autres l'ont recu plus tot)

	$result = sql_select("auteurs.id_auteur, auteurs.email", "spip_auteurs AS auteurs, spip_auteurs_articles AS lien", "lien.id_article=".sql_quote($t['id_article'])." AND auteurs.id_auteur=lien.id_auteur");

	while ($qui = sql_fetch($result)) {
		if (!autoriser('modererforum', 'article', $t['id_article'], $qui['id_auteur']))
				$tous[] = $qui['email'];
		else
				// Ne pas ecrire aux auteurs deja notifies precedemment
				$pasmoi[] = $qui['email'];
	}


	$destinataires = pipeline('notifications_destinataires',
		array(
			'args'=>array('quoi'=>$quoi,'id'=>$id_forum,'options'=>$options)
		,
			'data'=>$tous)
	);

	// Nettoyer le tableau
	// en enlevant les exclus
	notifications_nettoyer_emails($destinataires,$pasmoi);

	//
	// Envoyer les emails
	//
	foreach ($destinataires as $email) {
		$texte = email_notification_forum($t, $email);
		notifications_envoyer_mails($email, $texte);
	}

}

?>
