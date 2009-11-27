<?php
/*
 * Plugin Notifications
 * (c) 2009 SPIP
 * Distribue sous licence GPL
 *
 */

$GLOBALS['notifications_post_edition']['spip_signatures'] = "petitionsignee";

// Initialise les reglages sous forme de tableau
function notifications_go($x) {
	if (!is_array($GLOBALS['notifications']
	= @unserialize($GLOBALS['meta']['notifications'])))
		$GLOBALS['notifications'] = array();
	return $x;
}


/**
 * Pipeline post-edition
 * pour permettre de se pluger sur une modification en base non notifiee par defaut
 *
 * @param array $x
 * @return array
 */
function notifications_post_edition($x) {
	spip_log($x,'notifications');

	if (isset($x['args']['table'])
		AND $quoi=$GLOBALS['notification_post_edition'][$x['args']['table']]
	  AND $notification = charger_fonction($quoi,'notifications',true)) {
			$notification($quoi,$x['args']['id_objet']);
	}

	return $x;
}

/**
 * Ajouter des destinataires dans une notification en lot
 *
 * @param array $flux
 * @return array
 */
function notifications_notifications_destinataires($flux) {
	$quoi = $flux['args']['quoi'];

	// publication d'article : prevenir les auteurs
	if ($quoi=='instituer_article'
	  AND $GLOBALS['notifications']['prevenir_auteurs_articles']){
		$id_article = $flux['args']['id'];
		$options = $flux['args']['options'];

		include_spip('base/abstract_sql');

		// Qui va-t-on prevenir en plus ?
		$result_email = sql_select("auteurs.email", "spip_auteurs AS auteurs, spip_auteurs_articles AS lien", "lien.id_article=".intval($id_article)." AND auteurs.id_auteur=lien.id_auteur");

		while ($qui = sql_fetch($result_email)) {
			$flux['data'][] = $qui['email'];
		}

	}

	// forum valide ou prive : prevenir les autres contributeurs du thread
	if (($quoi=='forumprive' AND $GLOBALS['notifications']['thread_forum_prive'])
		OR ($quoi=='forumvalide' AND $GLOBALS['notifications']['thread_forum'])
	  ){

		$id_forum = $flux['args']['id'];
		$options = $flux['args']['options'];
		if ($t = $options['forum']
			OR $t = sql_fetsel("*", "spip_forum", "id_forum=".intval($id_forum))){

			// Tous les participants a ce *thread*
			// TODO: proposer une case a cocher ou un lien dans le message
			// pour se retirer d'un troll (hack: replacer @ par % dans l'email)
			$s = sql_select("DISTINCT(email_auteur)","spip_forum","id_thread=".intval($t['id_thread'])." AND email_auteur != ''");
			while ($r = sql_fetch($s))
				$flux['data'][] = $r['email_auteur'];

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
		}
	}

	// Les moderateurs de forums public
	if ($quoi=='forumpposte' AND $GLOBALS['notifications']['moderateurs_forum']){
		foreach (explode(',', $GLOBALS['notifications']['moderateurs_forum']) as $m) {
			$flux['data'][] = $m;
		}
	}

	return $flux;
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
			notifications_envoyer_mails($t['email_auteur'], $msg['body'],$msg['subject'])
	}
*/
?>