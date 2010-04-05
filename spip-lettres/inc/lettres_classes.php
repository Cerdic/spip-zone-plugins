<?php


	/**
	 * SPIP-Lettres
	 *
	 * Copyright (c) 2006-2009
	 * Agence Artégo http://www.artego.fr
	 *  
	 * Ce programme est un logiciel libre distribue sous licence GNU/GPLv3.
	 * Pour plus de details voir http://www.gnu.org/licenses/gpl-3.0.html
	 *  
	 **/


	include_spip('lettres_fonctions');
	include_spip('public/assembler');
	include_spip('inc/distant');
	include_spip('inc/rubriques');
	include_spip('base/lettres');

	/**
	 * abonne - classe pour la gestion des abonnes
	 *
	 * @copyright 2006-2009 Artégo
	 */

	class abonne {

	    var $id_abonne;
		var $objet = 'abonnes';
	    var $id_objet;
		var $email;
		var $code;
		var $nom;
		var $format = 'mixte';
		var $extra;

		var $existe = false;


		/**
		 * abonne : constructeur
		 *
		 * @param int id_abonne
		 * @param string email
		 * @return void
		 **/
		function abonne($id_abonne=0, $email='') {
			global $table_des_abonnes;
			if ($id_abonne != 0) {
				$this->id_abonne = intval($id_abonne);
				$spip_abonne = sql_select('*', 'spip_abonnes', 'id_abonne='.$this->id_abonne);
				if (sql_count($spip_abonne) == 1) {
					$abo = sql_fetch($spip_abonne);
					$table			= $table_des_abonnes[$abo['objet']]['table'];
					$champ_email	= $table_des_abonnes[$abo['objet']]['champ_email'];
					$champ_id		= $table_des_abonnes[$abo['objet']]['champ_id'];
					$champ_nom		= $table_des_abonnes[$abo['objet']]['champ_nom'];
					$obj = sql_select('OBJ.'.$champ_email.' AS email, '.(empty($champ_nom) ? '' : ' OBJ.'.$champ_nom.' AS nom, ').'OBJ.'.$champ_id.' AS id_objet', 'spip_'.$table.' AS OBJ', 'OBJ.'.$champ_id.'='.$abo['id_objet']);
					if (sql_count($obj) == 1) {
						$arr = sql_fetch($obj);
						$this->existe		= true;
						$this->objet		= $abo['objet'];
						$this->id_objet		= $abo['id_objet'];
						$this->email		= $arr['email'];
						$this->code			= $abo['code'];
						$this->nom			= $arr['nom'];
						$this->format		= $abo['format'];
						$this->extra		= $abo['extra'];
						$this->maj			= $abo['maj'];
					}
				}
			} else if (lettres_verifier_validite_email($email)) {
				$this->email = $email;
				foreach ($table_des_abonnes as $valeur) {
					$spip_objets = @sql_select('*', 'spip_'.$valeur['table'], $valeur['champ_email'].'='.sql_quote($this->email));
					if (@sql_count($spip_objets) == 1) {
						$arr = sql_fetch($spip_objets);
						$spip_abonnes = sql_select('*', 'spip_abonnes', 'objet="'.$valeur['table'].'" AND id_objet='.$arr[$valeur['champ_id']]);
						if (sql_count($spip_abonnes) == 1) {
							$abo = sql_fetch($spip_abonnes);
							$this->id_abonne	= $abo['id_abonne'];
							$this->objet		= $valeur['table'];
							$this->id_objet		= $arr[$valeur['champ_id']];
							$this->code			= $abo['code'];
							$this->nom			= $arr[$valeur['champ_nom']];
							$this->format		= $abo['format'];
							$this->extra		= $abo['extra'];
							$this->maj			= $abo['maj'];
							$this->existe		= true;
							break;
						}
					}
				}
			}
		}


		function enregistrer() {
			global $table_des_abonnes;
			if ($this->existe) {
				sql_updateq('spip_abonnes', array('format' => $this->format), 'id_abonne='.$this->id_abonne);
				if ($this->objet == 'abonnes')
					sql_updateq('spip_abonnes', array('nom' => ucwords($this->nom), 'email' => strtolower($this->email)), 'id_abonne='.$this->id_abonne);
			} else {
				foreach ($table_des_abonnes as $valeur) {
					$spip_objets = @sql_select('*', 'spip_'.$valeur['table'], $valeur['champ_email'].'='.sql_quote($this->email));
					if (@sql_count($spip_objets) == 1) {
						$arr = sql_fetch($spip_objets);
						$this->objet	= $valeur['table'];
						$this->id_objet	= $arr[$valeur['champ_id']];
						break;
					}
				}
				$this->code = md5(uniqid(rand()));
				$this->id_abonne = sql_insertq('spip_abonnes', 
												array(
													'objet' => $this->objet, 
													'code' => $this->code, 
													'format' => $this->format
													)
												);

				if (!intval($this->id_objet))
					$this->id_objet = $this->id_abonne;
				sql_updateq('spip_abonnes', array('id_objet' => intval($this->id_objet)), 'id_abonne='.intval($this->id_abonne));
				if ($this->objet == 'abonnes') {
					sql_updateq('spip_abonnes', array('email' => strtolower($this->email), 'nom' => ucwords($this->nom)), 'id_abonne='.intval($this->id_abonne));
				}
				$req = sql_select('*', 'spip_abonnes_statistiques', 'periode="'.date('Y-m').'"');
				if (sql_count($req) == 0)
					sql_insertq('spip_abonnes_statistiques', array('periode' => date('Y-m')));
				sql_update('spip_abonnes_statistiques', array('nb_inscriptions' => 'nb_inscriptions+1'), 'periode="'.date('Y-m').'"');
			}
			$this->existe = true;
			$this->enregistrer_champs_extra();
			$this->enregistrer_maj();
		}


		function enregistrer_statut($statut) {
			$ancien_statut = $this->statut;
			switch ($statut) {
				case 'valider':
					$this->valider_abonnements_en_attente();
					$redirection = generer_url_ecrire('abonnes', 'id_abonne='.$this->id_abonne, true);
					break;
				case 'poubelle':
					$this->supprimer();
					$redirection = generer_url_ecrire('abonnes_tous');
					break;
			}
			return $redirection;
		}


		function enregistrer_champs_extra($manuellement=false) {
			if (!$manuellement) {
				if ($champs_extra = $GLOBALS['champs_extra']['abonnes']) {
					$extra = array();
					foreach ($champs_extra as $cle => $valeur) {
						$extra[$cle] = _request('suppl_'.$cle);
					}
					$this->extra = serialize($extra);
				}
			}
			sql_updateq('spip_abonnes', array('extra' => $this->extra), 'id_abonne='.intval($this->id_abonne));
		}
		
		
		function enregistrer_maj() {
			sql_updateq('spip_abonnes', array('maj' => 'NOW()'), 'id_abonne='.intval($this->id_abonne));
		}
		
		
		function enregistrer_abonnement($id_rubrique=0) {
			global $connect_statut;
			if ($connect_statut == '0minirezo' or lettres_rubrique_autorisee($id_rubrique)) {
				if (sql_countsel('spip_abonnes_rubriques', 'id_abonne='.intval($this->id_abonne).' AND id_rubrique='.intval($id_rubrique)))
					sql_updateq('spip_abonnes_rubriques', array('statut' => 'a_valider', 'date_abonnement' => 'NOW()'), 'id_abonne='.intval($this->id_abonne).' AND id_rubrique='.intval($id_rubrique));
				else
					sql_insertq('spip_abonnes_rubriques', array('id_abonne' => intval($this->id_abonne), 'id_rubrique' => intval($id_rubrique), 'statut' => 'a_valider', 'date_abonnement' => 'NOW()'));
				$this->enregistrer_maj();
			}
		}


		function enregistrer_format($format) {
			$this->format = $format;
			sql_updateq('spip_abonnes', array('format' => $this->format), 'id_abonne='.intval($this->id_abonne));
			$this->enregistrer_maj();
		}


		function enregistrer_envoi($id_lettre, $resultat) {
			if ($resultat)
				$statut = 'envoye';
			else
				$statut = 'echec';
			if (sql_countsel('spip_abonnes_lettres', 'id_abonne='.intval($this->id_abonne).' AND id_lettre='.intval($id_lettre)))
				sql_updateq('spip_abonnes_lettres', array('statut' => $statut, 'format' => $this->format, 'maj' => 'NOW()'), 'id_abonne='.intval($this->id_abonne).' AND id_lettre='.intval($id_lettre));
			else
				sql_insertq('spip_abonnes_lettres', array('id_abonne' => intval($this->id_abonne), 'id_lettre' => intval($id_lettre), 'statut' => $statut, 'format' => $this->format, 'maj' => 'NOW()'));
			if (sql_countsel('spip_lettres_statistiques', 'periode="'.date('Y-m').'"') == 0)
				sql_insertq('spip_lettres_statistiques', array('periode' => date('Y-m')));
			sql_update('spip_lettres_statistiques', array('nb_envois' => 'nb_envois+1'), 'periode="'.date('Y-m').'"');
		}


		function enregistrer_clic($id_clic) {
			$verification_abonne = sql_select('C.url', 'spip_clics AS C INNER JOIN spip_abonnes_lettres AS AL ON AL.id_lettre=C.id_lettre', 'AL.id_abonne='.intval($this->id_abonne).' AND C.id_clic='.intval($id_clic));
			$verification_url = sql_select('url', 'spip_clics', 'id_clic='.intval($id_clic));
			if (sql_count($verification_abonne) == 1) {
				sql_insertq('spip_abonnes_clics', array('id_abonne' => $this->id_abonne, 'id_clic' => intval($id_clic)));
				$url = sql_fetch($verification_abonne);
				$redirection = $url['url'];
				$this->enregistrer_maj();
			} else if (sql_count($verification_url) == 1) {
				$url = sql_fetch($verification_url);
				$redirection = $url['url'];
			}
			
			if ($redirection)
				return $redirection;
			else
				return $GLOBALS['meta']['adresse_site'];
		}
		

		function envoyer_notification($action, $arguments=array()) {

			if (isset($arguments['rubriques'])) {
				foreach ($arguments['rubriques'] as $id_rubrique)
					$chaine_rubriques.= '&rubriques[]='.$id_rubrique;
			}
			if (!empty($arguments['format'])) {
				$this->format = $arguments['format'];
				$chaine_format = '&format='.$this->format;
			}
			$parametres = 'lang='.$arguments['lang'].$chaine_rubriques.'&code='.$this->code.'&email='.$this->email.$chaine_format;

			switch ($action) {

				case 'abonnements':
					$objet			= recuperer_fond('emails/lettres_abonnements_titre', $arguments);
					$message_html	= recuperer_fond('emails/lettres_abonnements_html', $arguments);
					$message_texte	= recuperer_fond('emails/lettres_abonnements_texte', $arguments);
					$url_action_validation_abonnements = url_absolue(generer_url_action('validation_abonnements', $parametres, true));
					$message_html	= ereg_replace("%%URL_VALIDATION_ABONNEMENTS%%", $url_action_validation_abonnements, $message_html);
					$message_texte	= ereg_replace("%%URL_VALIDATION_ABONNEMENTS%%", $url_action_validation_abonnements, $message_texte);
					break;
					
				case 'desabonnements':
					$objet			= recuperer_fond('emails/lettres_desabonnements_titre', $arguments);
					$message_html	= recuperer_fond('emails/lettres_desabonnements_html', $arguments);
					$message_texte	= recuperer_fond('emails/lettres_desabonnements_texte', $arguments);
					$url_action_validation_desabonnements = url_absolue(generer_url_action('validation_desabonnements', $parametres, true));
					$message_html	= ereg_replace("%%URL_VALIDATION_DESABONNEMENTS%%", $url_action_validation_desabonnements, $message_html);
					$message_texte	= ereg_replace("%%URL_VALIDATION_DESABONNEMENTS%%", $url_action_validation_desabonnements, $message_texte);
					break;
					
				case 'changement_format':
					$objet			= recuperer_fond('emails/lettres_changement_format_titre', $arguments);
					$message_html	= recuperer_fond('emails/lettres_changement_format_html', $arguments);
					$message_texte	= recuperer_fond('emails/lettres_changement_format_texte', $arguments);
					$url_action_validation_changement_format = url_absolue(generer_url_action('validation_changement_format', $parametres, true));
					$message_html	= ereg_replace("%%URL_VALIDATION_CHANGEMENT_FORMAT%%", $url_action_validation_changement_format, $message_html);
					$message_texte	= ereg_replace("%%URL_VALIDATION_CHANGEMENT_FORMAT%%", $url_action_validation_changement_format, $message_texte);
					break;
					
			}

			switch ($this->format) {
				case 'html':
					$corps = array('html' => $message_html, 'texte' => '');
					break;
				case 'texte':
					$corps = array('html' => '', 'texte' => $message_texte);
					break;
				case 'mixte':
				default:
					$corps = array('html' => $message_html, 'texte' => $message_texte);
					break;
			}

			$envoyer_mail = charger_fonction('envoyer_mail', 'inc');
			return $envoyer_mail($this->email, $objet, $corps);
		}


		function envoyer_lettre($id_lettre) {
			global $champs_extra;

			// verrouillage
			sql_update('spip_abonnes_lettres', array('verrou' => '1'), 'id_lettre='.intval($id_lettre).' AND id_abonne='.intval($this->id_abonne));

			$lettre = new lettre($id_lettre);
			
			if ($lettre->statut == 'brouillon')
				$objet		= 'TEST - '.$lettre->titre;
			else
				$objet		= $lettre->titre;
			$message_html	= $lettre->message_html;
			$message_texte	= $lettre->message_texte;

			$parametres = 'lang='.$lettre->lang.'&rubriques[]=-1&code='.$this->code.'&email='.$this->email;
			$url_action_validation_desabonnements = url_absolue(generer_url_action('validation_desabonnements', $parametres, true));
			$message_html	= ereg_replace("%%URL_VALIDATION_DESABONNEMENTS%%", $url_action_validation_desabonnements, $message_html);
			$message_texte	= ereg_replace("%%URL_VALIDATION_DESABONNEMENTS%%", $url_action_validation_desabonnements, $message_texte);

			$objet			= ereg_replace("%%EMAIL%%", $this->email, $objet);
			$message_html	= ereg_replace("%%EMAIL%%", $this->email, $message_html);
			$message_texte	= ereg_replace("%%EMAIL%%", $this->email, $message_texte);

			$message_html	= ereg_replace("%%CODE%%", $this->code, $message_html);
			$message_texte	= ereg_replace("%%CODE%%", $this->code, $message_texte);

			$objet			= lettres_remplacer_raccourci('NOM', $this->nom, $objet);
			$message_html	= lettres_remplacer_raccourci('NOM', $this->nom, $message_html);
			$message_texte	= lettres_remplacer_raccourci('NOM', $this->nom, $message_texte);

			if ($champs_extra['abonnes']) {
				$extra = unserialize($this->extra);
				foreach ($champs_extra['abonnes'] as $raccourci => $bidon) {
					$objet			= lettres_remplacer_raccourci($raccourci, $extra[$raccourci], $objet);
					$message_html	= lettres_remplacer_raccourci($raccourci, $extra[$raccourci], $message_html);
					$message_texte	= lettres_remplacer_raccourci($raccourci, $extra[$raccourci], $message_texte);
				}
			}

			if (function_exists('formulaires_remplacer_raccourci')) {
				$message_html	= formulaires_remplacer_raccourci($message_html, $this->email);
				$message_texte	= formulaires_remplacer_raccourci($message_texte, $this->email);
			}

			if (function_exists('lettres_specifique')) {
				$message_html	= lettres_specifique($message_html, $this->email);
				$message_texte	= lettres_specifique($message_texte, $this->email);
			}
			
			switch ($this->format) {
				case 'html':
					$corps = array('html' => $message_html, 'texte' => '');
					break;
				case 'texte':
					$corps = array('html' => '', 'texte' => $message_texte);
					break;
				case 'mixte':
				default:
					$corps = array('html' => $message_html, 'texte' => $message_texte);
					break;
			}

			$envoyer_mail = charger_fonction('envoyer_mail', 'inc');
			return $envoyer_mail($this->email, $objet, $corps);
		}
		
		
		function renvoyer_lettre($id_lettre) {
			$resultat = $this->envoyer_lettre($id_lettre);
			if ($resultat) {
				$this->enregistrer_envoi($id_lettre, $resultat);
				return true;
			}
			return false;	
		}
		
		
		function verifier_code($code) {
			if (strcmp($this->code, $code) == 0)
				return true;
			else
				return false;
		}
		
		
		function valider_abonnement($id_rubrique=0, $partie_publique=false) {
			global $connect_statut;
			if ($connect_statut == '0minirezo' or lettres_rubrique_autorisee($id_rubrique)) {
				sql_updateq('spip_abonnes_rubriques', array('statut' => 'valide', 'date_abonnement' => 'NOW()'), 'id_abonne='.intval($this->id_abonne).' AND id_rubrique='.intval($id_rubrique));
				$this->enregistrer_maj();
			}
			if ($partie_publique)
				$this->oublier_desabonnement();
			$this->supprimer_abonnements_inutiles();
		}
		
		
		function valider_abonnements_en_attente() {
			sql_updateq('spip_abonnes_rubriques', array('statut' => 'valide', 'date_abonnement' => 'NOW()'), 'id_abonne='.intval($this->id_abonne).' AND statut="a_valider"');
			$this->enregistrer_maj();
			$this->supprimer_abonnements_inutiles();
		}
		
		
		function valider_desabonnement($id_rubrique=0) {
			global $connect_statut;
			if ($id_rubrique == -1) {
				$this->memoriser_desabonnement();
				$this->supprimer();
			} else {
				if ($connect_statut == '0minirezo' or lettres_rubrique_autorisee($id_rubrique))
					sql_delete('spip_abonnes_rubriques', 'id_abonne='.intval($this->id_abonne).' AND id_rubrique='.intval($id_rubrique));
			}
			$this->enregistrer_maj();
		}
		
		
		function memoriser_desabonnement() {
			sql_insertq('spip_desabonnes', array('email' => $this->email));
		}
		
		
		function oublier_desabonnement() {
			sql_delete('spip_desabonnes', 'email='.sql_quote($this->email));
		}
		
		
		function calculer_nombre_abonnements($mode='total') {
			$a_valider	= sql_countsel('spip_abonnes_rubriques', 'id_abonne='.intval($this->id_abonne).' AND statut="a_valider"');
			$valide	= sql_countsel('spip_abonnes_rubriques', 'id_abonne='.intval($this->id_abonne).' AND statut="valide"');
			$total = $a_valider + $valide;
			switch ($mode) {
				case 'a_valider':
					return $a_valider;
				case 'valide':
					return $valide;
				case 'total':
				default:
					return $total;
			}
		}
		
		
		function calculer_statut() {
			$statut = 'vide';
			$abonnements_a_valider	= sql_countsel('spip_abonnes_rubriques', 'id_abonne='.intval($this->id_abonne).' AND statut="a_valider"');
			if ($abonnements_a_valider > 0)
				$statut = 'a_valider';
			$abonnements_valides	= sql_countsel('spip_abonnes_rubriques', 'id_abonne='.intval($this->id_abonne).' AND statut="valide"');
			if ($abonnements_valides > 0)
				$statut = 'valide';
			return $statut;
		}
		
		
		function recuperer_abonnements($seulement_valides=true) {
			$abonnements = array();
			if ($seulement_valides)
				$res = sql_select('id_rubrique', 'spip_abonnes_rubriques', 'id_abonne='.intval($this->id_abonne).' AND statut="valide"');
			else
				$res = sql_select('id_rubrique', 'spip_abonnes_rubriques', 'id_abonne='.intval($this->id_abonne));
			while ($arr = sql_fetch($res)) {
				$abonnements[] = $arr['id_rubrique'];
			}
			return $abonnements;
		}
		
		
		function supprimer() {
			if ($GLOBALS['meta']['spip_lettres_notifier_suppression_abonne'] == 'oui') {
				$objet			= recuperer_fond('emails/lettres_suppression_abonne_titre', array('email' => $this->email));
				$message_html	= recuperer_fond('emails/lettres_suppression_abonne_html', array('email' => $this->email));
				$message_texte	= recuperer_fond('emails/lettres_suppression_abonne_texte', array('email' => $this->email));
				$corps = array('html' => $message_html, 'texte' => $message_texte);
				$envoyer_mail = charger_fonction('envoyer_mail', 'inc');
				$envoyer_mail($GLOBALS['meta']['email_webmaster'], $objet, $corps);
			}
			$req = sql_select('*', 'spip_abonnes_statistiques', 'periode="'.date('Y-m').'"');
			if (sql_count($req) == 0)
				sql_insertq('spip_abonnes_statistiques', array('periode' => date('Y-m')));
			sql_update('spip_abonnes_statistiques', array('nb_desinscriptions' => 'nb_desinscriptions+1'), 'periode='.sql_quote(date('Y-m')));
			sql_delete('spip_abonnes', 'id_abonne='.intval($this->id_abonne));
			sql_delete('spip_abonnes_clics', 'id_abonne='.intval($this->id_abonne));
			sql_delete('spip_abonnes_lettres', 'id_abonne='.intval($this->id_abonne));
			sql_delete('spip_abonnes_rubriques', 'id_abonne='.intval($this->id_abonne));
		}


		function supprimer_si_zero_abonnement() {
			$nb_abonnements = count($this->recuperer_abonnements(false));
			if ($nb_abonnements == 0)
				$this->supprimer();
		}
		
		
		function supprimer_abonnements_inutiles() {
			$test_racine = sql_countsel('spip_abonnes_rubriques', 'id_rubrique=0 AND id_abonne='.intval($this->id_abonne));
			if ($test_racine)
				sql_delete('spip_abonnes_rubriques', 'id_rubrique!=0 AND id_abonne='.intval($this->id_abonne));
		}


	}




	/**
	 * lettre - classe pour la gestion des lettres
	 *
	 * @copyright 2006-2009 Artégo
	 */

	class lettre {

	    var $id_lettre;
		var $id_rubrique;
		var $titre;
		var $descriptif;
		var $chapo;
		var $texte;
		var $date;
		var $lang;
		var $message_html;
		var $message_texte;
		var $date_debut_envoi;
		var $date_fin_envoi;
		var $extra;
		var $statut = 'brouillon';

		var $existe = false;


		/**
		 * lettre : constructeur
		 *
		 * @param int id_lettre
		 * @return void
		 **/
		function lettre($id_lettre=-1) {
			$this->id_lettre = intval($id_lettre);
			$verif = sql_select('*', 'spip_lettres', 'id_lettre='.intval($this->id_lettre));
			if (sql_count($verif) == 1) {
				$lettre = sql_fetch($verif);
				$this->id_rubrique				= $lettre['id_rubrique'];
				$this->titre					= $lettre['titre'];
				$this->descriptif				= $lettre['descriptif'];
				$this->chapo					= $lettre['chapo'];
				$this->texte					= $lettre['texte'];
				$this->ps						= $lettre['ps'];
				$this->date						= $lettre['date'];
				$this->lang						= $lettre['lang'];
				$this->message_html				= $lettre['message_html'];
				$this->message_texte			= $lettre['message_texte'];
				$this->date_debut_envoi			= $lettre['date_debut_envoi'];
				$this->date_fin_envoi			= $lettre['date_fin_envoi'];
				$this->statut					= $lettre['statut'];
				$this->extra					= $lettre['extra'];
				$this->existe					= true;
			}
		}
		

		function enregistrer() {
			if ($this->id_lettre == -1 and $this->statut == 'brouillon') {
				$champs = array(
								'id_rubrique' => intval($this->id_rubrique),
								'titre' => $this->titre,
								'descriptif' => $this->descriptif,
								'chapo' => $this->chapo,
								'texte' => $this->texte,
								'ps' => $this->ps,
								'date' => 'NOW()',
								'maj' => 'NOW()'
								);
				if ($this->extra)
					$champs['extra'] = $this->extra;
				$this->id_lettre = sql_insertq('spip_lettres', $champs);
				$this->existe = true;
				sql_updateq("spip_documents_liens", array("id_objet" => $this->id_lettre), 'id_objet='.(0 - $GLOBALS['visiteur_session']['id_auteur']).' AND objet="lettre"');
				calculer_rubriques();
				propager_les_secteurs();
				calculer_langues_rubriques();
			} else if ($this->statut == 'brouillon') {
				$champs = array(
								'id_rubrique' => intval($this->id_rubrique),
								'titre' => $this->titre,
								'descriptif' => $this->descriptif,
								'chapo' => $this->chapo,
								'texte' => $this->texte,
								'ps' => $this->ps,
								'date' => 'NOW()',
								'maj' => 'NOW()'
								);
				if ($this->extra)
					$champs['extra'] = $this->extra;
				sql_updateq('spip_lettres', $champs, 'id_lettre='.intval($this->id_lettre));
				calculer_rubriques();
				propager_les_secteurs();
				calculer_langues_rubriques();
			}
		}

		function tester(){
			$resultat = true;
			$ancien_statut = $this->statut;
			sql_updateq('spip_lettres', array('statut' => 'envoi_en_cours', 'maj' => 'NOW()'), 'id_lettre='.intval($this->id_lettre));
			$this->enregistrer_squelettes();
			sql_updateq('spip_lettres', array('statut' => $ancien_statut, 'maj' => 'NOW()'), 'id_lettre='.intval($this->id_lettre));
			$auteurs = sql_select('A.email', 'spip_auteurs AS A INNER JOIN spip_auteurs_lettres AS AL ON AL.id_auteur=A.id_auteur', 'AL.id_lettre='.intval($this->id_lettre));
			while ($auteur = sql_fetch($auteurs)) {
				$abonne = new abonne(0, $auteur['email']);
				if (!$abonne->envoyer_lettre($this->id_lettre)) {
					$resultat = false;
					break;
				}
			}
			return $resultat;
		}


		function enregistrer_statut($statut, $cron=false, $xml=false) {
			$ancien_statut = $this->statut;
			switch ($statut) {
				case 'brouillon':
					$redirection = generer_url_ecrire('lettres', 'id_lettre='.$this->id_lettre, true);
					break;
				case 'envoi_en_cours':
					if ($ancien_statut == 'brouillon') {
						$this->statut = 'envoi_en_cours';
						$this->date_debut_envoi = date('Y-m-d h:i:s');
						sql_updateq('spip_lettres', array('statut' => $this->statut, 'date_debut_envoi' => 'NOW()', 'maj' => 'NOW()'), 'id_lettre='.intval($this->id_lettre));
						$this->enregistrer_squelettes();
						calculer_rubriques();
						propager_les_secteurs();
						calculer_langues_rubriques();
						$rubriques = lettres_recuperer_toutes_les_rubriques_parentes($this->id_rubrique);
						$rubriques_virgules = implode(',', $rubriques);
						$resultat_abonnes = sql_select('A.id_abonne, A.format', 'spip_abonnes_rubriques AS AR INNER JOIN spip_abonnes AS A ON A.id_abonne=AR.id_abonne', 'AR.id_rubrique IN ('.$rubriques_virgules.') AND AR.statut="valide"', 'A.id_abonne');
						while ($arr = sql_fetch($resultat_abonnes)) {
							sql_insertq('spip_abonnes_lettres', array(
																	'id_abonne' => intval($arr['id_abonne']), 
																	'id_lettre' => intval($this->id_lettre),
																	'statut' => 'a_envoyer',
																	'format' => $arr['format'],
																	'verrou' => 0,
																	'maj' => 'NOW()'
																	));
						}
						if ($cron) {
							$envois = sql_select('*', 'spip_abonnes_lettres', 'id_lettre='.intval($this->id_lettre).' AND verrou=0 AND statut="a_envoyer"');
							if (sql_count($envois) > 0) {
								while ($arr = sql_fetch($envois)) {
									$abonne = new abonne($arr['id_abonne']);
									$resultat = $abonne->envoyer_lettre($this->id_lettre);
									$abonne->enregistrer_envoi($this->id_lettre, $resultat);
								}
							}
							$this->statut = 'envoyee';
							$this->date_fin_envoi = date('Y-m-d h:i:s');
							sql_updateq('spip_lettres', array('statut' => $this->statut, 'date_fin_envoi' => 'NOW()', 'maj' => 'NOW()'), 'id_lettre='.intval($this->id_lettre));
							sql_updateq('spip_abonnes_lettres', array('statut' => 'annule'), 'id_lettre='.intval($this->id_lettre).' AND statut="a_envoyer"');
						}
						$redirection = generer_url_ecrire('lettres', 'id_lettre='.$this->id_lettre, true);
					}
					if ($ancien_statut == 'envoyee') {
						$this->statut = 'envoi_en_cours';
						sql_updateq('spip_lettres', array('statut' => $this->statut, 'date_debut_envoi' => 'NOW()', 'maj' => 'NOW()'), 'id_lettre='.intval($this->id_lettre));
						sql_updateq('spip_abonnes_lettres', array('verrou' => 0, 'statut' => 'a_envoyer'), 'id_lettre='.intval($this->id_lettre));
						$redirection = generer_url_ecrire('lettres', 'id_lettre='.$this->id_lettre, true);
					}
					if ($ancien_statut == 'envoi_en_cours') {
						$envois = sql_select('*', 'spip_abonnes_lettres', 'id_lettre='.intval($this->id_lettre).' AND verrou=0 AND statut="a_envoyer"', '', '', '10');
						if (sql_count($envois) > 0) {
							while ($arr = sql_fetch($envois)) {
								$abonne = new abonne($arr['id_abonne']);
								$resultat = $abonne->envoyer_lettre($this->id_lettre);
								$abonne->enregistrer_envoi($this->id_lettre, $resultat);
							}
							if ($xml)
								$redirection = 0;
							else
								$redirection = generer_url_ecrire('lettres', 'id_lettre='.$this->id_lettre, true);
						} else {
							$this->statut = 'envoyee';
							$this->date_fin_envoi = date('Y-m-d h:i:s');
							sql_updateq('spip_lettres', array('statut' => $this->statut, 'date_fin_envoi' => 'NOW()', 'maj' => 'NOW()'), 'id_lettre='.intval($this->id_lettre));
							sql_updateq('spip_abonnes_lettres', array('statut' => 'annule'), 'id_lettre='.intval($this->id_lettre).' AND statut="a_envoyer"');
							if ($xml)
								$redirection = 1;
							else
								$redirection = generer_url_ecrire('lettres', 'id_lettre='.$this->id_lettre.'&message=envoi_termine', true);
						}
					}
					break;
				case 'envoyee':
					if ($ancien_statut == 'envoi_en_cours') {
						$this->statut = 'envoyee';
						$this->date_fin_envoi = date('Y-m-d h:i:s');
						sql_updateq('spip_lettres', array('statut' => $this->statut, 'date_fin_envoi' => 'NOW()', 'maj' => 'NOW()'), 'id_lettre='.intval($this->id_lettre));
						sql_updateq('spip_abonnes_lettres', array('statut' => 'annule'), 'id_lettre='.intval($this->id_lettre).' AND statut="a_envoyer"');
					}
					$redirection = generer_url_ecrire('lettres', 'id_lettre='.$this->id_lettre.'&message=envoi_termine', true);
					break;
				case 'poubelle':
				case 'poub':
					sql_updateq('spip_lettres', array('statut' => 'poub'), 'id_lettre='.intval($this->id_lettre));
					#$id_rubrique = $this->id_rubrique;
					#$this->supprimer();
					#$redirection = generer_url_ecrire('naviguer', 'id_rubrique='.$id_rubrique, true);
					break;
			}
			return $redirection;
		}
		
		
		function callback_clic_html($matches) {
			$url = $matches[2];
			if (strcmp($url, '%%URL_VALIDATION_DESABONNEMENTS%%') != 0) {
				$verification = sql_select('id_clic', 'spip_clics', 'url='.sql_quote($url).' AND id_lettre='.intval($this->id_lettre));
				if (sql_count($verification) == 1) {
					$arr = sql_fetch($verification);
					$id_clic = $arr['id_clic'];
				} else {
					$id_clic = sql_insertq('spip_clics', array('id_lettre' => $this->id_lettre, 'url' => html_entity_decode($url)));
				}
				$url_clic = generer_url_action('clic', 'id_clic='.$id_clic.'&code=%%CODE%%&email=%%EMAIL%%', false);
				return 'href="'.$url_clic.'"';
			} else {
				return 'href="'.$url.'"';
			}
		}
		
		
		function callback_clic_texte($matches) {
			$url = $matches[0];
			if (strcmp($url, '%%URL_VALIDATION_DESABONNEMENTS%%') != 0) {
				$verification = sql_select('id_clic', 'spip_clics', 'url='.sql_quote($url).' AND id_lettre='.intval($this->id_lettre));
				if (sql_count($verification) == 1) {
					$arr = sql_fetch($verification);
					$id_clic = $arr['id_clic'];
				} else {
					$id_clic = sql_insertq('spip_clics', array('id_lettre' => $this->id_lettre, 'url' => html_entity_decode($url)));
				}
				$url_clic = generer_url_action('clic', 'id_clic='.$id_clic.'&code=%%CODE%%&email=%%EMAIL%%', true);
				return $url_clic;
			} else {
				return $url;
			}
		}
		
		
		function callback_images($matches) {
			global $i;
			$image = $matches[2];
			if (file_exists($image)) {
				$tab = explode('.', basename($image));
				$copie = _DIR_LETTRES.'lettre-'.$this->id_lettre.'-'.$i.'.'.$tab[1];
				$i++;
				if (copy($image, $copie))
					return 'src="'.$copie.'"';
			}
			return 'src="'.$image.'"';
		}
		
		
		function enregistrer_squelettes($vidange = true) {
			$this->message_html	= recuperer_fond($GLOBALS['meta']['spip_lettres_fond_lettre_html'], array('id_lettre' => $this->id_lettre, 'lang' => $this->lang));
			$this->message_texte = recuperer_fond($GLOBALS['meta']['spip_lettres_fond_lettre_texte'], array('id_lettre' => $this->id_lettre, 'lang' => $this->lang));
			
			if ($vidange) {
				// petite vidange due à l'envoi de test
				sql_delete('spip_clics', 'id_lettre='.intval($this->id_lettre));
				sql_delete('spip_abonnes_clics', 'id_lettre='.intval($this->id_lettre));
			}

			$this->message_html = preg_replace_callback('/(href=")(.*?)(")/i', array($this, 'callback_clic_html'), $this->message_html);
			$this->message_html = preg_replace_callback("/(href=')(.*?)(')/i", array($this, 'callback_clic_html'), $this->message_html);
			global $i;
			$i = 1;
			$this->message_html = preg_replace_callback('/(src=")(.*?)(")/i', array($this, 'callback_images'), $this->message_html);
			$this->message_html = preg_replace_callback("/(src=')(.*?)(')/i", array($this, 'callback_images'), $this->message_html);
			$this->message_texte = preg_replace_callback('/http:[^\s]*/', array($this, 'callback_clic_texte'), $this->message_texte);
			
			sql_updateq('spip_lettres', array('message_html' => $this->message_html, 'message_texte' => $this->message_texte, 'maj' => 'NOW()'), 'id_lettre='.intval($this->id_lettre));
		}


		function enregistrer_auteur($id_auteur) {
			$verif_email = sql_countsel('spip_auteurs', 'id_auteur='.intval($id_auteur).' AND email!=""');
			if ($verif_email) {
				sql_replace('spip_auteurs_lettres', array('id_auteur' => intval($id_auteur), 'id_lettre' => intval($this->id_lettre)));
				$email = sql_getfetsel('email', 'spip_auteurs', 'id_auteur='.intval($id_auteur));
				$abonne = new abonne(0, $email);
				if (!$abonne->existe)
					$abonne->enregistrer();
				$abonne->enregistrer_abonnement($this->id_rubrique);
				$abonne->valider_abonnement($this->id_rubrique);
			}
		}


		function enregistrer_article($id_article) {
			$verif = sql_countsel('spip_articles_lettres', 'id_article='.intval($id_article).' AND id_lettre='.intval($this->id_lettre));
			if (!$verif)
				sql_insertq('spip_articles_lettres', array('id_article' => intval($id_article), 'id_lettre' => intval($this->id_lettre)));
		}


		function copier_lettre($copie_lettre) {
			$lettre_a_copier = new lettre($copie_lettre);
			if ($lettre_a_copier->existe) {
				$this->id_rubrique				= $lettre_a_copier->id_rubrique;
				$this->titre					= _T('lettresprive:copie').' - '.$lettre_a_copier->titre;
				$this->descriptif				= $lettre_a_copier->descriptif;
				$this->chapo					= $lettre_a_copier->chapo;
				$this->texte					= $lettre_a_copier->texte;
				$this->ps						= $lettre_a_copier->ps;
				$this->date						= $lettre_a_copier->date;
				$this->extra					= $lettre_a_copier->extra;
				$this->enregistrer();
				// auteurs
				$auteurs = sql_select('id_auteur', 'spip_auteurs_lettres', 'id_lettre='.intval($lettre_a_copier->id_lettre));
				while ($arr = sql_fetch($auteurs))
					$this->enregistrer_auteur($arr['id_auteur']);
				// logos
				$logo_f = charger_fonction('chercher_logo', 'inc');
				if ($logo = $logo_f($lettre_a_copier->id_lettre, 'id_lettre', 'on')) {
					list($fid, $dir, $nom, $format) = $logo;
					copy($fid, $dir.'lettreon'.$this->id_lettre.'.'.$format);
				}
				if ($logo = $logo_f($lettre_a_copier->id_lettre, 'id_lettre', 'off')) {
					list($fid, $dir, $nom, $format) = $logo;
					copy($fid, $dir.'lettreoff'.$this->id_lettre.'.'.$format);
				}
				// mots-clés
				$mots = sql_select('id_mot', 'spip_mots_lettres', 'id_lettre='.intval($lettre_a_copier->id_lettre));
				while ($arr = sql_fetch($mots))
					sql_insertq('spip_mots_lettres', array('id_mot' => intval($arr['id_mot']), 'id_lettre' => intval($this->id_lettre)));
			}
		}
		
		
		function calculer_nb_envois($statut='') {
			if ($statut)
				return sql_countsel('spip_abonnes_lettres', 'id_lettre='.intval($this->id_lettre).' AND statut='.sql_quote($statut));
			else
				return sql_countsel('spip_abonnes_lettres', 'id_lettre='.intval($this->id_lettre));
		}


		function calculer_pourcentage_format($format) {
			$total = $this->calculer_nb_envois();
			if ($total) {
				$nb = sql_countsel('spip_abonnes_lettres', 'id_lettre='.intval($this->id_lettre).' AND format='.sql_quote($format));
				return floor($nb / $total * 100);
			} else {
				return 0;
			}
		}
		

		function calculer_taux_ouverture() {
			$total = $this->calculer_nb_envois();
			if ($total) {
				$nb = sql_count(sql_select('AC.id_abonne', 'spip_abonnes_clics AS AC, spip_clics AS C', 'C.id_clic=AC.id_clic AND C.id_lettre='.intval($this->id_lettre), 'AC.id_abonne'));
				$pourcentage = $nb / $total * 100;
				return round($pourcentage, 2);
			} else {
				return 0;
			}
		}
		
		function supprimer() {
			sql_delete('spip_lettres', 'id_lettre='.intval($this->id_lettre));
			$res = sql_select('id_clic', 'spip_clics', 'id_lettre='.intval($this->id_lettre));
			while ($arr = sql_fetch($res))
				sql_delete('spip_abonnes_clics', 'id_clic='.intval($arr['id_clic']));
			sql_delete('spip_clics', 'id_lettre='.intval($this->id_lettre));
			sql_delete('spip_mots_lettres', 'id_lettre='.intval($this->id_lettre));
			sql_delete('spip_abonnes_lettres', 'id_lettre='.intval($this->id_lettre));
			sql_delete('spip_auteurs_lettres', 'id_lettre='.intval($this->id_lettre));
			// suppression logos
			$logo_f = charger_fonction('chercher_logo', 'inc');
			if ($logo_on = $logo_f($this->id_lettre, 'id_lettre', 'on'))
				unlink($logo_on[0]);
			if ($logo_off = $logo_f($this->id_lettre, 'id_lettre', 'off'))
				unlink($logo_off[0]);
			// suppression documents
			$res = sql_select('D.*', 'spip_documents_liens AS DL INNER JOIN spip_documents AS D ON D.id_document=DL.id_document', 'DL.id_objet='.intval($this->id_lettre).' AND DL.objet="lettre"');
			$supprimer_document = charger_fonction('supprimer_document','action');
			while ($arr = sql_fetch($res))
				$supprimer_document($arr['id_document']);
			sql_delete('spip_documents_liens', 'id_objet='.intval($this->id_lettre).' AND objet="lettre"');
			// articles associés
			sql_delete('spip_articles_lettres', 'id_lettre='.intval($this->id_lettre));
			calculer_rubriques();
			propager_les_secteurs();
			calculer_langues_rubriques();
		}


		function supprimer_article($id_article) {
			sql_delete('spip_articles_lettres', 'id_article='.intval($id_article).' AND id_lettre='.intval($this->id_lettre));
		}
		
		
	}

?>
