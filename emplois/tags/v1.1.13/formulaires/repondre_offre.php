<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2011                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION')) {
	return;
}

function formulaires_repondre_offre_charger_dist( $id_offre ) {

	$titre = sql_getfetsel('titre', 'spip_offres', "id_offre=".intval($id_offre ));
	$valeurs = array( 'id_offre' => $id_offre, 'titre' => $titre );
	return $valeurs;
}

function formulaires_repondre_offre_verifier_dist($id_offre ) {
	$erreurs = array();
	include_spip('inc/filtres');
	
	if (!$adres = _request('email_message_auteur'))
		$erreurs['email_message_auteur'] = _T("info_obligatoire");
	elseif(!email_valide($adres))
		$erreurs['email_message_auteur'] = _T('form_prop_indiquer_email');
	else {
		include_spip('inc/session');
		session_set('email', $adres);
	}

	if (!$sujet=_request('sujet_message_auteur'))
		$erreurs['sujet_message_auteur'] = _T("info_obligatoire");
	elseif(!(strlen($sujet)>3))
		$erreurs['sujet_message_auteur'] = _T('forum_attention_trois_caracteres');

	if (!$texte=_request('texte_message_auteur'))
		$erreurs['texte_message_auteur'] = _T("info_obligatoire");
	elseif(!(strlen($texte)>10))
		$erreurs['texte_message_auteur'] = _T('forum_attention_dix_caracteres');

	if (!_request('confirmer') AND !count($erreurs))
		$erreurs['previsu']=' ';
	return $erreurs;
}

function formulaires_repondre_offre_traiter_dist($id_offre ) {
	$envoyer_mail = charger_fonction('envoyer_mail','inc');

	/* étape 1 : envoi de l'email au déposant de l'offre */

	$res = sql_fetsel('nom, email,titre', 'spip_offres', "id_offre=" . sql_quote($id_offre));

	$email_to = $res['email'];
	$email_from = _request('email');
	$sujet = 'Contact Offre Emploi : '.$res['titre'];
	$message = _request('texte_message_auteur');

	$mail_deposant = $envoyer_mail($email_to,$sujet,$message,$email_from);

	/* étape 2 : envoi copie de l'email aux admins du site */

	$email_admins = 'nils.gouisset@techxv.org, marion.pelissie@techxv.org';
	$copie_admin = $envoyer_mail($email_admins,$sujet,$message,$email_from);
	

	/* étape 3 : message de retour au demandeur */
    
	if ($mail_deposant AND $copie_admin) {
		$message = _T('form_prop_message_envoye');
	}
	else
		$message = _T('pass_erreur_probleme_technique');

	return array('message_ok'=>$message);
}
