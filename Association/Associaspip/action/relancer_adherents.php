<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations             *
 *                                                                         *
 *  Copyright (c) 2007 Bernard Blazin & François de Montlivault (V1)       *
 *  Copyright (c) 2010-2011 Emmanuel Saint-James & Jeannot Lapin (V2)       *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/


if (!defined('_ECRIRE_INC_VERSION'))
	return;

// envoi du mail aux destinataires sélectionnés et chgt du statut de relance
function action_relancer_adherents()
{
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$count = $securiser_action();
	$sujet = _request('sujet');
	$relance = association_recuperer_entier('relance');
	$message = html_entity_decode(_request('message'), ENT_QUOTES, 'UTF-8');
	$statut_tab = (isset($_POST['statut'])) ? $_POST['statut']:array(); // contient un tableau id_auteur => statut_interne
	$exp = $GLOBALS['association_metas']['nom'].'<'.$GLOBALS['association_metas']['email'].'>';
	include_spip ('inc/envoyer_mail'); //= $envoyer_mail = charger_fonction('envoyer_mail', 'inc');

	// on recupere les adresses emails de tous les auteurs selectionnes
	include_spip('inc/association_coordonnees');
	$emails_auteurs = association_recuperer_emails(array_keys($statut_tab)); // cette fonction renvoie un tableau auteur_id => array(emails)

	// initialise les valeurs retournees
	$emails_envoyes_ok = 0;
	$emails_envoyes_echec = 0;
	$nb_membres_avec_email = 0;
	$membres_sans_email = array();
	// envoyer les messages
	foreach ($emails_auteurs as $id_auteur => $emails) {
		if (count($emails)) { // decompte des membres avec email
			$nb_membres_avec_email++;
		} else { // identification des membres sans email
			$membres_sans_email[] = $id_auteur;
		}
		foreach ($emails as $email) { // envoi des mails
			if (!inc_envoyer_mail_dist($email, $sujet, $message, $exp)) {
				$emails_envoyes_echec++;
				spip_log("non envoi du mail a $email",'associaspip');
			} else {
				$emails_envoyes_ok++;
				if ($relance && $statut_tab[$id_auteur]=='echu') { // dans le cas d'une relance on met a jour le statut interne (qui passe d'echu a relance) ; et dans le cas d'en simple publipostage (ou pour les membres non echus) le statut n'est pas modifie
					sql_updateq('spip_asso_membres',
						array('statut_interne' => 'relance'),
						"id_auteur=$id_auteur");
				}
			}
		}
	}

	return array($emails_envoyes_ok, $emails_envoyes_echec, $nb_membres_avec_email, $membres_sans_email);
}

?>