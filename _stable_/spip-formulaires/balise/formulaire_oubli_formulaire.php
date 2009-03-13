<?php


	/**
	 * SPIP-Formulaires
	 * 
	 * @copyright 2006-2007 Artégo
	 */


	include_spip('formulaires_fonctions');


	/**
	 * balise_FORMULAIRE_OUBLI_FORMULAIRE
	 *
	 * @param p est un objet SPIP
	 * @return string formulaire
	 * @author Pierre Basson
	 **/
	function balise_FORMULAIRE_OUBLI_FORMULAIRE($p) {
		return calculer_balise_dynamique($p,'FORMULAIRE_OUBLI_FORMULAIRE', array());
	}


	/**
	 * balise_FORMULAIRE_OUBLI_FORMULAIRE_dyn
	 *
	 * Calcule la balise #FORMULAIRE_OUBLI_FORMULAIRE
	 *
	 * @return formulaire
	 * @author Pierre Basson
	 **/
	function balise_FORMULAIRE_OUBLI_FORMULAIRE_dyn() {

		$bouton_valider = _request('bouton_valider');
		if (!empty($bouton_valider)) {
			$email = _request('email');
			if (preg_match("/(%0A|%0D|\n+|\r+)(content-type:|to:|cc:|bcc:)/i", $email)) {
				$email_inexistant = true;
			} else {
				if (!ereg(_REGEXP_EMAIL, $email)) {
					$email_inexistant = true;
				} else {
					$verification = spip_query('SELECT id_applicant FROM spip_applicants WHERE email="'.addslashes($email).'" AND mdp!=""');
					if (spip_num_rows($verification) == 1) {
						list($id_applicant) = spip_fetch_array($verification, SPIP_NUM);
						$email_inexistant = false;
					} else {
						$email_inexistant = true;
					}
				}
			}
			if (!$email_inexistant and $id_applicant) {
				if (isset($GLOBALS['meta']['spip_notifications_version'])) {
					$nouveau_mdp = strtolower(formulaires_generer_nouveau_mdp());
					include_spip('inc/spip-notifications');
					$objet = $GLOBALS['meta']['nom_site'].' - '._T('formulairespublic:nouveau_mot_de_passe');
					$message_html	= inclure_balise_dynamique(array('notifications/notification_oubli_formulaire_html', 0, array('nouveau_mdp' => $nouveau_mdp)), false);
					$message_texte	= inclure_balise_dynamique(array('notifications/notification_oubli_formulaire_texte', 0, array('nouveau_mdp' => $nouveau_mdp)), false);
					$notification	= new Notification($email, $objet, $message_html, $message_texte);
					if ($notification->Send()) {
						spip_query('UPDATE spip_applicants SET mdp="'.$nouveau_mdp.'" WHERE id_applicant="'.$id_applicant.'"');
						$succes = true;
					}
				}							
			}
		} else {
			$email_inexistant = false;
		}
		return	inclure_balise_dynamique(
					array(
						'formulaires/formulaire_oubli_formulaire',
						0,
						array(
							'email_inexistant'	=> $email_inexistant ? ' ' : '',
							'email'				=> $email,
							'succes'			=> $succes ? ' ' : ''
						)
					),
					false
				);
	}



?>