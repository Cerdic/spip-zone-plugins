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
	include_spip('classes/lettre');

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
			if (intval($id_abonne) != 0) {
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
			} else if ($email AND lettres_verifier_validite_email($email)) {
				$this->email = $email;
				foreach ($table_des_abonnes as $valeur) {
					$spip_objets = @sql_select('*', 'spip_'.$valeur['table'], $valeur['champ_email'].'='.sql_quote($this->email));
					if ($arr = sql_fetch($spip_objets)) {
						$this->nom			= $arr[$valeur['champ_nom']];
						$this->objet		= $valeur['table'];
						$this->id_objet		= $arr[$valeur['champ_id']];
						$spip_abonnes = sql_select('*', 'spip_abonnes', 'objet="'.$valeur['table'].'" AND id_objet='.$arr[$valeur['champ_id']]);
						if (sql_count($spip_abonnes) == 1) {
							$abo = sql_fetch($spip_abonnes);
							$this->id_abonne	= $abo['id_abonne'];
							$this->code			= $abo['code'];
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
			include_spip('inc/autoriser');
			if (autoriser('abonner','rubrique',$id_rubrique) OR lettres_rubrique_autorisee($id_rubrique)) {
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
			static $mois = null;
			$statut = $resultat?'envoye':'echec';
			
			if (sql_countsel('spip_abonnes_lettres', 'id_abonne='.intval($this->id_abonne).' AND id_lettre='.intval($id_lettre)))
				sql_updateq('spip_abonnes_lettres', array('statut' => $statut, 'format' => $this->format, 'maj' => 'NOW()'), 'id_abonne='.intval($this->id_abonne).' AND id_lettre='.intval($id_lettre));
			else
				sql_insertq('spip_abonnes_lettres', array('id_abonne' => intval($this->id_abonne), 'id_lettre' => intval($id_lettre), 'statut' => $statut, 'format' => $this->format, 'maj' => 'NOW()'));

			if (!$mois) {
				$mois = date('Y-m');
				if (!sql_countsel('spip_lettres_statistiques', 'periode='.sql_quote($mois)))
					sql_insertq('spip_lettres_statistiques', array('periode' => $mois,'nb_envois'=>0));
			}
			sql_update('spip_lettres_statistiques', array('nb_envois' => 'nb_envois+1'), 'periode='.sql_quote($mois));
		}


		function enregistrer_clic($id_clic) {
			$verification_url = sql_select('url, id_lettre', 'spip_clics', 'id_clic='.intval($id_clic));
			if (sql_count($verification_url) == 1) {
				$url = sql_fetch($verification_url);
				$redirection = $url['url'];
				$id_lettre = intval($url['id_lettre']);
			};
			if ($GLOBALS['meta']['spip_lettres_cliquer_anonyme']=='non') {
				$verification_abonne = sql_select('C.url', 'spip_clics AS C INNER JOIN spip_abonnes_lettres AS AL ON AL.id_lettre=C.id_lettre', 'AL.id_abonne='.intval($this->id_abonne).' AND C.id_clic='.intval($id_clic));
				if (sql_count($verification_abonne) == 1) {
					sql_insertq('spip_abonnes_clics', array('id_abonne' => $this->id_abonne, 'id_clic' => intval($id_clic), 'id_lettre' => $id_lettre)); // le champ id_lettre pourrait être supprimé de la bdd, mais comme il est là on le renseigne
					$urldeja = $url;
					$url = sql_fetch($verification_abonne);		// inutile semblerait-il
					$redirection = $url['url']; 				// inutile semblerait-il
					if ($urldeja != $url) 						// pour détecter si jamais c'est utile
						spip_log ("Ya un truc à piger dans spip-lettres : urldeja=$urldeja different de url=$url", "enquete"); 
					$this->enregistrer_maj();
				}
			} else	// on enregistre tout sur le compte du non-abonné '0'
				sql_insertq('spip_abonnes_clics', array('id_abonne' => 0, 'id_clic' => intval($id_clic), 'id_lettre' => $id_lettre));

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
					$message_html	= str_replace("%%URL_VALIDATION_ABONNEMENTS%%", $url_action_validation_abonnements, $message_html);
					$message_texte	= str_replace("%%URL_VALIDATION_ABONNEMENTS%%", $url_action_validation_abonnements, $message_texte);
					break;
					
				case 'desabonnements':
					$objet			= recuperer_fond('emails/lettres_desabonnements_titre', $arguments);
					$message_html	= recuperer_fond('emails/lettres_desabonnements_html', $arguments);
					$message_texte	= recuperer_fond('emails/lettres_desabonnements_texte', $arguments);
					$url_action_validation_desabonnements = url_absolue(generer_url_action('validation_desabonnements', $parametres, true));
					$message_html	= str_replace("%%URL_VALIDATION_DESABONNEMENTS%%", $url_action_validation_desabonnements, $message_html);
					$message_texte	= str_replace("%%URL_VALIDATION_DESABONNEMENTS%%", $url_action_validation_desabonnements, $message_texte);
					break;
					
				case 'changement_format':
					$objet			= recuperer_fond('emails/lettres_changement_format_titre', $arguments);
					$message_html	= recuperer_fond('emails/lettres_changement_format_html', $arguments);
					$message_texte	= recuperer_fond('emails/lettres_changement_format_texte', $arguments);
					$url_action_validation_changement_format = url_absolue(generer_url_action('validation_changement_format', $parametres, true));
					$message_html	= str_replace("%%URL_VALIDATION_CHANGEMENT_FORMAT%%", $url_action_validation_changement_format, $message_html);
					$message_texte	= str_replace("%%URL_VALIDATION_CHANGEMENT_FORMAT%%", $url_action_validation_changement_format, $message_texte);
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
			if (!$this->existe)
				return;
			global $champs_extra;

			// le verrouillage est fait en amont, par la gestion des queue
			$lettre = new lettre($id_lettre);

			$objet = $lettre->titre;
			if ($lettre->statut == 'brouillon')
				$objet = 'TEST - '.$lettre->titre;

			$message_html	= $lettre->message_html;
			$message_texte	= $lettre->message_texte;

			$parametres = 'lang='.$lettre->lang.'&rubriques[]=-1&code='.$this->code.'&email='.$this->email;
			$url_action_validation_desabonnements = url_absolue(generer_url_action('validation_desabonnements', $parametres, true));
			$message_html	= str_replace("%%URL_VALIDATION_DESABONNEMENTS%%", $url_action_validation_desabonnements, $message_html);
			$message_texte	= str_replace("%%URL_VALIDATION_DESABONNEMENTS%%", $url_action_validation_desabonnements, $message_texte);

			$objet			= str_replace("%%EMAIL%%", $this->email, $objet);
			$message_html	= str_replace("%%EMAIL%%", $this->email, $message_html);
			$message_texte	= str_replace("%%EMAIL%%", $this->email, $message_texte);

			$message_html	= str_replace("%%CODE%%", $this->code, $message_html);
			$message_texte	= str_replace("%%CODE%%", $this->code, $message_texte);

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
			include_spip('inc/autoriser');
			if (autoriser('validerabonnement','rubrique',$id_rubrique) OR lettres_rubrique_autorisee($id_rubrique)) {
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
			if ($id_rubrique == -1) {
				$this->memoriser_desabonnement();
				$this->supprimer();
			} else {
				include_spip('inc/autoriser');
				if (autoriser('validerdesabonnement','rubrique',$id_rubrique) or lettres_rubrique_autorisee($id_rubrique))
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

?>