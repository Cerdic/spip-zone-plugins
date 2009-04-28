<?php


	/**
	 * SPIP-Formulaires
	 *
	 * Copyright (c) 2006-2009
	 * Agence Artégo http://www.artego.fr
	 *  
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPLv3.
	 * Pour plus de details voir http://www.gnu.org/licenses/gpl-3.0.html
	 *  
	 **/


	include_spip('formulaires_fonctions');


	function balise_FORMULAIRE_OUBLI_FORMULAIRE($p) {
		return calculer_balise_dynamique($p,'FORMULAIRE_OUBLI_FORMULAIRE', array());
	}


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
					$verification = sql_select('id_applicant', 'spip_applicants', 'email="'.addslashes($email).'" AND mdp!=""');
					if (sql_count($verification) == 1) {
						$t = sql_fetch($verification);
						$id_applicant = $t['id_applicant'];
						$email_inexistant = false;
					} else {
						$email_inexistant = true;
					}
				}
			}
			if (!$email_inexistant and $id_applicant) {
				include_spip('public/assembler');
				$nouveau_mdp = strtolower(formulaires_generer_nouveau_mdp());
				$objet			= recuperer_fond('notifications/notification_oubli_formulaire_titre', array('nouveau_mdp' => $nouveau_mdp, 'lang' => $lang));
				$message_html	= recuperer_fond('notifications/notification_oubli_formulaire_html', array('nouveau_mdp' => $nouveau_mdp, 'lang' => $lang));
				$message_texte	= recuperer_fond('notifications/notification_oubli_formulaire_texte', array('nouveau_mdp' => $nouveau_mdp, 'lang' => $lang));
				$corps = array('html' => $message_html, 'texte' => $message_texte);
				$envoyer_mail = charger_fonction('envoyer_mail', 'inc');
				if ($envoyer_mail($email, $objet, $corps)) {
					sql_updateq('spip_applicants', array('mdp' => $nouveau_mdp), 'id_applicant='.intval($id_applicant));
					$succes = true;
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