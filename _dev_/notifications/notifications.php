<?php


if (!defined("_ECRIRE_INC_VERSION")) return;

// Ne sert qu'a charger les autres fonctions quand le plugin est appele
function Notifications_go($x) {
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


// insertion d'une nouvelle signature => mail aux moderateurs
// definis par la constante _SPIP_MODERATEURS_PETITION
function Notifications_spip_signatures($x) {
	if (!defined('_SPIP_MODERATEURS_PETITION')
	OR !_SPIP_MODERATEURS_PETITION)
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

		include_spip('inc/mail');
		envoyer_mail(_SPIP_MODERATEURS_PETITION,
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
	include_spip('inc/mail');
	include_spip('inc/autoriser');


	// Qui va-t-on prevenir ?
	$tous = array();
	// 1. Les auteurs de l'article (si c'est un article)
	if ($t['id_article']
	AND $GLOBALS['meta']['prevenir_auteurs'] == 'oui') {
		$result = spip_query("SELECT auteurs.email FROM spip_auteurs AS auteurs, spip_auteurs_articles AS lien WHERE lien.id_article="._q($t['id_article'])." AND auteurs.id_auteur=lien.id_auteur");

		while ($qui = spip_fetch_array($result))
			$tous[] = $qui['email'];
	}

	// 2. Les moderateurs definis par mes_options
	// TODO: a passer en meta
	// define('_MODERATEURS_FORUM_PRIVE', 'email1,email2,email3');
	if (defined('_MODERATEURS_FORUM_PRIVE'))
	foreach (explode(',', _MODERATEURS_FORUM_PRIVE) as $m) {
		$tous[] = $m;
	}

	// 2. Tous les participants a ce *thread* (desactive pour l'instant)
	// TODO: proposer une case a cocher ou un lien dans le message
	// pour se retirer d'un troll (hack: replacer @ par % dans l'email)
	if (defined('_SUIVI_FORUM_THREAD')
	AND _SUIVI_FORUM_THREAD) {
		$s = spip_query("SELECT DISTINCT(email_auteur) FROM spip_forum WHERE id_thread=".$t['id_thread']." AND email_auteur != ''");
		while ($r = spip_fetch_array($s))
			$tous[] = $r['email_auteur'];
	}

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
		envoyer_mail($email, $msg['subject'], $msg['body']);
	}

}

?>