<?php
/*
 * Plugin Alertes
 * Distribué sous licence GPL
 *
 * Système d'envoi des emails d'alertes par pseudo-CRON SPIP.
 */

if (!defined("_ECRIRE_INC_VERSION")) {
	return;
}

function genie_alertes_dist($time) {
	spip_log('Lancement du cron ' . __FUNCTION__, 'alertes');
	include_spip('base/abstract_sql');
	$now = date('Y-m-d H:i:s');
	//Récupération de la configuration
	include_spip('inc/config');
	$a = lire_config('config_alertes');
	//Est-ce que Accès restreint est activé ?
	$plugins = lire_config('plugin');
	if (is_array($plugins['ACCESRESTREINT'])) {
		$acces_restreint = true;
	} else {
		$acces_restreint = false;
	}
	$groupby = array();
	$orderby = array();
	$limit = "0, " . intval($a['nb_mails']);
	$nb_max = sql_countsel("spip_alertes_cron",
		"date_pour_envoi <= '" . $now . "'"); //Nombres total d'alerte à traiter, donc de mail à envoyer.
	//Si on a plus d'alertes prevues que d'email autorisé à envoyer, il faudra poursuivre la tâche.
	if ($nb_max > intval($a['nb_mails'])) {
		$time = -intval($a['nb_mails']); //Si j'ai bien compris, temps en seconde avant de relancer la tâche non-terminée.
		spip_log('Le temps sera fixe a ' . $time, 'alertes');
	}
	//Parcours des alertes demandées, qui ne doivent pas être dans le futur
	if ($resultats = sql_select("*", "spip_alertes_cron", "date_pour_envoi <= '" . $now . "'", $groupby, $orderby,
		$limit)
	) {
		include_spip('classes/facteur'); //dépendance Facteur
		$envoyer_mail = charger_fonction('envoyer_mail', 'inc/');
		spip_log("Il y a des resultats\n" . print_r($resultats, true), 'alertes');
		//Envoi des emails d'alertes
		while ($res = sql_fetch($resultats)) {
			$id_article = $res['id_objet']; //Pour l'instant ça ne gère que les articles
			$id_auteur = $res['id_auteur'];
			$article_accessible = true;
			spip_log('Pour article #' . $id_article . ', on a auteur #' . $id_auteur . ' qui est abo', 'alertes');
			//On récupere l'email de l'auteur concerné
			if ($auteur = sql_select('id_auteur,email', 'spip_auteurs', 'id_auteur = ' . intval($id_auteur))) {
				spip_log('Il y a des abonnes : #' . $id_auteur, 'alertes');
				while ($aut = sql_fetch($auteur)) {
					//Evidemment, il faut l'email du membre.
					$email = $aut['email'];
					//On va quand même vérifier que l'article existe encore/est en etat publié
					include_spip('base/objets');
					$statut = objet_test_si_publie('article', $id_article);
					//Si accès restreint : vérifions que l'article n'est pas dans une zone limité pour l'auteur;
					if ($acces_restreint == true) {
						$article_accessible = false; //On repasse à false par défaut
						spip_log('Il y a acces restreint', 'alertes');
						//Pour chacunes des zones de l'auteur, on regarde si l'article n'est pas dans une des rubriques restreinte
						if ($restreint = accesrestreint_article_restreint($id_article, $id_auteur) and $restreint == false or empty($restreint)) {
							$article_accessible = true; //Dès qu'on a une zone accessible, on passe à true
							spip_log('Mais on est dans une zone accessible pour l\'article #' . $id_article,
								'alertes');
						}
					}
					if (!empty(trim($email)) && ($statut == true) && ($article_accessible == true)) {
						spip_log('On build le mail pour l\'article #' . $id_article, 'alertes');
						//On build le mail à partir de templates
						$header_email = recuperer_fond("alertes/header-email-alerte",
							array('id_article' => $id_article, 'id_auteur' => $id_auteur));
						$footer_email = recuperer_fond("alertes/footer-email-alerte",
							array('id_article' => $id_article, 'id_auteur' => $id_auteur));
						$corps_email = recuperer_fond("alertes/corps-email-alerte",
							array('id_article' => $id_article, 'id_auteur' => $id_auteur));
						$sujet = recuperer_fond("alertes/sujet-email-alerte", array(
							'id_article' => $id_article,
							'id_auteur' => $id_auteur,
						));  // Sujet du mail aussi en template (dangereux mais pratique si on veut le customiser). Doit renvoyer du texte brut
						//On n'envoie que si on a un contenu (présumé dans le corps du mail)
						if ($corps_email) {
							spip_log('On prepare l\'envoi', 'alertes');
							//Envoi email via Facteur
							$html = $header_email . $corps_email . $footer_email;
							include_spip('facteur_fonctions');
							$texte = facteur_mail_html2text($html); //Version  texte
							$corps = array(
								'html' => $html,
								'texte' => $texte,
							);
							if ($ok = $envoyer_mail($email, $sujet, $corps)) {
								//Email envoyé, on retire l'alerte-cron et on log.
								$del = sql_delete('spip_alertes_cron',
									'id_alerte_cron = ' . intval($res['id_alerte_cron']));
								spip_log('Email correctement envoyé a ' . $email, 'alertes');
							} else {
								//Email non envoyé. On log.
								spip_log('Echec de l\'envoi d\'email a ' . $email, 'alertes');
							}
						} else {
							spip_log('On n\'a pas de contenu pour l\'article #' . $id_article, 'alertes');
						}
					} else {
						spip_log('Pas d\'email pour l\'auteur #' . $id_auteur . ' ou article #' . $id_article . ' non publie ou inaccessible',
							'alertes');
						//Auteur sans email ou article non-publié/inexistant/restreint
						if ($statut != true) {
							//Article non publie, l'alerte n'a pas lieu d'être nulle part
							$del = sql_delete("spip_alertes_cron",
								"objet = 'article' AND id_objet = " . intval($id_article));
						}
					}
				}
			}
		}
	}

	return $time;
}

