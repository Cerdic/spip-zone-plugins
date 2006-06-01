<?php

	include_spip('base/create');
	include_spip('base/abstract_sql');
	include_spip('base/lettres');
	include_spip('inc/plugin');
	include_spip('inc/lettres_balises');
	include_spip('inc/lettres_filtres');


	/**
	 * lettres_ajouter_boutons
	 *
	 * Ajoute les boutons pour la lettre d'information dans l'espace priv�
	 *
	 * @param array boutons_admin
	 * @return array boutons_admin le m�me tableau avec des entr�es en plus
	 * @author Pierre Basson
	 **/
	function lettres_ajouter_boutons($boutons_admin) {
		if ($GLOBALS['connect_statut'] == "0minirezo") {
			$entree = new Bouton('../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/lettre.png', _T('lettres:lettres_information'));
			lettres_ajouter_bouton_avant($boutons_admin, 'forum', 'lettres', $entree);
			$boutons_admin['lettres']->sousmenu['abonnes']= new Bouton('../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/abonnes.png', _T('lettres:abonnes'));
			$boutons_admin['lettres']->sousmenu['lettres']= new Bouton('../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/lettre-24.png', _T('lettres:lettres_information'));
			$boutons_admin['lettres']->sousmenu['lettres_statistiques']= new Bouton('../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/statistiques.png', _T('lettres:statistiques'));
			$boutons_admin['lettres']->sousmenu['lettres_configuration']= new Bouton('../'._DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/configuration.png', _T('lettres:configuration'));
		}
		return $boutons_admin;
	}


	/**
	 * lettres_ajouter_bouton_avant
	 *
	 * Ajoute une entr�e dans un tableau avant une entr�e particuli�re
	 *
	 * @param array tableau pass� par r�f�rence
	 * @param string cle de l'entr�e du tableau devant laquelle on veut ins�rer notre entr�e
	 * @param string cle de notre entr�e
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
	 * lettres_header_prive
	 *
	 * Modifie le chemin du javascript presentation.js pour prendre celui du plugin et masquer le menu du plugin
	 *
	 * @param string texte
	 * @return string texte avec le chemin modifi�
	 * @author Pierre Basson
	 **/
	function lettres_header_prive($texte) { 
		$texte	= ereg_replace('img_pack/presentation.js', _DIR_PLUGIN_LETTRE_INFORMATION.'/img_pack/presentation.js', $texte);
		return $texte;
	}


	/**
	 * lettres_taches_generales_cron
	 *
	 * Ajout des t�ches planifi�es pour le plugin
	 *
	 * @param array taches_generales
	 * @return true
	 * @author Pierre Basson
	 **/
	function lettres_taches_generales_cron($taches_generales) {
		$taches_generales['envoi_lettres'] = 60 * 10;
		return $taches_generales;
	}

	/**
	 * cron_envoi_lettres
	 *
	 * T�che de fond pour l'envoi planifi�
	 *
	 * @param array taches_generales
	 * @return true
	 * @author Pierre Basson
	 **/
	function cron_envoi_lettres($t) {

		# ici le code pour lancer les envois

		# return (0 - $t); # si pas termin�
		return true;
	}


	/**
	 * lettres_exec_init
	 *
	 * @param flux
	 * @return flux
	 * @author Pierre Basson
	 **/
	function lettres_exec_init($flux) {
		return $flux;
	}


	/**
	 * lettres_verifier_action_possible
	 *
	 * Regarde si l'action demand�e est possible
	 *
	 * @param int id_lettre
	 * @param string action
	 * @param string email
	 * @return boolean resultat
	 * @author Pierre Basson
	 **/
	function lettres_verifier_action_possible($id_lettre, $action, $email) {
		list($existence, $id_abonne) = lettres_verifier_existence_abonne($email);
		// si l'email n'est pas celui d'un abonn� alors c'est bon
		if (!$existence) {
			return true;
		} else {
			// pour cet email, un abonn� existe
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
	 * Valide l'inscription de l'abonn�
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
					spip_query('INSERT INTO spip_lettres_statistiques (id_lettre, date, type) VALUES ("'.$id_lettre.'", NOW(), "inscription")');
				}
			}
			return true;
		}
	}


	/**
	 * lettres_valider_desinscription_lettres
	 *
	 * Valide la d�sinscription de l'abonn�
	 *
	 * @param int id_abonne
	 * @param array lettres
	 * @return boolean resultat de la d�sinscription
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
					spip_query('INSERT INTO spip_lettres_statistiques (id_lettre, date, type) VALUES ("'.$id_lettre.'", NOW(), "desinscription")');
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
	 * Statistiques, pour savoir quels liens ont �t� cliqu�s
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
		spip_query('INSERT INTO spip_archives_statistiques (id_archive, url) VALUES ("'.$id_archive.'", "'.addslashes($url).'")');
		return '<script language="javascript" type="text/javascript">window.location.replace("'.$url.'");</script>';
	}
	

	/**
	 * lettres_verifier_validite_email
	 *
	 * V�rifie la validit� de l'email
	 *
	 * @param string email
	 * @return boolean validit�
	 * @author Pierre Basson, Yves Maistriaux
	 **/
	function lettres_verifier_validite_email($email) {
		return ereg("^[[:alnum:]]([-_.]?[[:alnum:]])*@[[:alnum:]]([-.]?[[:alnum:]])*\.([a-z]{2,4})$", $email);
	}
	

	/**
	 * lettres_calculer_code
	 *
	 * G�n�re un code unique
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
	 * V�rifie l'existence de l'abonn� � partir de son email
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
	 * V�rifie l'existence de la lettre � laquelle on veut s'abonner
	 *
	 * @param int id_lettre
	 * @return boolean existence
	 * @author Pierre Basson
	 **/
	function lettres_verifier_existence_lettre($id_lettre) {
		$resultat = spip_query('SELECT id_lettre FROM spip_lettres WHERE id_lettre="'.$id_lettre.'"');
		if (@spip_num_rows($resultat) > 0)
			return true;
		else
			return false;
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
	 * Envoie un email texte, html ou mixte en fonction des pr�f�rences de l'abonn�
	 * Remplace %%CODE%% et %%EMAIL%% dans le message_html et le message_texte par leurs valeurs
	 *
	 * @param int id_abonne pour qu'on puisse r�cup�rer son email et ses pr�f�rences
	 * @param string objet de l'email
	 * @param string message_html version html de l'email
	 * @param string message_texte version texte de l'email
	 * @param array lettres
	 * @param string format_force pour forcer l'envoi de l'email dans un format
	 * @return boolean le r�sultat de l'envoi
	 * @author Pierre Basson, PHPcodeur
	 **/
	function lettres_envoyer_email_confirmation($id_abonne, $objet, $message_html, $message_texte, $lettres, $format_force='') {
		global $lang;
		if (empty($id_abonne)) return false;

		$charset			= lire_meta('charset');
		$email_webmaster	= lire_meta('email_webmaster');
		$nom_site			= lire_meta('nom_site');

		// R�cup�ration des donn�es de l'abonn�
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

		// D�finition de l'ent�te du message
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
	 * Envoie un email texte, html ou mixte en fonction des pr�f�rences de l'abonn�
	 * Remplace %%CODE%% et %%EMAIL%% dans le message_html et le message_texte par leurs valeurs
	 *
	 * @param int id_abonne pour qu'on puisse r�cup�rer son email et ses pr�f�rences
	 * @param string objet de l'email
	 * @param string message_html version html de l'email
	 * @param string message_texte version texte de l'email
	 * @param int id_lettre
	 * @param int id_archive
	 * @return boolean le r�sultat de l'envoi
	 * @author Pierre Basson, PHPcodeur
	 **/
	function lettres_envoyer_lettre($id_abonne, $objet, $message_html, $message_texte, $id_lettre, $id_archive) {
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

		// R�cup�ration des donn�es de l'abonn�
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

		// D�finition de l'ent�te du message
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
	 * @return boolean le r�sultat de l'envoi
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
	 * V�rifie que l'email ne figure pas d�j� en base associ� � une ou plusieurs lettres
	 * Retourne faux tant que l'email est associ� � une lettre
	 *
	 * @param string email
	 * @param array lettres pour lesquelles il faut v�rifier que l'email n'est pas d�j� associ�
	 * @return boolean r�sultat
	 * @author Pierre Basson
	 **/
	function lettres_verifier_inscription_lettres($email, $lettres) {
		list($existence, $id_abonne) = lettres_verifier_existence_abonne($email);
		// si l'email n'est pas celui d'un abonn� alors c'est bon
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
	 * V�rifie que l'email figure en base associ� � une ou plusieurs lettres
	 * Retourne faux si l'email n'est pas associ� � toutes les lettres pass�es en argument
	 *
	 * @param string email
	 * @param array lettres pour lesquelles il faut v�rifier que l'email est associ�
	 * @return boolean r�sultat
	 * @author Pierre Basson
	 **/
	function lettres_verifier_desinscription_lettres($email, $lettres) {
		if (empty($lettres))
			return false;
		list($existence, $id_abonne) = lettres_verifier_existence_abonne($email);
		// si l'email n'est pas celui d'un abonn� alors c'est bon
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
	 * Change les pr�f�rences de l'abonn�
	 * Retourne faux si l'email n'est pas associ� � toutes les lettres pass�es en argument
	 *
	 * @param string email
	 * @param array lettres pour lesquelles il faut v�rifier que l'email est associ�
	 * @return boolean r�sultat
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