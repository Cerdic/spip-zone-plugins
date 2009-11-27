<?php
/*
 * Plugin Notifications
 * (c) 2009 SPIP
 * Distribue sous licence GPL
 *
 */


function notifier_publication_auteurs_article($id_article) {
	include_spip('base/abstract_sql');
	$nom_site_spip = nettoyer_titre_email($GLOBALS['meta']["nom_site"]);
	$suivi_edito = $GLOBALS['meta']["suivi_edito"];

	if ($GLOBALS['notifications']['prevenir_auteurs_articles']) {
		$row = sql_fetsel("*", "spip_articles", "id_article = $id_article");
		if ($row) {

			$l = lang_select($row['lang']);

			// URL de l'article
			if (function_exists('generer_url_entite_absolue')) {
				$url = generer_url_entite_absolue($id_article, 'article');
			} else {
				charger_generer_url(false);
				$url = url_absolue(suivre_lien(_DIR_RACINE,generer_url_article($id_article, '','', 'publie')));
			}

			$titre = nettoyer_titre_email($row['titre']);

			$sujet = _T('info_publie_1', array('nom_site_spip' => $nom_site_spip, 'titre' => $titre));
			$courr = _T('info_publie_2')."\n\n";

			$nom = $GLOBALS['visiteur_session']['nom'];
			$nom = trim(supprimer_tags(typo($nom)));
			$courr .= _T('info_publie_01', array('titre' => $titre, 'connect_nom' => $nom))
				. "\n\n"
				. "-> " . $url
				. "\n";

		// Qui va-t-on prevenir ?
			$tous = array();

			$result_email = sql_select("auteurs.email", "spip_auteurs AS auteurs, spip_auteurs_articles AS lien", "lien.id_article=$id_article AND auteurs.id_auteur=lien.id_auteur");

			while ($qui = sql_fetch($result_email)) {
				$tous[] = $qui['email'];
			}

			// Nettoyer le tableau
			// Ne pas ecrire au posteur du message !
			$destinataires = array();
			foreach ($tous as $m) {
				if ($m = email_valide($m))
					$destinataires[$m]++;
			}

			//
			// Envoyer les emails
			//
			$envoyer_mail = charger_fonction('envoyer_mail','inc');
			foreach (array_keys($destinataires) as $email) {
				if (!function_exists('job_queue_add'))
					$envoyer_mail($email, $sujet, $courr);
				else
					job_queue_add('envoyer_mail',">$email : $sujet",array($email,$sujet,$courr),'inc/');
			}

			if ($l) lang_select();
		}
	}
}
?>