<?php


	/**
	 * Formulaire #FORMULAIRE_ECRIRE_AMI
	 *
	 * Copyright (c) 2006-2009
	 * Agence Artégo http://www.artego.fr
	 *  
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPLv3.
	 * Pour plus de details voir http://www.gnu.org/licenses/gpl-3.0.html
	 *  
	 **/


	function balise_FORMULAIRE_ECRIRE_AMI($p) {
		return calculer_balise_dynamique($p,'FORMULAIRE_ECRIRE_AMI', array('id_article', 'id_document'));
	}


	function balise_FORMULAIRE_ECRIRE_AMI_stat($args, $filtres) {
		// Pas d'id_article ni d'id_document ? Erreur de squelette
		if (!$args[0] AND !$args[1])
			return erreur_squelette(
				_T('zbug_champ_hors_motif',
					array ('champ' => '#FORMULAIRE_ECRIRE_AMI',
						'motif' => 'ARTICLES/DOCUMENTS')), '');
		return $args;
	}


	function balise_FORMULAIRE_ECRIRE_AMI_dyn($id_article, $id_document) {

		$votre_nom		= _request('votre_nom');
		$email_ami		= _request('email_ami');
		$commentaire	= _request('commentaire');

		$bouton_previsualiser	= _request('bouton_previsualiser');
		$bouton_confirmer		= _request('bouton_confirmer');

		if (!empty($bouton_previsualiser) OR !empty($bouton_confirmer)) {
			if (empty($votre_nom))	$votre_nom_ko = true;
			else					$votre_nom_ko = preg_match("/(%0A|%0D|\n+|\r+)(content-type:|to:|cc:|bcc:)/i", $votre_nom);
			if (preg_match("/(%0A|%0D|\n+|\r+)(content-type:|to:|cc:|bcc:)/i", $email_ami))	$email_ami_ko = true;
			else																			$email_ami_ko = !ereg("^[[:alnum:]]([-_.]?[[:alnum:]])*@[[:alnum:]]([-.]?[[:alnum:]])*\.([a-z]{2,4})$", $email_ami);
			
			$validable = !$votre_nom_ko
						 && !$email_ami_ko;
		} else {
			$validable = false;
		}
		
		if ($validable AND !empty($bouton_confirmer)) {

			$arguments = array(
								'id_article'	=> $id_article,
								'id_document'	=> $id_document,
								'votre_nom'		=> $votre_nom,
								'email_ami'		=> $email_ami,
								'commentaire'	=> $commentaire
								);
			$objet			= recuperer_fond('notifications/notification_ecrire_ami_titre', $arguments);
			$message_html	= recuperer_fond('notifications/notification_ecrire_ami_html', $arguments);
			$message_texte	= recuperer_fond('notifications/notification_ecrire_ami_texte', $arguments);

			$corps = array('html' => $message_html, 'texte' => $message_texte);

			$envoyer_mail = charger_fonction('envoyer_mail', 'inc');
			$envoyer_mail($email_ami, $objet, $corps);

			return
				array(
					'formulaires/formulaire_ecrire_ami', 
					0,
					array(
						'id_article'	=> $id_article,
						'id_document'	=> $id_document,

						'votre_nom'		=> $votre_nom,
						'email_ami'		=> $email_ami,
						'commentaire'	=> $commentaire,

						'message_envoye'	=> ' '
					)
				);
		} else {
			$message_envoye = false;
		}

		return
			array(
				'formulaires/formulaire_ecrire_ami', 
				0,
				array(
					'id_article'	=> $id_article,
					'id_document'	=> $id_document,

					'votre_nom'		=> $votre_nom,
					'email_ami'		=> $email_ami,
					'commentaire'	=> $commentaire,
				
					'votre_nom_ko'	=> $votre_nom_ko ? ' ' : '',
					'email_ami_ko'	=> $email_ami_ko ? ' ' : '',

					'message_envoye'	=> '',
					'validable'			=> $validable ? ' ' : ''
				)
			);
	}

?>