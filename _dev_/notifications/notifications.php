<?php


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


?>