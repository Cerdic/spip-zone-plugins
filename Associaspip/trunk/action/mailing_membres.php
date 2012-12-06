<?php
/***************************************************************************\
 *  Associaspip, extension de SPIP pour gestion d'associations
 *
 * @copyright Copyright (c) 2007 Bernard Blazin & Francois de Montlivault
 * @copyright Copyright (c) 2010--2011 (v2) Emmanuel Saint-James & Jeannot Lapin
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION'))
	return;

function action_mailing_membres() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
	$securiser_action();
	$sujet = _request('_sujet');
	$message = html_entity_decode(_request('_message'), ENT_QUOTES, 'UTF-8');
	$selectedId = _request('id');
	$relance = intval(_request('filtre_relance')); // si a 1, c'est un mail de relance
	$exp = $GLOBALS['association_metas']['nom'].'<'.$GLOBALS['association_metas']['email'].'>';

	// on recupere les adresses emails de tous les auteurs selectionnes
	include_spip('inc/association_coordonnees');
	include_spip ('inc/envoyer_mail');
	$emails_auteurs = association_recuperer_emails(array_keys($selectedId)); // cette fonction renvoie un tableau auteur_id => array(emails)

	// initialise les valeurs retournees
	$emails_envoyes_ok = 0;
	$emails_envoyes_echec = 0;
	$nb_membres_avec_email = 0;
	$membres_sans_email = array();
	$membres_ok = array(); // stocke l'id des membres pour qui ca a marche' pour changer leur statut si besoin
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
				$membres_ok[]=$id_auteur;
			}
		}
	}
	// si c'est une relance, on met a jour le statut de tous les membres relances 
	if ($relance==1) {
		$where = sql_in('id_auteur', $membres_ok);	
		sql_updateq('spip_asso_membres', array('statut_interne' => 'relance'), $where);
	}

	return array($emails_envoyes_ok, $emails_envoyes_echec, $nb_membres_avec_email, $membres_sans_email);
}

?>
