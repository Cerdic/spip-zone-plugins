<?php

	include_spip('inc/lettres_balises');
	include_spip('inc/lettres_boucles');
	include_spip('inc/lettres_filtres');

	/**
	 * lettres_ajouter_boutons
	 *
	 * Ajoute les boutons pour la lettre d'information dans l'espace privé
	 *
	 * @param array boutons_admin
	 * @return array boutons_admin le même tableau avec nos entrées en plus
	 * @author Pierre Basson
	 **/
	function lettres_ajouter_boutons($boutons_admin) {
		if ($GLOBALS['connect_statut'] == "0minirezo") {
			if (!lettres_verifier_existence_tables()) {
				$entree = new Bouton('../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/installation-plugin.png', _T('lettres:installation'));
				lettres_ajouter_bouton_avant($boutons_admin, 'forum', 'lettres_installation', $entree);
#				$boutons_admin['lettres']->sousmenu['lettres_installation']= new Bouton('../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/installation.gif', _T('lettres:installation'));
			} else {
				$entree = new Bouton('../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/lettre.png', _T('lettres:lettres_information'));
				lettres_ajouter_bouton_avant($boutons_admin, 'forum', 'lettres', $entree);
				$boutons_admin['lettres']->sousmenu['abonnes']= new Bouton('../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/abonnes.png', _T('lettres:abonnes'));
				$boutons_admin['lettres']->sousmenu['lettres']= new Bouton('../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/lettre-24.png', _T('lettres:lettres_information'));
				$boutons_admin['lettres']->sousmenu['lettres_configuration']= new Bouton('../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/configuration.png', _T('lettres:configuration'));
			}
		}
		return $boutons_admin;
	}


	/**
	 * lettres_ajouter_bouton_avant
	 *
	 * Ajoute une entrée dans un tableau avant une entrée particulière
	 *
	 * @param array tableau passé par référence
	 * @param string cle de l'entrée du tableau devant laquelle on veut insérer notre entrée
	 * @param string cle de notre entrée
	 * @param string valeur
	 * @author Christian Lefebvre
	 **/
	function lettres_ajouter_bouton_avant(&$t, $marque, $cle, $valeur) {
	    $pos = array_keys(array_keys($t), $marque);
	    if(count($pos) == 1) {
	        $pos = $pos[0];
	    } else {
	        $pos = count($t);
	    }
	    $t = array_merge(array_slice($t, 0, $pos), array($cle => $valeur), array_slice($t, $pos));
	}

	/**
	 * lettres_modifier_chemin_presentation_js
	 *
	 * Modifie le chemin du javascript presentation.js pour prendre celui du plugin et masquer le menu du plugin
	 *
	 * @param string texte
	 * @return string texte avec le chemin modifié
	 * @author Pierre Basson
	 **/
	function lettres_modifier_chemin_presentation_js($texte) { 
		$texte	= ereg_replace('img_pack/presentation.js', _DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/presentation.js', $texte);
		return $texte;
	}

	/**
	 * lettres_verifier_existence_tables
	 *
	 * @return boolean présence de la table spip_abonnes
	 * @author Pierre Basson
	 **/
	function lettres_verifier_existence_tables() {
		$requete_test_existence_tables = 'SHOW TABLES LIKE "spip_abonnes"';
		$resultat_existence = spip_query($requete_test_existence_tables);
		$nb_lignes = @spip_num_rows($resultat_existence);

		if ($nb_lignes > 0)
			return true;
		else
			return false;
	}


	/**
	 * lettres_recuperer_meta
	 *
	 * récupère la valeur d'une meta
	 *
	 * @param string nom meta
	 * @return string valeur de la meta
	 * @author Pierre Basson
	 **/
	function lettres_recuperer_meta($meta) {
		$verification = 'SELECT valeur FROM spip_meta WHERE nom="'.$meta.'" LIMIT 1';
		$resultat_verification = spip_query($verification);
		$nb_resultat = @spip_num_rows($resultat_verification);
		if (!$nb_resultat) {
			$valeur = '';
		} else {
			list($valeur) = spip_fetch_array($resultat_verification);
		}
		return $valeur;
	}


	/**
	 * lettres_verifier_action_possible
	 *
	 * Regarde si l'action demandée est possible
	 *
	 * @param int id_lettre
	 * @param string action
	 * @param string email
	 * @return boolean resultat
	 * @author Pierre Basson
	 **/
	function lettres_verifier_action_possible($id_lettre, $action, $email) {
		list($existence, $id_abonne) = lettres_verifier_existence_abonne($email);
		// si l'email n'est pas celui d'un abonné alors c'est bon
		if (!$existence) {
			return true;
		} else {
			// pour cet email, un abonné existe
			$requete_abonne_a_cette_lettre = 'SELECT id_abonne FROM spip_abonnes_lettres WHERE id_lettre="'.$id_lettre.'" AND id_abonne="'.$id_abonne.'"';
			$resultat_abonne_a_cette_lettre = spip_query($requete_abonne_a_cette_lettre);
			$abonne_a_cette_lettre = @spip_num_rows($resultat_abonne_a_cette_lettre);
			switch($action) {
				case 'inscription':
					if ($abonne_a_cette_lettre == 0)	$reponse = true;
					else								$reponse = false;
					break;
			
				case 'desinscription':
					if ($abonne_a_cette_lettre > 0)		$reponse = true;
					else								$reponse = false;
					break;
				
				default:
					$reponse = false;
					break;
			}
			return $reponse;
		}
	}


	/**
	 * lettres_valider_inscription_lettres
	 *
	 * Valide l'inscription de l'abonné
	 *
	 * @param int id_abonne
	 * @param array lettres
	 * @return boolean resultat de la validation
	 * @author Pierre Basson
	 **/
	function lettres_valider_inscription_lettres($id_abonne, $lettres) {
		if (empty($lettres)) {
			return false;
		} else {
			foreach ($lettres as $id_lettre) {
				$verification_validation = 'SELECT statut FROM spip_abonnes_lettres WHERE id_abonne="'.$id_abonne.'" AND id_lettre="'.$id_lettre.'"';
				$resultat_verification = spip_query($verification_validation);
				if (!$resultat_verification)
					return false;
				else
					list($statut) = spip_fetch_array($resultat_verification);
					
				if ($statut == 'a_valider') {
					$requete_maj = 'UPDATE spip_abonnes_lettres SET statut="valide" WHERE id_abonne="'.$id_abonne.'" AND id_lettre="'.$id_lettre.'" LIMIT 1';
					if (!spip_query($requete_maj))
						return false;
				}
			}
			return true;
		}
	}


	/**
	 * lettres_valider_desinscription_lettres
	 *
	 * Valide la désinscription de l'abonné
	 *
	 * @param int id_abonne
	 * @param array lettres
	 * @return boolean resultat de la désinscription
	 * @author Pierre Basson
	 **/
	function lettres_valider_desinscription_lettres($id_abonne, $lettres) {
		if (empty($lettres)) {
			return false;
		} else {
			$email = lettres_recuperer_email_depuis_id_abonne($id_abonne);
			$resultat = true;
			foreach ($lettres as $id_lettre) {
				if (lettres_verifier_action_possible($id_lettre, 'desinscription', $email)) {
					$requete_desinscription = 'DELETE FROM spip_abonnes_lettres WHERE id_abonne="'.$id_abonne.'" AND id_lettre="'.$id_lettre.'" LIMIT 1';
					$resultat_desinscription = spip_query($requete_desinscription);
					if (!$resultat_desinscription)
						$resultat = false;
				} else {
					$resultat = false;
				}
			}
			return $resultat;
		}
	}


	/**
	 * lettres_valider_changement_format
	 *
	 * Valide le changement de format
	 *
	 * @param int id_abonne
	 * @param string format
	 * @return boolean resultat du changement
	 * @author Pierre Basson
	 **/
	function lettres_valider_changement_format($id_abonne, $format) {
		if (!ereg('html|texte|mixte', $format)) {
			return false;
		} else {
			$requete_changement_format = 'UPDATE spip_abonnes SET format="'.$format.'" WHERE id_abonne="'.$id_abonne.'" LIMIT 1';
			return spip_query($requete_changement_format);
		}
	}
	

	/**
	 * lettres_valider_redirection
	 *
	 * Statistiques, pour savoir quels liens ont été cliqués
	 *
	 * @param int id_abonne
	 * @param int id_archive
	 * @param string url_redirection
	 * @return string redirection javascript vers l'url
	 * @author Pierre Basson
	 **/
	function lettres_valider_redirection($id_abonne, $id_archive, $url) {
		$url = str_replace('&amp;', '&', $url);
		$url = str_replace('&amp;', '&', $url);
		return '<script language="javascript" type="text/javascript">window.location.replace("'.$url.'");</script>';
	}
	

	/**
	 * lettres_verifier_validite_email
	 *
	 * Vérifie la validité de l'email
	 *
	 * @param string email
	 * @return boolean validité
	 * @author Pierre Basson, Yves Maistriaux
	 **/
	function lettres_verifier_validite_email($email) {
		return ereg("^[[:alnum:]]([-_.]?[[:alnum:]])*@[[:alnum:]]([-.]?[[:alnum:]])*\.([a-z]{2,4})$", $email);
	}
	

	/**
	 * lettres_calculer_code
	 *
	 * Génère un code unique
	 *
	 * @return string code
	 * @author Pierre Basson
	 **/
	function lettres_calculer_code() {
		return md5(uniqid(rand()));
	}
	
	/**
	 * lettres_verifier_existence_abonne
	 *
	 * Vérifie l'existence de l'abonné à partir de son email
	 *
	 * @param string email
	 * @return array
	 *				boolean existence
	 *				int id_abonne
	 * @author Pierre Basson
	 **/
	function lettres_verifier_existence_abonne($email) {
		$requete_existence_abonne = 'SELECT id_abonne FROM spip_abonnes WHERE email="'.$email.'"';
		$resultat_existence_abonne = spip_query($requete_existence_abonne);
		$nombre_enregistrement = @spip_num_rows($resultat_existence_abonne);
		if ($nombre_enregistrement > 0) {
			list($id_abonne) = spip_fetch_array($resultat_existence_abonne);
			return array(true, $id_abonne);
		} else {
			return array(false, 0);
		}
	}

	/**
	 * lettres_verifier_existence_lettre
	 *
	 * Vérifie l'existence de la lettre à laquelle on veut s'abonner
	 *
	 * @param int id_lettre
	 * @return boolean existence
	 * @author Pierre Basson
	 **/
	function lettres_verifier_existence_lettre($id_lettre) {
		return true;
	}
	
	
	/**
	 * lettres_recuperer_id_abonne_depuis_email
	 *
	 * @param string email
	 * @return int id_abonne
	 * @author Pierre Basson
	 **/
	function lettres_recuperer_id_abonne_depuis_email($email) {
		$requete_id_abonne = 'SELECT id_abonne FROM spip_abonnes WHERE email="'.$email.'"';
		list($id_abonne) = spip_fetch_array(spip_query($requete_id_abonne));

		return $id_abonne;
	}

	
	/**
	 * lettres_recuperer_email_depuis_id_abonne
	 *
	 * @param int id_abonne
	 * @return string email
	 * @author Pierre Basson
	 **/
	function lettres_recuperer_email_depuis_id_abonne($id_abonne) {
		$requete_email = 'SELECT email FROM spip_abonnes WHERE id_abonne="'.$id_abonne.'"';
		list($email) = spip_fetch_array(spip_query($requete_email));

		return $email;
	}


	/**
	 * lettres_verifier_identification_couple_code_email
	 *
	 * @param string code
	 * @param string email
	 * @return boolean resultat
	 *					0 si faux
	 *					id_abonne si vrai
	 * @author Pierre Basson
	 **/
	function lettres_verifier_identification_couple_code_email($code, $email) {
		if (empty($code) OR empty($email)) {
			return 0;
		} else {
			$requete_verification = 'SELECT id_abonne FROM spip_abonnes WHERE code="'.$code.'" AND email="'.$email.'" LIMIT 1';
			$resultat_verification = spip_query($requete_verification);
			$nombre_enregistrement = @spip_num_rows($resultat_verification);
			if ($nombre_enregistrement == 1) {
				list($id_abonne) = spip_fetch_array($resultat_verification);
				return $id_abonne;
			} else {
				return 0;
			}
		}
	}


	/**
	 * lettres_envoyer_email_confirmation
	 *
	 * Envoie un email texte, html ou mixte en fonction des préférences de l'abonné
	 * Remplace %%CODE%% et %%EMAIL%% dans le message_html et le message_texte par leurs valeurs
	 *
	 * @param int id_abonne pour qu'on puisse récupérer son email et ses préférences
	 * @param string objet de l'email
	 * @param string message_html version html de l'email
	 * @param string message_texte version texte de l'email
	 * @param array lettres
	 * @param string format_force pour forcer l'envoi de l'email dans un format
	 * @return boolean le résultat de l'envoi
	 * @author Pierre Basson, PHPcodeur
	 **/
	function lettres_envoyer_email_confirmation($id_abonne, $objet, $message_html, $message_texte, $lettres, $format_force='') {
		global $lang;
		if (empty($id_abonne)) return false;

		$charset			= lire_meta('charset');
		$email_webmaster	= lire_meta('email_webmaster');
		$nom_site			= lire_meta('nom_site');

		// Récupération des données de l'abonné
		$requete_donnees_abonne = 'SELECT email, code, format FROM spip_abonnes WHERE id_abonne="'.$id_abonne.'"';
		list($email, $code, $format) = spip_fetch_array(spip_query($requete_donnees_abonne));
		
		// Doit-on forcer le format ?
		if (!empty($format_force)) $format = $format_force;

		// Remplacement de %%CODE%% dans $message_html et $message_texte par $code
		$message_html	= ereg_replace("%%CODE%%", $code, $message_html);
		$message_texte	= ereg_replace("%%CODE%%", $code, $message_texte);
		// Remplacement de %%EMAIL%% dans $message_html et $message_texte par $email
		$message_html	= ereg_replace("%%EMAIL%%", $email, $message_html);
		$message_texte	= ereg_replace("%%EMAIL%%", $email, $message_texte);
		// Remplacement de %%LETTRES%% dans $message_html et $message_texte par $chaine_lettres
		$chaine_lettres = '&lang='.$lang;
		if (!empty($lettres)) {
			foreach ($lettres as $id_lettre)
				$chaine_lettres.= '&lettres[]='.$id_lettre;
		}
		$message_html	= ereg_replace("%%LETTRES%%", $chaine_lettres, $message_html);
		$message_texte	= ereg_replace("%%LETTRES%%", $chaine_lettres, $message_texte);
		// Remplacement de %%FORMAT%% dans $message_html et $message_texte par $format_force
		$message_html	= ereg_replace("%%FORMAT%%", $format_force, $message_html);
		$message_texte	= ereg_replace("%%FORMAT%%", $format_force, $message_texte);

		// Définition de l'entête du message
		$entete = 'From: "'._T('lettres:webmaster').' '.$nom_site.'" <'.$email_webmaster.'>'."\n";
		
		switch ($format) {
			case 'html':
				$entete.= 'Content-Type: text/html; charset="'.$charset.'"'."\n";
				$entete.= 'Content-Transfer-Encoding: 7bit'."\n\n";
				$message = $message_html;
				break;
				
			case 'texte':
				$entete.= 'Content-Type: text/plain; charset="'.$charset.'"'."\n";
				$entete.= 'Content-Transfer-Encoding: 7bit'."\n\n";
				$message = $message_texte;
				break;
				
			case 'mixte':
			default:
				$frontiere = "-----=" . md5( uniqid ( rand() ) );
				$entete.= 'MIME-Version: 1.0'."\n";
				$entete.= 'Content-Type: multipart/alternative; boundary="'.$frontiere.'"';
				$message = 'This is a multi-part message in MIME format.'."\n\n";
				$message.= '--'.$frontiere."\n";
				$message.= 'Content-Type: text/plain; charset="'.$charset.'"'."\n";
				$message.= 'Content-Transfer-Encoding: 7bit'."\n\n";
				$message.= $message_texte;
				$message.= "\n\n";
				$message.= '--'.$frontiere."\n";
				$message.= 'Content-Type: text/html; charset="'.$charset.'"'."\n";
				$message.= 'Content-Transfer-Encoding: 7bit'."\n\n";
				$message.= $message_html;
				$message.= "\n\n";
				$message.= '--'.$frontiere."--\n";
				break;
		}

		// Envoi du mail
		return mail($email, $objet, $message, $entete);

	}


	/**
	 * lettres_envoyer_lettre
	 *
	 * Envoie un email texte, html ou mixte en fonction des préférences de l'abonné
	 * Remplace %%CODE%% et %%EMAIL%% dans le message_html et le message_texte par leurs valeurs
	 *
	 * @param int id_abonne pour qu'on puisse récupérer son email et ses préférences
	 * @param string objet de l'email
	 * @param string message_html version html de l'email
	 * @param string message_texte version texte de l'email
	 * @param int id_lettre
	 * @return boolean le résultat de l'envoi
	 * @author Pierre Basson, PHPcodeur
	 **/
	function lettres_envoyer_lettre($id_abonne, $objet, $message_html, $message_texte, $id_lettre) {
		global $lang;
		if (empty($id_abonne)) return false;

		$charset			= lire_meta('charset');
		$email_webmaster	= lire_meta('email_webmaster');
		$nom_site			= lire_meta('nom_site');
		$url_site			= lire_meta('adresse_site');

		$requete_auteurs = 'SELECT A.email
							FROM spip_auteurs AS A
							INNER JOIN spip_auteurs_lettres AS AL ON AL.id_auteur=A.id_auteur
							WHERE AL.id_lettre="'.$id_lettre.'"';
		$resultat_auteurs = spip_query($requete_auteurs);
		if (@spip_num_rows($resultat_auteurs) > 0) {
			$auteurs = array();
			while ($arr = spip_fetch_array($resultat_auteurs))
				$auteurs[] = $arr['email'];
			$liste_auteurs = implode(', ', $auteurs);
		} else {
			$liste_auteurs = $email_webmaster;
		}

		// Récupération des données de l'abonné
		$requete_donnees_abonne = 'SELECT email, code, format FROM spip_abonnes WHERE id_abonne="'.$id_abonne.'"';
		list($email, $code, $format) = spip_fetch_array(spip_query($requete_donnees_abonne));
		
		$message_html	= ereg_replace('../IMG', "$url_site/IMG", $message_html);
		// Remplacement de %%CODE%% dans $message_html et $message_texte par $code
		$message_html	= ereg_replace("%%CODE%%", $code, $message_html);
		$message_texte	= ereg_replace("%%CODE%%", $code, $message_texte);
		// Remplacement de %%EMAIL%% dans $message_html et $message_texte par $email
		$message_html	= ereg_replace("%%EMAIL%%", $email, $message_html);
		$message_texte	= ereg_replace("%%EMAIL%%", $email, $message_texte);
		// Remplacement de %%LETTRES%% dans $message_html et $message_texte par $chaine_lettres
		$chaine_lettres = '&lettres[]='.$id_lettre.'&id_archive='.$id_archive.'&lang='.$lang;
		$message_html	= ereg_replace("%%LETTRES%%", $chaine_lettres, $message_html);
		$message_texte	= ereg_replace("%%LETTRES%%", $chaine_lettres, $message_texte);

		// Définition de l'entête du message
		$entete = 'From: "'._T('lettres:webmaster').' '.$nom_site.'" <'.$email_webmaster.'>'."\n";
		$entete.= 'Reply-To: '.$liste_auteurs."\n";
		
		switch ($format) {
			case 'html':
				$entete.= 'Content-Type: text/html; charset="'.$charset.'"'."\n";
				$entete.= 'Content-Transfer-Encoding: 7bit'."\n\n";
				$message = $message_html;
				break;
				
			case 'texte':
				$entete.= 'Content-Type: text/plain; charset="'.$charset.'"'."\n";
				$entete.= 'Content-Transfer-Encoding: 7bit'."\n\n";
				$message = $message_texte;
				break;
				
			case 'mixte':
			default:
				$frontiere = "-----=" . md5( uniqid ( rand() ) );
				$entete.= 'MIME-Version: 1.0'."\n";
				$entete.= 'Content-Type: multipart/alternative; boundary="'.$frontiere.'"';
				$message = 'This is a multi-part message in MIME format.'."\n\n";
				$message.= '--'.$frontiere."\n";
				$message.= 'Content-Type: text/plain; charset="'.$charset.'"'."\n";
				$message.= 'Content-Transfer-Encoding: 7bit'."\n\n";
				$message.= $message_texte;
				$message.= "\n\n";
				$message.= '--'.$frontiere."\n";
				$message.= 'Content-Type: text/html; charset="'.$charset.'"'."\n";
				$message.= 'Content-Transfer-Encoding: 7bit'."\n\n";
				$message.= $message_html;
				$message.= "\n\n";
				$message.= '--'.$frontiere."--\n";
				break;
		}

		// Envoi du mail
		return mail($email, $objet, $message, $entete);

	}


	/**
	 * lettres_envoyer_test
	 *
	 * Envoie un email mixte de test
	 *
	 * @param string email
	 * @param string objet de l'email
	 * @param string message_html version html de l'email
	 * @param string message_texte version texte de l'email
	 * @param int id_lettre
	 * @return boolean le résultat de l'envoi
	 * @author Pierre Basson, PHPcodeur
	 **/
	function lettres_envoyer_test($email, $objet, $message_html, $message_texte, $id_lettre) {
		if (!lettres_verifier_validite_email($email)) return false;

		$charset			= lire_meta('charset');
		$email_webmaster	= lire_meta('email_webmaster');
		$nom_site			= lire_meta('nom_site');
		$url_site			= lire_meta('adresse_site');

		$message_html	= ereg_replace('../IMG', "$url_site/IMG", $message_html);

		$requete_auteurs = 'SELECT A.email
							FROM spip_auteurs AS A
							INNER JOIN spip_auteurs_lettres AS AL ON AL.id_auteur=A.id_auteur
							WHERE AL.id_lettre="'.$id_lettre.'"';
		$resultat_auteurs = spip_query($requete_auteurs);
		if (@spip_num_rows($resultat_auteurs) > 0) {
			$auteurs = array();
			while ($arr = spip_fetch_array($resultat_auteurs))
				$auteurs[] = $arr['email'];
			$liste_auteurs = implode(', ', $auteurs);
		} else {
			$liste_auteurs = $email_webmaster;
		}

		$frontiere = "-----=" . md5( uniqid ( rand() ) );

		$entete = 'From: "'._T('lettres:webmaster').' '.$nom_site.'" <'.$email_webmaster.'>'."\n";
		$entete.= 'Reply-To: '.$liste_auteurs."\n";
		$entete.= 'MIME-Version: 1.0'."\n";
		$entete.= 'Content-Type: multipart/alternative; boundary="'.$frontiere.'"';
		
		$message = 'This is a multi-part message in MIME format.'."\n\n";
		$message.= '--'.$frontiere."\n";
		$message.= 'Content-Type: text/plain; charset="'.$charset.'"'."\n";
		$message.= 'Content-Transfer-Encoding: 7bit'."\n\n";
		$message.= $message_texte;
		$message.= "\n\n";
		$message.= '--'.$frontiere."\n";
		$message.= 'Content-Type: text/html; charset="'.$charset.'"'."\n";
		$message.= 'Content-Transfer-Encoding: 7bit'."\n\n";
		$message.= $message_html;
		$message.= "\n\n";
		$message.= '--'.$frontiere."--\n";

		// Envoi du mail
		return mail($email, $objet, $message, $entete);

	}


	/**
	 * lettres_verifier_inscription_lettres
	 *
	 * Vérifie que l'email ne figure pas déjà en base associé à une ou plusieurs lettres
	 * Retourne faux tant que l'email est associé à une lettre
	 *
	 * @param string email
	 * @param array lettres pour lesquelles il faut vérifier que l'email n'est pas déjà associé
	 * @return boolean résultat
	 * @author Pierre Basson
	 **/
	function lettres_verifier_inscription_lettres($email, $lettres) {
		list($existence, $id_abonne) = lettres_verifier_existence_abonne($email);
		// si l'email n'est pas celui d'un abonné alors c'est bon
		if (!$existence) {
			return true;
		} else {
			foreach ($lettres as $id_lettre) {
				if (!lettres_verifier_action_possible($id_lettre, 'inscription', $email))
					return false;
			}
			return true;
		}
	}


	/**
	 * lettres_verifier_desinscription_lettres
	 *
	 * Vérifie que l'email figure en base associé à une ou plusieurs lettres
	 * Retourne faux si l'email n'est pas associé à toutes les lettres passées en argument
	 *
	 * @param string email
	 * @param array lettres pour lesquelles il faut vérifier que l'email est associé
	 * @return boolean résultat
	 * @author Pierre Basson
	 **/
	function lettres_verifier_desinscription_lettres($email, $lettres) {
		if (empty($lettres))
			return false;
		list($existence, $id_abonne) = lettres_verifier_existence_abonne($email);
		// si l'email n'est pas celui d'un abonné alors c'est bon
		if (!$existence) {
			return false;
		} else {
			foreach ($lettres as $id_lettre) {
				if (!lettres_verifier_action_possible($id_lettre, 'desinscription', $email))
					return false;
			}
			return true;
		}
	}


	/**
	 * lettres_verifier_changement_format
	 *
	 * Change les préférences de l'abonné
	 * Retourne faux si l'email n'est pas associé à toutes les lettres passées en argument
	 *
	 * @param string email
	 * @param array lettres pour lesquelles il faut vérifier que l'email est associé
	 * @return boolean résultat
	 * @author Pierre Basson
	 **/
	function lettres_verifier_changement_format($email) {

		return array(false, '');

	}


	/**
	 * lettres_calculer_URL_VALIDATION
	 *
	 * @param string action
	 * @return string url
	 * @author Pierre Basson
	 **/
	function lettres_calculer_URL_VALIDATION($action) {
		if ($action == 'changement_format')
			$format = '&format=%%FORMAT%%';
		else
			$format = '';
		return lire_meta('adresse_site').'/spip.php?page='.lire_meta('fond_formulaire_lettre').'&lettres_action='.$action.'%%LETTRES%%&code=%%CODE%%&email=%%EMAIL%%'.$format;
	}



?>