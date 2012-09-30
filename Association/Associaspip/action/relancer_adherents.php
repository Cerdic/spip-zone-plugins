<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010--2011 (v2) Emmanuel Saint-James & Jeannot Lapin
 *
 *  @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

function action_relancer_adherents() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$count = $securiser_action();
	$sujet = _request('sujet');
	$relance = association_recuperer_entier('relance');
	$message = html_entity_decode(_request('message'), ENT_QUOTES, 'UTF-8');
	$statut_tab = association_recuperer_liste('statut', true); // contient un tableau id_auteur => statut_interne
	$exp = $GLOBALS['association_metas']['nom'].'<'.$GLOBALS['association_metas']['email'].'>';
	include_spip ('inc/envoyer_mail'); //= $envoyer_mail = charger_fonction('envoyer_mail', 'inc');

	// on recupere les adresses emails de tous les auteurs selectionnes
	$emails_auteurs = association_formater_emails(array_keys($statut_tab), 'auteur', ''); // cette fonction renvoie un tableau auteur_id => array(emails)

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
		foreach ($emails as $email) { // envoi des mails a toutes les adresses connues...
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