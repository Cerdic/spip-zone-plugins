<?

	/**
	 * balise_FORMULAIRE_LETTRES
	 *
	 * @param p est un objet SPIP
	 * @return string url de validation de l'inscription
	 * @author Pierre Basson
	 **/
	function balise_FORMULAIRE_LETTRES ($p) {
		return calculer_balise_dynamique($p,'FORMULAIRE_LETTRES', array());
	}


	/**
	 * balise_FORMULAIRE_LETTRES_dyn
	 *
	 * Calcule la balise #FORMULAIRE_LETTRES
	 *
	 * @return array ou string : le tableau si on affiche le formulaire ou le code généré par lettres_message.html
	 * @author Pierre Basson
	 **/
	function balise_FORMULAIRE_LETTRES_dyn() {

		$code			= _request('code');
		$email			= _request('email');
		$lettres_action	= _request('lettres_action');
		$lettres		= _request('lettres'); // tableau : lettre[]=1&lettre[]=3...
		$format			= _request('format');
		$id_archive		= _request('id_archive');
		$url			= _request('url');
		$action_ok		= true;

		$valider		= _request('valider');

		if (($id_abonne = lettres_verifier_identification_couple_code_email($code, $email))) {
			// L'abonné vient de cliquer sur un lien d'un email qu'on lui a envoyé
			switch ($lettres_action) {
				case 'inscription':
					$resultat_action = lettres_valider_inscription_lettres($id_abonne, $lettres);
					break;
		
				case 'desinscription':
					$resultat_action = lettres_valider_desinscription_lettres($id_abonne, $lettres);
					break;
		
				case 'changement_format':
					$resultat_action = lettres_valider_changement_format($id_abonne, $format);
					break;
		
				case 'redirection':
					return lettres_valider_redirection($id_abonne, $id_archive, $url);
					break;
		
				default:
					$resultat_action = false;
					$action_inconnue = true;
					break;
			}

			return	inclure_balise_dynamique(
						array(
							'lettres_messages',
							0,
							array(
								'validation_email'	=> ' ',
								'resultat_action'	=> $resultat_action ? ' ' : '',
								'action_inconnue'	=> $action_inconnue ? ' ' : '',
								'envoi'				=> '',
								'resultat_envoi'	=> '',
								'email'				=> $email,
								'format'			=> $format,
								'lettres_action'	=> $lettres_action
							)
						),
						false
					);

		} else {
			// L'internaute accède au formulaire
		
			if (!empty($valider)) {
				// L'internaute a validé le formulaire
				$email_ok = lettres_verifier_validite_email($email);
				$lettres_ok	= !empty($lettres);
			
				if ($email_ok) {
					switch ($lettres_action) {
						case 'inscription':
							$action_ok = lettres_verifier_inscription_lettres($email, $lettres);
							break;
		
						case 'desinscription':
							$action_ok = lettres_verifier_desinscription_lettres($email, $lettres);
							break;
		
						case 'changement_format':
						default:
							$action_ok = true;
							$lettres_ok = true; // on a pas besoin de cette information
							break;
					}
				} else {
					$action_ok = false;
				}

				$validable = $email_ok
							&& $lettres_ok
							&& $action_ok;
			} else {
				// Visualisation simple du formulaire
				$email_ok = true;
				$validable = false;
			}
		
		
		
			if ($validable) {
				// On va : mettre à jour la BDD ; envoyer les mails pour confirmation des actions

				switch ($lettres_action) {
					case 'inscription':
						$code = lettres_calculer_code();
						list($existence, $id_abonne) = lettres_verifier_existence_abonne($email);
						if ($existence) {
							$modification_format = 'UPDATE spip_abonnes SET format="'.$format.'", maj=NOW() WHERE id_abonne="'.$id_abonne.'"';
							$resultat_modification_format = spip_query($modification_format);
						}
						if (!$existence) {
							$insertion_abonne = 'INSERT INTO spip_abonnes (email, code, format, maj) VALUES ("'.$email.'", "'.$code.'", "'.$format.'", NOW())';
							$resultat_insertion_abonne = spip_query($insertion_abonne);
							$id_abonne = spip_insert_id();
						}
						foreach ($lettres as $id_lettre) {
							if (lettres_verifier_existence_lettre($id_lettre)) {
								$insertion_abonnement = 'INSERT INTO spip_abonnes_lettres (id_abonne, id_lettre, date_inscription, statut) VALUES ("'.$id_abonne.'", "'.$id_lettre.'", NOW(), "a_valider")';
								$resultat_insertion_abonnement = spip_query($insertion_abonnement);
							}
						}
						break;
	
					case 'desinscription':
					case 'changement_format':
					default:
						break;
				}
			
				$id_abonne = lettres_recuperer_id_abonne_depuis_email($email);
				$lettres_virgule = implode(',', $lettres);
			
				switch ($lettres_action) {
					case 'inscription':
						$objet = _T('lettres:objet_inscription');
						$message_html	= inclure_balise_dynamique(array('formulaire_lettres_inscription_html', 0, array('lettres_virgule' => $lettres_virgule)), false);
						$message_texte	= inclure_balise_dynamique(array('formulaire_lettres_inscription_texte', 0, array('lettres_virgule' => $lettres_virgule)), false);
						$resultat_envoi = lettres_envoyer_email_confirmation($id_abonne, $objet, $message_html, $message_texte, $lettres);
						break;
	
					case 'desinscription':
						$objet = _T('lettres:objet_desinscription');
						$message_html	= inclure_balise_dynamique(array('formulaire_lettres_desinscription_html', 0, array('lettres_virgule' => $lettres_virgule)), false);
						$message_texte	= inclure_balise_dynamique(array('formulaire_lettres_desinscription_texte', 0, array('lettres_virgule' => $lettres_virgule)), false);
						$resultat_envoi = lettres_envoyer_email_confirmation($id_abonne, $objet, $message_html, $message_texte, $lettres);
						break;
	
					case 'changement_format':
						$objet = _T('lettres:objet_changement_format');
						$message_html	= inclure_balise_dynamique(array('formulaire_lettres_changement_format_html', 0, array()), false);
						$message_texte	= inclure_balise_dynamique(array('formulaire_lettres_changement_format_texte', 0, array()), false);
						$resultat_envoi = lettres_envoyer_email_confirmation($id_abonne, $objet, $message_html, $message_texte, $lettres, $format);
						break;
	
					default:
						break;
				}
			
				return 	inclure_balise_dynamique(
							array(
								'lettres_messages',
								0,
								array(
									'validation_email'	=> '',
									'resultat_action'	=> '',
									'envoi'				=> ' ',
									'resultat_envoi'	=> $resultat_envoi ? ' ' : '',
									'email'				=> $email,
									'format'			=> $format,
									'lettres_action'	=> $lettres_action
								)
							),
							false
						);
			
			} else {

				return	array(
							'formulaire_lettres', 
							0,
							array(
								'lettre'			=> $lettre,
								'email'				=> $email,
								'format'			=> $format,
								'lettres_action'	=> $lettres_action,
							
								'email_ko'			=> $email_ok ? '' : ' ',
								'lettres_ko'		=> $lettres_ok ? '' : ' ',
								'action_ko'			=> $action_ok ? '' : ' '
							)
						);
			
			}
		}
	}



?>