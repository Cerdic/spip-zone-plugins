<?php
/**
 * Formulaire d'inscription rapide.
 * Ne demande que l'adresse mail.
 * Envoie une confirmation mail en retour, avec un lien pour
 * revenir sélectionner les listes désirées.
 *
 * Prévu pour être incrusté dans le menu rubriques
 * (par défaut: 140px de large).
 *
 * Voir inscrivez_vous.html à la racine du plugin
 * pour exemple :
 * ex.:  http://<foo.bar>/?page=inscrivez_vous
 *
 * @licence GNU/GPL
 * 
 * @since SPIP 2.0
 * @author CP
 * @version CP-20110822
 * 
 * @see http://www.spip.net/fr_article3800.html (les formulaires CVT)
 * @see http://www.spip.net/fr_article3796.html (CVT par l'exemple)
 * @package spiplistes
 */
 // $LastChangedRevision$
 // $LastChangedBy$
 // $LastChangedDate$
 
if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/spiplistes_api');
include_spip('inc/spiplistes_api_courrier');

/**
 * Proposer le formulaire d'inscription si l'adresse
 * mail du connecté est inconnue
 */
function formulaires_inscrivez_vous_charger_dist ()
{
	$valeurs['accepter_visiteurs'] = $GLOBALS['meta']['accepter_visiteurs'];
	$valeurs['session_email'] = sinon($GLOBALS['visiteur_session']['session_email'],
			$GLOBALS['visiteur_session']['email']);
	
	return ($valeurs);
}

/**
 * Vérification des données transmises via le formulaire.
 * (qui ne demande qu'une adresse mail).
 *
 * Si l'adresse mail existe déjà, prévenir.
 * Sinon, créer adresse mail et envoyer un message de
 * demande de confirmation.
 */
function formulaires_inscrivez_vous_verifier_dist($mode = NULL, $focus = NULL, $id = 0) {

	$erreurs = array();
	include_spip('inc/filtres');	
	if (!tester_config($id, $mode) || (strlen(_request('nobot'))>0))
	{
		$erreurs['message_erreur'] = _T('pass_rien_a_faire_ici');
	}

	if (!count($erreurs)) {
		
		$email = _request('mail_inscription');
		
		if ($email = email_valide($email))
		{
			$sql_select = array('statut', 'pass');
			$sql_where = 'email=' . sql_quote($email);
			
			if ($row = spiplistes_auteurs_auteur_select ($sql_select, $sql_where))
			{
				if (($row['statut'] == '5poubelle') && !$row['pass'])
				{
					// irrecuperable
					// Pourquoi ? C'est ainsi dans le form inscription !
					$erreurs['message_erreur'] = _T('form_forum_access_refuse');	
				}
				elseif (($row['statut'] != 'nouveau') && !$row['pass'])
				{
					// deja inscrit
					$erreurs['message_erreur'] = _T('spiplistes:email_deja_enregistre');
				}
			}
		}
		else
		{
			$erreurs['message_erreur'] = _T('spiplistes:email_incorrect');
		}
	}
	return ($erreurs);
}

/**
 * Traiter les données du formulaire
 * - Créer le compte si besoin
 * - Envoyer un mail de confirmation avec lien cookie
 */
function formulaires_inscrivez_vous_traiter_dist ($mode = NULL, $focus = NULL, $id = 0)
{
	$auteur = FALSE;
	$email = _request('mail_inscription');
	
	$sql_select = array('id_auteur','nom','email','login','statut','lang');
	$sql_where = 'email=' . sql_quote($email);
	
	/**
	 * Si l'abonné existe déjà, aller chercher son format
	 * de réception et les listes auxquelles il est abonné
	 */
	if ($auteur = spiplistes_auteurs_auteur_select ($sql_select, $sql_where))
	{
		$auteur['format'] = spiplistes_format_abo_demande ($auteur['id_auteur']);
		$auteur['ids_abos'] = spiplistes_abonnements_listes_auteur ($auteur['id_auteur']);
		$nouvel_inscription = 'non';
		if ($auteur['statut'] == '5poubelle')
		{
			// si le pass n'a pas été supprimé, peut être réactivé
			// si pass a été supprimé, c'est détecté dans *_verifier ci-dessus
			spiplistes_auteurs_auteur_statut_modifier ($auteur['id_auteur'], '6forum');
		}
	}
	/**
	 * Sinon, le créer (en créant le format de réception par défaut)
	 */
	else
	{
		if ($auteur = spiplistes_auteurs_create_from_mail ($email))
		{
			$contexte['message_ok'] = _T('enregistrement_ok');
			$nouvel_inscription = 'oui';
		}
		else
		{
			$contexte['message_erreur'] = _T('erreur_enregistrement');
		}
	}
	
	/**
	 * Envoyer le mail de confirmation
	 */
	if ($auteur)
	{
		if (!$auteur['lang']) {	$auteur['lang'] = $GLOBALS['spip_lang']; }
		$auteur['format'] = spiplistes_format_valide ($auteur['format']);
		/**
		 * Préparer un nouveau cookie pour le lien de retour
		 * présenté dans le mail de rappel.
		 */
		$auteur['cookie_oubli'] = creer_uniqid();
		spiplistes_auteurs_cookie_oubli_updateq (
												$auteur['cookie_oubli'],
												$auteur['email']
												);
		
		$nom_site_spip = spiplistes_nom_site_texte ($auteur['lang']);
		$objet_email = _T('spiplistes:confirmation_inscription');
		$contexte = array(
			'titre' => $objet_email,
			'nouvel_inscription' => $nouvel_inscription
			);
		
		list ($message_html, $message_texte) = spiplistes_preparer_message(
					($objet_email = "[$nom_site_spip] " . $objet_email)
					, spiplistes_patron_message()
					, array_merge($contexte, $auteur)
					);
		if(
			spiplistes_envoyer_mail (
				$auteur['email'],
				$objet_email,
				array ('html' => $message_html, 'texte' => $message_texte),
				FALSE,
				'',
				$auteur['format']
			)
		) {
			spiplistes_debug_log('SEND MAIL @ '.$auteur['email']);
			$contexte['message_ok'] = _T('spiplistes:demande_enregistree_retour_mail');
		}
		else
		{
			spiplistes_debug_log(' erreur mail @ '.$auteur['email']);
			$contexte['message_erreur'] = _T('pass_erreur_probleme_technique');
		}
	}

	return ($contexte);
}

