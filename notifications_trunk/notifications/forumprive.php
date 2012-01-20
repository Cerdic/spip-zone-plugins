<?php
/*
 * Plugin Notifications
 * (c) 2009 SPIP
 * Distribue sous licence GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;

/**
 * notification appelee quand on poste un forum prive
 *
 * @param string $quoi
 * @param int $id_forum
 * @param array $options
 * @return <type>
 */
function notifications_forumprive_dist($quoi, $id_forum, $options) {
	include_spip('base/abstract_sql');

	$t = sql_fetsel("*", "spip_forum", "id_forum=".intval($id_forum));
	if (!$t)
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
			$result = sql_select("auteurs.email","spip_auteurs AS auteurs, spip_auteurs_messages AS lien","lien.id_message=".intval($t['id_message'])." AND auteurs.id_auteur=lien.id_auteur");

			while ($qui = sql_fetch($result))
				$tous[] = $qui['email'];

			$url = url_absolue(generer_url_entite($id_message, 'message'));
			$t['texte'] .= "\n\n"._T('forum_ne_repondez_pas')."\n<html>$url</html>";
		}

		// 1.2. Les auteurs de l'article (si c'est un article)
		elseif ($t['id_article']) {
			$result = sql_select("auteurs.email","spip_auteurs AS auteurs, spip_auteurs_articles AS lien","lien.id_article=".intval($t['id_article'])." AND auteurs.id_auteur=lien.id_auteur");

			while ($qui = sql_fetch($result))
				$tous[] = $qui['email'];
		}
	}

// 2. Les moderateurs
	if ($GLOBALS['notifications']['moderateurs_forum_prive']){
		foreach (explode(',', $GLOBALS['notifications']['moderateurs_forum_prive']) as $m) {
			$tous[] = $m;
		}
	}

	$options['forum'] = $t;
	$destinataires = pipeline('notifications_destinataires',
		array(
			'args'=>array('quoi'=>$quoi,'id'=>$id_forum,'options'=>$options)
		,
			'data'=>$tous)
	);

	// Nettoyer le tableau
	// Ne pas ecrire au posteur du message !
	notifications_nettoyer_emails($destinataires,array($t['email_auteur']));

	//
	// Envoyer les emails
	//
	foreach ($destinataires as $email) {
		$texte = email_notification_forum($t, $email);
		notifications_envoyer_mails($email, $texte);
	}
}

?>