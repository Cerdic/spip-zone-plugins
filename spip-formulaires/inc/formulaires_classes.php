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


	include_spip('inc/cookie');
	include_spip('formulaires_fonctions');
	include_spip('inc/rubriques');
	include_spip('inc/presentation');


	/**
	 * applicant
	 *
	 * @copyright Artégo
	 */

	class applicant {

	    var $id_applicant;
		var $iv;
		var $email;
		var $mdp;
		var $nom;
		var $txt;

		var $existe = false;


		/**
		 * applicant : constructeur
		 *
		 * @param int id_applicant, qd omis il crée un nouveau applicant, sinon charge les données du client en question
		 * @return void
		 **/
		function applicant($id_applicant=-1) {
			if ($id_applicant == -1) {
				// insertion de l'applicant
				$verification = false;
				while (!$verification) {
				    $this->iv = formulaires_generer_vecteur_initialisation();
					$res = sql_select('id_applicant', 'spip_applicants', 'iv="'.base64_encode($this->iv).'"');
					if (sql_count($res) == 0)
						$verification = true;
				}
				$this->id_applicant = sql_insertq('spip_applicants', array('iv' => base64_encode($this->iv)));
				$this->existe = true;
			} else {
				$verification = sql_select('*', 'spip_applicants', 'id_applicant='.intval($id_applicant));
				if (sql_count($verification) == 1) {
					$tableau = sql_fetch($verification);
					$this->id_applicant	= $tableau['id_applicant'];
					$this->iv			= base64_decode($tableau['iv']);
					$this->email		= $tableau['email'];
					$this->txt			= ($this->email!=NULL) ? $this->email : ("Visiteur n° " . $this->id_applicant);
					$this->nom			= $tableau['nom'];
					$this->mdp			= $tableau['mdp'];
					$this->existe		= true;
				} else {
					$this->iv			= '';
					$this->existe		= false;
					$this->id_applicant	= -1;
				}
			}
		}


		/**
		 * enregistrer
		 *
		 * @return void
		 **/
		function enregistrer() {
			$email_en_base = sql_getfetsel('email', 'spip_applicants', 'id_applicant='.intval($this->id_applicant));
			if (ereg(_REGEXP_EMAIL, $email_en_base)) {
				// mettre à jour l'email de l'applicant
				sql_updateq('spip_applicants', array('email' => $this->email, 'nom' => ucwords($this->nom)), 'id_applicant='.intval($this->id_applicant));
				// synchroniser les réponses à une question email_applicant
				$reponses_email_applicant = sql_select('R.id_reponse AS id_reponse', 'spip_reponses AS R INNER JOIN spip_applications AS APP ON APP.id_application=R.id_application INNER JOIN spip_questions AS Q ON Q.id_question=R.id_question', 'APP.id_applicant='.intval($this->id_applicant).' AND Q.type="email_applicant"');
				while ($arr = sql_fetch($reponses_email_applicant)) {
					sql_updateq('spip_reponses', array('valeur' => $this->email), 'id_reponse='.intval($arr['id_reponse']));
				}
				// synchroniser les réponses à une question nom_applicant
				$reponses_nom_applicant = sql_select('R.id_reponse AS id_reponse', 'spip_reponses AS R INNER JOIN spip_applications AS APP ON APP.id_application=R.id_application INNER JOIN spip_questions AS Q ON Q.id_question=R.id_question', 'APP.id_applicant='.intval($this->id_applicant).' AND Q.type="nom_applicant"');
				while ($arr = sql_fetch($reponses_email_applicant)) {
					sql_updateq('spip_reponses', array('valeur' => ucwords($this->nom)), 'id_reponse='.intval($arr['id_reponse']));
				}
			} else {
				$mdp = strtolower(formulaires_generer_nouveau_mdp());
				$this->mdp = $mdp;
				sql_updateq('spip_applicants', array('email' => $this->email, 'nom' => ucwords($this->nom), 'mdp' => $this->mdp), 'id_applicant='.intval($this->id_applicant));
			}
			$this->enregistrer_maj();
			$this->existe = true;
		}


		function enregistrer_maj() {
			sql_update('spip_applicants', array('maj' => 'NOW()'), 'id_applicant='.intval($this->id_applicant));
		}
		
		
		/**
		 * poser_cookies : pose les cookies pour que l'applicant reste identifié
		 *
		 * @return void
		 **/
		function poser_cookies() {
			// pose des cookies : le vecteur d'initialisation et l'id_applicant crypté
			spip_setcookie('spip_formulaires_mcrypt_iv', base64_encode($this->iv), time() + _DUREE_VALIDITE_COOKIE_FORMULAIRES);
			spip_setcookie('spip_formulaires_id_applicant', formulaires_crypter_avec_blowfish($this->id_applicant, $this->iv, $GLOBALS['meta']['spip_formulaires_blowfish']), time() + _DUREE_VALIDITE_COOKIE_FORMULAIRES);
			// on met à jour en base
			sql_update('spip_applicants', array('cookie' => 'NOW()'), 'id_applicant='.intval($this->id_applicant));
		}


		/**
		 * supprimer_cookies : supprime les cookies qu'on avait posés
		 *
		 * @return void
		 **/
		function supprimer_cookies() {
			spip_setcookie('spip_formulaires_mcrypt_iv');
			spip_setcookie('spip_formulaires_id_applicant');
		}


	}



	/**
	 * formulaire
	 *
	 * @copyright Artégo
	 */

	class formulaire {

	    var $id_formulaire;
		var $id_rubrique;
		var $titre;
		var $descriptif;
		var $chapo;
		var $texte;
		var $merci;
		var $ps;
		var $lang;
		var $date;
		var $type;
		var $limiter_invitation;
		var $limiter_applicant;
		var $notifier_applicant;
		var $notifier_auteurs;
		var $statut;
		
		var $existe;


	    /**
	     * formulaire : constructeur
	     *
	     * @param int id_formulaire
	     * @return void
	     */
		function formulaire($id_formulaire=-1) {
			if ($id_formulaire == -1) {
				$this->id_formulaire		= -1;
				$id_rubrique = intval($_GET['id_rubrique']);
				if (!$id_rubrique) $id_rubrique = sql_getfetsel('id_rubrique', 'spip_rubriques', 'statut="publie"', 'id_rubrique', '1');
				$this->id_rubrique			= $id_rubrique;
				$this->titre				= _T('formulairesprive:nouveau_formulaire');
				$this->type					= 'une_seule_page';
				$this->limiter_invitation	= 'non';
				$this->limiter_applicant	= 'non';
				$this->notifier_applicant	= 'non';
				$this->notifier_auteurs		= 'non';
				$this->existe				= false;
			} else {
				$requete = sql_select('*', 'spip_formulaires', 'id_formulaire='.intval($id_formulaire));
				$formulaire = sql_fetch($requete);
				$this->id_formulaire		= intval($id_formulaire);
				$this->id_rubrique			= $formulaire['id_rubrique'];
				$this->titre				= $formulaire['titre'];
				$this->descriptif			= $formulaire['descriptif'];
				$this->chapo				= $formulaire['chapo'];
				$this->texte				= $formulaire['texte'];
				$this->merci				= $formulaire['merci'];
				$this->ps					= $formulaire['ps'];
				$this->lang					= $formulaire['lang'];
				$this->date					= $formulaire['date'];
				$this->type					= $formulaire['type'];
				$this->limiter_invitation	= $formulaire['limiter_invitation'];
				$this->limiter_applicant	= $formulaire['limiter_applicant'];
				$this->notifier_applicant	= $formulaire['notifier_applicant'];
				$this->notifier_auteurs		= $formulaire['notifier_auteurs'];
				$this->statut				= $formulaire['statut'];
				$this->existe				= true;
			}
		}
		

		/**
	     * enregistrer : mettre à jour ou insérer un formulaire
	     *
	     * @param int id_formulaire
	     * @return void
	     */
		function enregistrer() {
			if ($this->id_formulaire == -1) { // insertion
				$this->id_formulaire = sql_insertq('spip_formulaires', array(
																			'titre' => $this->titre,
																			'id_rubrique' => $this->id_rubrique,
																			'descriptif' => $this->descriptif,
																			'chapo' => $this->chapo,
																			'type' => $this->type,
																			'limiter_invitation' => $this->limiter_invitation,
																			'limiter_applicant' => $this->limiter_applicant,
																			'notifier_applicant' => $this->notifier_applicant,
																			'notifier_auteurs' => $this->notifier_auteurs,
																			'texte' => $this->texte,
																			'merci' => $this->merci,
																			'ps' => $this->ps,
																			'date' => "NOW()",
																			'maj' => "NOW()"
																		));
				$this->ajouter_auteur($GLOBALS['auteur_session']['id_auteur']);
				$this->existe = true;
			} else {
				$t = sql_fetsel('limiter_applicant, limiter_invitation', 'spip_formulaires', 'id_formulaire='.intval($this->id_formulaire));
				$limiter_applicant_vieux = $t['limiter_applicant'];
				$limiter_invitation_vieux = $t['limiter_invitation'];
				sql_updateq('spip_formulaires', array(
													'titre' => $this->titre,
													'id_rubrique' => $this->id_rubrique,
													'descriptif' => $this->descriptif,
													'chapo' => $this->chapo,
													'type' => $this->type,
													'limiter_invitation' => $this->limiter_invitation,
													'limiter_applicant' => $this->limiter_applicant,
													'notifier_applicant' => $this->notifier_applicant,
													'notifier_auteurs' => $this->notifier_auteurs,
													'texte' => $this->texte,
													'merci' => $this->merci,
													'ps' => $this->ps,
													'maj' => 'NOW()'
													), 'id_formulaire='.intval($this->id_formulaire));
			}
			$this->mettre_a_jour_rubriques();
			if ($this->limiter_applicant == 'oui' and $limiter_applicant_vieux == 'non') {
				$this->purger_reponses();
			}
			if ($this->limiter_invitation == 'oui' and $limiter_invitation_vieux == 'non') {
				$this->purger_reponses();
			}
			if ($this->limiter_applicant == 'oui' or $this->notifier_applicant == 'oui') {
				if ($this->est_vide()) {
					// il faut créer un bloc
					$bloc = new bloc($this->id_formulaire, -1);
					$bloc->enregistrer();
					// et une question de type 'email_applicant'
					$email_applicant = new email_applicant($this->id_formulaire, $bloc->id_bloc, -1);
					$email_applicant->enregistrer();
				} else {
					$verification = sql_select('Q.id_question', 'spip_questions AS Q INNER JOIN spip_blocs AS B ON B.id_bloc=Q.id_bloc', 'Q.type="email_applicant" AND B.id_formulaire='.intval($this->id_formulaire));
					if (sql_count($verification) == 0) {
						// on ajoute la question de type 'email_applicant' au premier bloc
						$id_premier_bloc = $this->recuperer_premier_bloc();
						$email_applicant = new email_applicant($this->id_formulaire, $id_premier_bloc, -1);
						$email_applicant->enregistrer();
					}
				}
			} else {
				// il faut changer la question 'email_applicant' et 'nom_applicant' en 'champ_texte' le cas échéant et 'abonnements' en 'cases_a_cocher'
				$blocs = $this->recuperer_blocs();
				foreach ($blocs as $id_bloc) {
					sql_updateq('spip_questions', array('type' => 'champ_texte', 'controle' => 'email'), 'id_bloc='.intval($id_bloc).' AND type="email_applicant"');
					sql_updateq('spip_questions', array('type' => 'champ_texte'), 'id_bloc='.intval($id_bloc).' AND type="nom_applicant"');
					sql_updateq('spip_questions', array('type' => 'cases_a_cocher'), 'id_bloc='.intval($id_bloc).' AND type="abonnements"');
				}
			}
		}
		

		function enregistrer_statut($statut) {
			$ancien_statut = $this->statut;
			switch ($statut) {
				case 'hors_ligne':
					sql_updateq('spip_formulaires', array('statut' => $statut, 'maj' => 'NOW()'), 'id_formulaire='.intval($this->id_formulaire));
					$redirection = generer_url_ecrire('formulaires', 'id_formulaire='.$this->id_formulaire, true);
					break;
				case 'en_ligne':
					sql_updateq('spip_formulaires', array('statut' => $statut, 'maj' => 'NOW()'), 'id_formulaire='.intval($this->id_formulaire));
					$redirection = generer_url_ecrire('formulaires', 'id_formulaire='.$this->id_formulaire, true);
					break;
				case 'export':
					$this->exporter();
					$redirection = false;
					break;
				case 'copie':
					$copie = new formulaire(-1);
					$copie->id_rubrique			= $this->id_rubrique;
					$copie->titre				= _T('formulairesprive:copie').' - '.$this->titre;
					$copie->descriptif			= $this->descriptif;
					$copie->chapo				= $this->chapo;
					$copie->texte				= $this->texte;
					$copie->merci				= $this->merci;
					$copie->ps					= $this->ps;
					$copie->lang				= $this->lang;
					$copie->date				= $this->date;
					$copie->type				= $this->type;
					$copie->limiter_invitation	= $this->limiter_invitation;
					$copie->limiter_applicant	= $this->limiter_applicant;
					$copie->notifier_applicant	= $this->notifier_applicant;
					$copie->notifier_auteurs	= $this->notifier_auteurs;
					$copie->statut				= $this->statut;
					$copie->existe				= true;
					$copie->enregistrer();
					$blocs = $this->recuperer_blocs();
					foreach ($blocs as $id_bloc) {
						$bloc		= new bloc($this->id_formulaire, $id_bloc);
						$copie_bloc	= new bloc($copie->id_formulaire, -1);
						$copie_bloc->ordre		= $bloc->ordre;
						$copie_bloc->titre		= $bloc->titre;
						$copie_bloc->descriptif	= $bloc->descriptif;
						$copie_bloc->texte		= $bloc->texte;
						$copie_bloc->enregistrer();
						$questions = $bloc->recuperer_questions();
						foreach ($questions as $id_question) {
							$question		= new question($this->id_formulaire, $bloc->id_bloc, $id_question);
							$copie_question	= new question($copie->id_formulaire, $copie_bloc->id_bloc, -1);
							$copie_question->ordre			= $question->ordre;
							$copie_question->titre			= $question->titre;
							$copie_question->descriptif		= $question->descriptif;
							$copie_question->type			= $question->type;
							$copie_question->obligatoire	= $question->obligatoire;
							$copie_question->controle		= $question->controle;
							$copie_question->enregistrer();
							$choix = $question->recuperer_choix_question();
							foreach ($choix as $id_choix_question) {
								$choix_question			= new choix_question($this->id_formulaire, $bloc->id_bloc, $question->id_question, $id_choix_question);
								$copie_choix_question	= new choix_question($copie->id_formulaire, $copie_bloc->id_bloc, $copie_question->id_question, -1);
								$copie_choix_question->ordre			= $choix_question->ordre;
								$copie_choix_question->titre			= $choix_question->titre;
								$copie_choix_question->id_rubrique		= $choix_question->id_rubrique;
								$copie_choix_question->id_auteur		= $choix_question->id_auteur;
								$copie_choix_question->enregistrer();
							}
						}
					}
					$res = sql_select('id_auteur', 'spip_auteurs_formulaires', 'id_formulaire='.intval($this->id_formulaire));
					while ($auteurs = sql_fetch($res)) {
						$copie->ajouter_auteur($auteurs['id_auteur']);
					}
					$redirection = generer_url_ecrire('formulaires', 'id_formulaire='.$copie->id_formulaire, true);
					break;
				case 'poubelle':
					$id_rubrique = $this->id_rubrique;
					$this->supprimer();
					$redirection = generer_url_ecrire('naviguer', 'id_rubrique='.$id_rubrique, true);
					break;
			}
			$this->mettre_a_jour_rubriques();
			return $redirection;
		}
		
		
	    /**
	     * mettre_a_jour_rubriques
	     *
	     * @return void
	     */
		function mettre_a_jour_rubriques() {
			calculer_rubriques();
			formulaires_trig_propager_les_secteurs($dummy);
			formulaires_calculer_langues_rubriques($dummy);
		}


	    /**
	     * mettre_a_jour_maj : pour les objets enfants
	     *
	     */
		function mettre_a_jour_maj() {
			sql_updateq('spip_formulaires', array('maj' => 'NOW()'), 'id_formulaire='.intval($this->id_formulaire));
		}
		

		/**
		 * est_vide
		 *
		 * @return boolean est_vide
		 **/
		function est_vide() {
			$blocs = $this->recuperer_blocs();
			if (count($blocs) == 0)
				return true;
			else
				return false;
		}


		/**
		 * possede_applications
		 *
		 * @return boolean possede_applications
		 **/
		function possede_applications() {
			if (sql_countsel('spip_applications', 'id_formulaire='.intval($this->id_formulaire)) > 0)
				return true;
			else
				return false;
		}


		/**
	     * supprimer
	     *
	     * @return void
	     */
	    function supprimer() {
			$this->limiter_invitation = 'non';
			$this->limiter_applicant = 'non';
			$this->enregistrer();
			$this->supprimer_blocs();
			$this->supprimer_auteurs();
			$this->supprimer_documents();
			$this->supprimer_mots_cles();
			$this->supprimer_applications();
			$this->supprimer_logos();
			if ($this->est_vide()) {
				sql_delete('spip_formulaires', 'id_formulaire='.intval($this->id_formulaire));
				$this->mettre_a_jour_rubriques();
			}
		}
		
		
		function supprimer_blocs() {
			$blocs = $this->recuperer_blocs();
			foreach ($blocs as $id_bloc) {
				$bloc = new bloc($this->id_formulaire, $id_bloc);
				$bloc->supprimer();
			}
		}
		
		
		function supprimer_auteurs() {
			$res = sql_select('id_auteur', 'spip_auteurs_formulaires', 'id_formulaire='.intval($this->id_formulaire));
			while ($auteurs = sql_fetch($res)) {
				$this->supprimer_auteur($auteurs['id_auteur']);
			}
		}
		
		
		function supprimer_documents() {
			// suppression documents
			$res = sql_select('D.*', 'spip_documents_liens AS DL INNER JOIN spip_documents AS D ON D.id_document=DL.id_document', 'DL.id_objet='.intval($this->id_formulaire).' AND DL.objet="formulaire"');
			$supprimer_document = charger_fonction('supprimer_document','action');
			while ($arr = sql_fetch($res))
				$supprimer_document($arr['id_document']);
			sql_delete('spip_documents_liens', 'id_objet='.intval($this->id_formulaire).' AND objet="formulaire"');
		}
		
		
		function supprimer_mots_cles() {
			sql_delete('spip_mots_formulaires', 'id_formulaire='.intval($this->id_formulaire));
		}
		
		
		function supprimer_applications() {
			$res = sql_select('id_applicant, id_application', 'spip_applications', 'id_formulaire='.intval($this->id_formulaire));
			while ($applications = sql_fetch($res)) {
				$application = new application($this->id_formulaire, $applications['id_applicant'], $applications['id_application']);
				$application->supprimer();
			}
		}
		
		
		function supprimer_logos() {
			include_spip('inc/chercher_logo');
			$logo_f = charger_fonction('chercher_logo', 'inc');
			if ($logo_on = $logo_f($this->id_formulaire, 'id_formulaire', 'on'))
				unlink($logo_on[0]);
			if ($logo_off = $logo_f($this->id_formulaire, 'id_formulaire', 'off'))
				unlink($logo_off[0]);
		}
		
		
	    /**
	     * recuperer_blocs
	     *
	     * @return array blocs
	     */
		function recuperer_blocs() {
			$blocs = array();
			$res = sql_select('id_bloc', 'spip_blocs', 'id_formulaire='.intval($this->id_formulaire), '', 'ordre');
			while ($arr = sql_fetch($res))
				$blocs[] = $arr['id_bloc'];
			return $blocs;
		}


	    /**
	     * recuperer_premier_bloc
	     *
	     * @return int id_premier_bloc
	     */
		function recuperer_premier_bloc() {
			$blocs = $this->recuperer_blocs();
			return $blocs[0];
		}


	    /**
	     * recuperer_dernier_bloc
	     *
	     * @return int id_dernier_bloc
	     */
		function recuperer_dernier_bloc() {
			$blocs = $this->recuperer_blocs();
			$i = count($blocs) - 1;
			return $blocs[$i];
		}


	    /**
	     * recuperer_bloc_apres
	     *
	     * @param int id_bloc
	     * @return int id_bloc_apres
	     */
		function recuperer_bloc_apres($id_bloc) {
			$blocs = $this->recuperer_blocs();
			$prochain = false;
			foreach ($blocs as $prochain_bloc) {
				if ($prochain) return $prochain_bloc;
				if ($prochain_bloc == $id_bloc) $prochain = true;
			}
			return -1;	
		}


	    /**
	     * recuperer_abonnements_disponibles
	     *
	     * @return array abonnements_disponibles
	     */
		function recuperer_abonnements_disponibles() {
			$abonnements_disponibles = array();
			$tab_blocs = $this->recuperer_blocs();
			foreach ($tab_blocs as $id_bloc) {
				$bloc = new bloc($this->id_formulaire, $id_bloc);
				$tab_questions = $bloc->recuperer_questions_de_type_abonnements();
				foreach ($tab_questions as $id_question) {
					$question = new question($this->id_formulaire, $id_bloc, $id_question);
					$tab_choix = $question->recuperer_choix_question();
					foreach ($tab_choix as $id_choix_question) {
						$choix_question = new choix_question($this->id_formulaire, $id_bloc, $id_question, $id_choix_question);
						$abonnements_disponibles[] = $choix_question->id_rubrique;
					}
				}
			}
			return $abonnements_disponibles;
		}


	    /**
	     * purger_reponses
	     *
	     * @return void
	     */
		function purger_reponses() {
			$blocs = $this->recuperer_blocs();
			foreach ($blocs as $id_bloc) {
				$bloc = new bloc($this->id_formulaire, $id_bloc);
				$questions = $bloc->recuperer_questions();
				foreach ($questions as $id_question) {
					$question = new question($this->id_formulaire, $bloc->id_bloc, $id_question);
					$question->supprimer_reponses();
				}
			}
			sql_delete('spip_applications', 'id_formulaire='.intval($this->id_formulaire));
		}


	    /**
	     * supprimer_auteur
	     *
	     * @param int id_auteur
	     */
		function supprimer_auteur($id_auteur) {
			$blocs = $this->recuperer_blocs();
			for ($i=0; $i<count($blocs); $i++) {
				$bloc = new bloc($this->id_formulaire, $blocs[$i]);
				$questions = $bloc->recuperer_questions_de_type_auteurs();
				for ($j=0; $j<count($questions); $j++) {
					$res = sql_select('id_choix_question', 'spip_choix_question', 'id_auteur='.intval($id_auteur).' AND id_question='.intval($questions[$j]));
					while ($arr = sql_fetch($res)) {
						$choix_question = new choix_question($this->id_formulaire, $bloc->id_bloc, $questions[$j], $arr['id_choix_question']);
						$choix_question->supprimer();
					}
				}
			}
			sql_delete('spip_auteurs_formulaires', 'id_auteur='.intval($id_auteur).' AND id_formulaire='.intval($this->id_formulaire));
		}


	    /**
	     * ajouter_auteur
	     *
	     * @param int id_auteur
	     */
		function ajouter_auteur($id_auteur) {
			if (sql_countsel('spip_auteurs_formulaires', 'id_auteur='.intval($id_auteur).' AND id_formulaire='.intval($this->id_formulaire)) == 0)
				sql_insertq('spip_auteurs_formulaires', array('id_auteur' => intval($id_auteur), 'id_formulaire' => intval($this->id_formulaire)));
		}
		
		
		function afficher() {
			$blocs = $this->recuperer_blocs();
			foreach ($blocs as $id_bloc) {
				$bloc = new bloc($this->id_formulaire, $id_bloc);
				$bloc->afficher();
			}
		}
		
		
		function exporter() {
			$resultats = array();
			$i = 0;
			$questions = sql_select('Q.*', 'spip_questions AS Q INNER JOIN spip_blocs AS B ON B.id_bloc=Q.id_bloc', 'B.id_formulaire='.intval($this->id_formulaire), '', 'B.ordre, Q.ordre');
			while ($question = spip_fetch_array($questions))
				$resultats[$i][] = typo($question['titre']);
			$i++;
			$applications = sql_select('*', 'spip_applications', 'id_formulaire='.intval($this->id_formulaire));
			while ($application = sql_fetch($applications)) {
				$questions = sql_select('Q.*', 'spip_questions AS Q INNER JOIN spip_blocs AS B ON B.id_bloc=Q.id_bloc', 'B.id_formulaire='.intval($this->id_formulaire), '', 'B.ordre, Q.ordre');
				while ($question = sql_fetch($questions)) {
					$reponses = sql_select('*', 'spip_reponses', 'id_question='.intval($question['id_question']).' AND id_application='.intval($application['id_application']));
					$tableau_reponses = array();
					$tableau_choix = array();
					while ($reponse = sql_fetch($reponses)) {
						$tableau_reponses[] = $reponse['valeur'];
					}
					switch ($question['type']) {
						case 'champ_texte':
						case 'zone_texte':
						case 'email_applicant':
						case 'nom_applicant':
						case 'fichier':
							$resultats[$i][] = implode(', ', $tableau_reponses);
							break;
						case 'boutons_radio':
						case 'cases_a_cocher':
						case 'liste':
						case 'liste_multiple':
						case 'abonnements':
						case 'auteurs':
							foreach ($tableau_reponses as $id_choix) {
								$choix = sql_getfetsel('titre', 'spip_choix_question', 'id_choix_question='.intval($id_choix));
								$tableau_choix[] = typo($choix);
							}
							$resultats[$i][] = implode(', ', $tableau_choix);
							break;
					}
				}
				$i++;
			}
			include_spip('surcharges_fonctions');
			surcharges_exporter_csv('resultats', $resultats);
		}


	}


	/**
	 * application
	 *
	 * @copyright Artégo
	 */

	class application {

	    var $id_application;
	    var $applicant;
	    var $formulaire;
		var $statut;
		var $note;

		var $existe = false;


		/**
		 * application : constructeur
		 *
		 * @param int id_applicant
		 * @param int id_formulaire
		 * @param int id_application
		 * @return void
		 **/
		function application($id_applicant, $id_formulaire, $id_application=-1) {
			$this->id_application	= $id_application;
			$this->applicant		= new applicant($id_applicant);
			$this->formulaire		= new formulaire($id_formulaire);
			if ($this->applicant->existe and $this->formulaire->existe) {
				if ($this->id_application == -1) {
					if ($this->formulaire->limiter_invitation == 'oui') {
						// on recherche l'application associée possédant le statut "valide"
						// pour un applicant ayant un email et un mdp
						$res = sql_select('APP.id_application AS id_application, APP.statut AS statut', 'spip_applications AS APP INNER JOIN spip_applicants AS A ON A.id_applicant=APP.id_applicant', 'A.id_applicant='.intval($this->applicant->id_applicant).' AND APP.id_formulaire='.intval($this->formulaire->id_formulaire).' AND APP.statut IN ("valide") AND A.email != "" AND A.mdp != ""');
						if (sql_count($res) == 1) {
							$t = sql_fetch($res);
							$this->id_application = $t['id_application'];
							$this->statut = $t['statut'];
							$this->existe = true;
						} else {
							$this->existe = false;
						}
					} else {
						if ($this->formulaire->limiter_applicant == 'oui') {
							// on recherche l'application associée possédant le statut "temporaire" ou "valide"
							$res = sql_select('id_application, statut', 'spip_applications', 'id_applicant='.intval($this->applicant->id_applicant).' AND id_formulaire='.intval($this->formulaire->id_formulaire).' AND statut IN ("temporaire","valide")');
							if (sql_count($res) == 1) {
								$t = sql_fetch($res);
								$this->id_application = $t['id_application'];
								$this->statut = $t['statut'];
								$this->existe = true;
							} else {
								$this->existe = false;
							}
						} else {
							// on recherche l'application associée possédant le statut "temporaire"
							$res = sql_select('id_application', 'spip_applications', 'id_applicant='.intval($this->applicant->id_applicant).' AND id_formulaire='.intval($this->formulaire->id_formulaire).' AND statut IN ("temporaire")');
							if (sql_count($res) == 1) {
								$t = sql_fetch($res);
								$this->id_application = $t['id_application'];
								$this->statut = 'temporaire';
								$this->existe = true;
							} else {
								$this->existe = false;
							}
						}
					}
				} else {
					$res = sql_select('id_application, statut', 'spip_applications', 'id_applicant='.intval($this->applicant->id_applicant).' AND id_formulaire='.intval($this->formulaire->id_formulaire).' AND id_application='.intval($this->id_application));
					if (sql_count($res) == 1) {
						$t = sql_fetch($res);
						$this->id_application = $t['id_application'];
						$this->statut = $t['statut'];
						$this->existe = true;
					} else {
						$this->existe = false;
					}
				}
			}
		}


		/**
		 * enregistrer
		 *
		 * @return void
		 **/
		function enregistrer() {
			if ($this->id_application == -1) {
				$this->id_application = sql_insertq('spip_applications', array(
																			'id_applicant' => $this->applicant->id_applicant, 
																			'id_formulaire' => $this->formulaire->id_formulaire,
																			'statut' => 'temporaire',
																			'maj' => 'NOW()'));
				$this->existe = true;
			}
		}


		/**
		 * est_vide
		 *
		 * @return boolean
		 **/
		function est_vide() {
			// l'applicant en question possede un email et un mdp
			$applicant = sql_select('id_applicant', 'spip_applicants', 'email!="" AND mdp!="" AND id_applicant='.intval($this->applicant->id_applicant));
			if (sql_count($applicant) == 0)
				return true;
			// aucune réponse ?
			$reponses = sql_select('id_reponse', 'spip_reponses', 'id_application='.intval($this->id_application));
			if (sql_count($reponses) > 0)
				return false;
			else
				return true;
		}


		/**
		 * valider_bloc_par_bloc_jusquau_bloc
		 *
		 * @param int id_bloc
		 * @param boolean tout
		 * @return array tableau
		 *				boolean resultat_bon
		 *				int id_bloc_erreur
		 *				array erreurs
		 **/
		function valider_bloc_par_bloc_jusquau_bloc($id_bloc, $tout) {
			$tableau = array('resultat_bon' => true, 'id_bloc_erreur' => 0, 'erreurs' => array());
			if ($this->formulaire->recuperer_premier_bloc() == $id_bloc and $this->formulaire->type == 'plusieurs_pages') {
				return $tableau;
			} else {
				$blocs = $this->formulaire->recuperer_blocs();
				foreach ($blocs as $valeur) {
					if (!$tout and ($valeur == $id_bloc)) break;
					$bloc = new bloc($this->formulaire->id_formulaire, $valeur);
					$questions_obligatoires = $bloc->recuperer_questions_obligatoires();
					foreach ($questions_obligatoires as $id_question) {
						$question = new question($this->formulaire->id_formulaire, $valeur, $id_question);
						if (!$this->controler_reponses($question)) {
							$tableau['resultat_bon'] = false;
							$tableau['id_bloc_erreur'] = $valeur;
							$tableau['erreurs'][] = $id_question;
						}
					}
				}
			}
			return $tableau;
		}


		/**
		 * controler_reponses
		 *
		 * @param objet question
		 * @return boolean resultat
		 **/
		function controler_reponses($question) {
			$res = sql_select('valeur', 'spip_reponses', 'id_question='.intval($question->id_question).' AND id_application='.intval($this->id_application).' AND valeur!=""', '', '', '1');
			if (sql_count($res) > 0) {
				$t = sql_fetch($res);
				$valeur = $t['valeur'];
				switch ($question->controle) {
					case 'non_vide':			return !empty($valeur);
					case 'email':				return ereg(_REGEXP_EMAIL, $valeur);
					case 'url':					return ereg("^(http|https)://", $valeur);
					case 'nombre':				return ereg("^[0-9]+$", $valeur);
					case 'date':				return ereg("([0-9]{2})/([0-9]{2})/([0-9]{4})", $valeur);
					case 'email_applicant':
						if (!ereg(_REGEXP_EMAIL, $valeur))
							return false;

						//$verification = sql_select('id_applicant', 'spip_applicants', 'email="'.addslashes($valeur).'"');
						$verification = sql_select(
											array(
												'spip_applicants.id_applicant'
											),
											array(
												'spip_applicants',
												'spip_applications'
											),
											array(
												'`spip_applications`.`id_applicant`=`spip_applicants`.`id_applicant`',
												'`spip_applicants`.`email`="'.addslashes($valeur).'"',
												'`spip_applications`.`id_formulaire`="'.addslashes($this->bloc->formulaire->id_formulaire).'"'/*,
												'`spip_applications`.`statut`="valide"'*/
											));
/*
 * Cette requête a été complètement ré-écrite
 * pour gérer les cas où une même personne répond à plusieurs formulaires
 * en voici un code SQL exemple complet: 

SELECT spip_applicants.id_applicant
FROM `spip_applications`, `spip_applicants` 
WHERE `spip_applications`.`id_applicant`=`spip_applicants`.`id_applicant`
      AND `spip_applicants`.`email`='jtlebi@gmail.com'
      AND `spip_applications`.`id_formulaire`=18
      AND `spip_applications`.`statut`='valide'
 */ 
						if (sql_count($verification) == 0) {
							return true;
						} else {
							$pop = sql_fetch($verification);
							$id_applicant = $pop['id_applicant'];
							if ($id_applicant == $this->applicant->id_applicant)
								return true;
							else
								return false;
						}
				}
			} else {
				return false;
			}
		}
		
		
		/**
		 * enregistrer_bloc
		 *
		 * @param int id_bloc
		 * @return void 
		 **/
		function enregistrer_bloc($id_bloc) {
			$bloc = new bloc($this->formulaire->id_formulaire, $id_bloc);
			$questions = $bloc->recuperer_questions();
			foreach ($questions as $id_question) {
				$question = new question($this->formulaire->id_formulaire, $id_bloc, $id_question);
				$reponse = _request('q_'.$id_question);
				if ($question->type != 'fichier') {
					if (empty($reponse) and !$question->obligatoire) 
						$question->supprimer_reponses_application($this->id_application);
				}
				switch ($question->type) {
					case 'email_applicant':
					case 'nom_applicant':
					case 'champ_texte':
					case 'zone_texte': // une seule valeur
						$valeur = _request('q_'.$id_question);
						if (!empty($valeur)) {
							$question->supprimer_reponses_application($this->id_application);
							sql_insertq('spip_reponses', array('id_question' => $id_question, 'id_application' => $this->id_application, 'valeur' => $valeur));
							if ($question->type == 'email_applicant' and ereg(_REGEXP_EMAIL, $valeur))
								$this->synchroniser_email_applicant($valeur);
							if ($question->type == 'nom_applicant')
								$this->synchroniser_nom_applicant($valeur);
						}
						break;
					case 'boutons_radio':
					case 'cases_a_cocher':
					case 'liste':
					case 'liste_multiple':
					case 'auteurs':
					case 'abonnements': // tableau
						$valeurs = _request('q_'.$id_question);
						if (is_array($valeurs)) {
							$question->supprimer_reponses_application($this->id_application);
							foreach ($valeurs as $valeur)
								if (!empty($valeur)) {
									sql_insertq('spip_reponses', array('id_question' => $id_question, 'id_application' => $this->id_application, 'valeur' => $valeur));
								}
						} else {
							if (!empty($valeurs)) {
								$question->supprimer_reponses_application($this->id_application);
								sql_insertq('spip_reponses', array('id_question' => $id_question, 'id_application' => $this->id_application, 'valeur' => $valeurs));
							}
						}
						if ($question->type == 'abonnements')
							$this->synchroniser_abonnements();
						break;
					case 'fichier':
						if (!empty($_FILES['q_'.$id_question])) {
							$titre			= $_FILES['q_'.$id_question]['name'];
							$chemin_fichier	= $_FILES['q_'.$id_question]['tmp_name'];
							$mime			= $_FILES['q_'.$id_question]['type'];
							$taille			= $_FILES['q_'.$id_question]['size'];
							if ($_FILES['q_'.$id_question]['error'] == 0 and in_array($mime, $question->mimes_type)) {
								$envers = array_flip($question->mimes_type);
								$extension = $question->extensions[$envers[$mime]];
								$nom_fichier = $this->id_application.'-'.mktime().'.'.$extension;
								$fichier = _DIR_FORMULAIRES.$nom_fichier;
								$taille_image = @getimagesize($chemin_fichier);
								$largeur = intval($taille_image[0]);
								$hauteur = intval($taille_image[1]);
								move_uploaded_file($chemin_fichier, $fichier);
								$id_document = sql_insertq('spip_documents', array('titre' => $titre, 'date' => 'NOW()', 'fichier' => $fichier, 'taille' => $taille, 'largeur' => $largeur, 'hauteur' => $hauteur, 'mode' => 'document', 'distant' => 'non', 'maj' => 'NOW()', 'extension' => $extension));
								sql_insertq('spip_documents_liens', array('id_document' => $id_document, 'id_objet' => $this->id_application, 'objet' => 'application'));
								sql_insertq('spip_reponses', array('id_question' => $id_question, 'id_application' => $this->id_application, 'valeur' => $id_document));
							}
						}
						$reponses = $question->recuperer_reponses_application($this->id_application);
						foreach ($reponses as $id_reponse) {
							$supprimer_document = _request('s_'.$id_reponse);
							if (!empty($supprimer_document)) {
								$verif = sql_select('D.*', 'spip_documents AS D INNER JOIN spip_documents_liens AS DL ON DL.id_document=D.id_document', 'DL.id_objet='.intval($this->id_application).' AND objet="application" AND D.id_document='.intval($supprimer_document));
								if (sql_count($verif) > 0) {
									$arr = sql_fetch($verif);
									@unlink($arr['fichier']);
									sql_delete('spip_documents', 'id_document='.intval($supprimer_document));
									sql_delete('spip_documents_liens', 'id_document='.intval($supprimer_document));
									sql_delete('spip_reponses', 'id_application='.intval($this->id_application).' AND id_reponse='.intval($id_reponse));
								}
							}
						}
						break;
				}
			}
			$this->mettre_a_jour_maj();
		}

		
		/**
		 * mettre_a_jour_maj
		 *
		 * @return void 
		 **/
		function mettre_a_jour_maj() {
			sql_updateq('spip_applications', array('maj' => 'NOW()'), 'id_application='.intval($this->id_application));
		}

		
		/**
		 * creer_invitation
		 *
		 * @return void 
		 **/
		function creer_invitation() {
			if ($this->formulaire->limiter_invitation == 'oui') {
				$this->id_application = sql_insertq('spip_applications', array('id_applicant' => $this->applicant->id_applicant, 'id_formulaire' => $this->formulaire->id_formulaire, 'statut' => 'valide', 'maj' => 'NOW()'));
			}
		}

		
		function envoyer_invitation() {
			include_spip('inc/facteur_classes');
			$objet			= recuperer_fond('notifications/notification_invitation_titre', array('mdp' => $this->applicant->mdp, 'email' => $this->applicant->email, 'id_formulaire' => $this->formulaire->id_formulaire, 'lang' => $lang));
			$message_html	= recuperer_fond('notifications/notification_invitation_html', array('mdp' => $this->applicant->mdp, 'email' => $this->applicant->email, 'id_formulaire' => $this->formulaire->id_formulaire, 'lang' => $lang));
			$message_texte	= recuperer_fond('notifications/notification_invitation_texte', array('mdp' => $this->applicant->mdp, 'email' => $this->applicant->email, 'id_formulaire' => $this->formulaire->id_formulaire, 'lang' => $lang));
			$notification	= new Facteur($this->applicant->email, $objet, $message_html, $message_texte);
			$notification->Send();
		}


		/**
		 * changer_statut
		 *
		 * @param string statut
		 * @return void 
		 **/
		function changer_statut($statut) {
			global $lang;

			if ($this->statut != $statut) {
				sql_updateq('spip_applications', array('statut' => $statut, 'maj' => 'NOW()'), 'id_application='.intval($this->id_application));
				$this->statut = $statut;
			}

			if ($this->formulaire->notifier_auteurs == 'oui' || $this->formulaire->notifier_applicant == 'oui' ) {
				include_spip('inc/facteur_classes');
				
				// réponses aux questions de type "auteurs"
				$resA = sql_select('A.email',
									'spip_auteurs AS A
									INNER JOIN spip_choix_question AS CQ ON CQ.id_auteur=A.id_auteur
									INNER JOIN spip_questions AS Q ON Q.id_question=CQ.id_question
									INNER JOIN spip_blocs AS B ON B.id_bloc=Q.id_bloc
									INNER JOIN spip_applications AS APP ON APP.id_formulaire=B.id_formulaire
									INNER JOIN spip_reponses AS REP ON REP.id_application=APP.id_application',
									'Q.type="auteurs"
										AND REP.id_question=Q.id_question
										AND REP.valeur=CQ.id_choix_question
										AND APP.id_application='.intval($this->id_application));
				$resB = sql_select('A.email',
									'spip_auteurs AS A
									INNER JOIN spip_auteurs_formulaires AS AF ON AF.id_auteur=A.id_auteur',
									'AF.id_formulaire='.intval($this->formulaire->id_formulaire));
				$tableau_emails = array();
				if (sql_count($resB) >= 1) {
					if (sql_count($resA) >= 1) {
						while ($arr = sql_fetch($resA))
							$tableau_emails[] = $arr['email'];
					} else {
						while ($arr = sql_fetch($resB))
							$tableau_emails[] = $arr['email'];
					}
				}	
				
				//mail de la personne qui répond
				$resC = sql_select('R.valeur AS email',
										'spip_reponses AS R
										INNER JOIN spip_questions AS Q ON Q.id_question=R.id_question
										INNER JOIN spip_blocs AS B ON B.id_bloc=Q.id_bloc',
										'Q.controle="email_applicant"
											AND B.id_formulaire='.intval($this->formulaire->id_formulaire).'
											AND R.id_application='.intval($this->id_application), '', '1');
				if (sql_count($resC) == 1) {
					$t = sql_fetch($resC);
				} else {
					$resD = sql_select('R.valeur AS email',
											'spip_reponses AS R
											INNER JOIN spip_questions AS Q ON Q.id_question=R.id_question
											INNER JOIN spip_blocs AS B ON B.id_bloc=Q.id_bloc',
											'Q.controle="email"
												AND B.id_formulaire='.intval($this->formulaire->id_formulaire).'
												AND R.id_application='.intval($this->id_application), '', '1');
					if (sql_count($resD) == 1) {
						$t = sql_fetch($resC);
					}
				}
					
				//envoye des mails
				if ($this->formulaire->notifier_auteurs == 'oui' && count($tableau_emails)>0) {
					$objet			= recuperer_fond('notifications/notification_nouveau_resultat_titre', array('id_formulaire' => $this->formulaire->id_formulaire, 'id_application' => $this->id_application, 'lang' => $lang));
					$message_html	= recuperer_fond('notifications/notification_nouveau_resultat_html', array('id_formulaire' => $this->formulaire->id_formulaire, 'id_application' => $this->id_application, 'lang' => $lang));
					$message_texte	= recuperer_fond('notifications/notification_nouveau_resultat_texte', array('id_formulaire' => $this->formulaire->id_formulaire, 'id_application' => $this->id_application, 'lang' => $lang));

					$email_premier_auteur = $tableau_emails[0];
					$notification_auteur = new Facteur($email_premier_auteur, $objet, $message_html, $message_texte);
					$tableau_emails_sans_premier = array_slice($tableau_emails, 1);
					foreach ($tableau_emails_sans_premier as $email_auteur)
						$notification_auteur->AddAddress($email_auteur);
					if (isset($t['email'])) {
						$notification_auteur->AddReplyTo($t['email']);
					}
					$message_envoye = $notification_auteur->Send();
				}
				if ($this->formulaire->notifier_applicant=='oui'  && isset($t['email']))  {
					
					$objet			= recuperer_fond('notifications/notification_nouveau_resultat_applicant_titre', array('id_formulaire' => $this->formulaire->id_formulaire, 'id_application' => $this->id_application, 'lang' => $lang));
					$message_html	= recuperer_fond('notifications/notification_nouveau_resultat_applicant_html', array('id_formulaire' => $this->formulaire->id_formulaire, 'id_application' => $this->id_application, 'lang' => $lang));
					$message_texte	= recuperer_fond('notifications/notification_nouveau_resultat_applicant_texte', array('id_formulaire' => $this->formulaire->id_formulaire, 'id_application' => $this->id_application, 'lang' => $lang));

					$notification_interroge = new Facteur($t['email'] ,$objet, $message_html, $message_texte);
					foreach ($tableau_emails as $mail)
						$notification_interroge->AddReplyTo($mail);
					$notification_interroge->From=$tableau_emails[0];
					$notification_interroge->FromName=$tableau_emails[0];
					$message_envoye=$notification_interroge->Send();
				}
				
			}
		}


		/**
		 * synchroniser_email_applicant
		 *
		 * @param string email
		 * @return void 
		 **/
		function synchroniser_email_applicant($email) {
			$this->applicant->email = $email;
			$this->applicant->enregistrer();
		}
		
		
		/**
		 * synchroniser_nom_applicant
		 *
		 * @param string nom
		 * @return void 
		 **/
		function synchroniser_nom_applicant($nom) {
			$this->applicant->nom = $nom;
			$this->applicant->enregistrer();
		}
		
		
		/**
		 * synchroniser_abonnements
		 *
		 * @param int id_choix_question
		 * @return void 
		 **/
		function synchroniser_abonnements() {
			include_spip('lettres_fonctions');

			$rubriques = array();

			// on récupère les questions de type "abonnements"
			$abonnements_disponibles = $this->formulaire->recuperer_abonnements_disponibles();

			// a-t'on une réponse ?
			foreach ($abonnements_disponibles as $id_rubrique) {
				$res = sql_select('REP.id_reponse',
									'spip_reponses AS REP
									INNER JOIN spip_choix_question AS CH ON CH.id_question=REP.id_question',
									'CH.id_rubrique='.intval($id_rubrique).'
										AND REP.id_application='.intval($this->id_application).'
										AND REP.valeur=CH.id_choix_question');
				if (sql_count($res) > 0)
					$rubriques[] = $id_rubrique;
			}

			if (ereg(_REGEXP_EMAIL, $this->applicant->email)) {

				$abonne = new abonne(0, $this->applicant->email);

				if ($abonne->existe) {

					$abonnements = $abonne->recuperer_abonnements();

					$abonnements = array_intersect($abonnements_disponibles, $abonnements);

					$desabonnements = array_diff($abonnements, $rubriques);
					if (!empty($desabonnements)) { // on désinscrit s'il y a des différences
						foreach ($desabonnements as $id_rubrique) {
							$abonne->valider_desabonnement($id_rubrique);
						}
					}
					$abonnements = array_diff($rubriques, $abonnements);
					if (!empty($abonnements)) {
						foreach ($abonnements as $id_rubrique) {
							$abonne->enregistrer_abonnement($id_rubrique);
							$abonne->valider_abonnement($id_rubrique);
						}
					}

					$abonne->supprimer_si_zero_abonnement();

				} else {

					if (!empty($rubriques)) {
						$abonne->enregistrer();
						foreach ($rubriques as $id_rubrique) {
							$abonne->enregistrer_abonnement($id_rubrique);
							$abonne->valider_abonnement($id_rubrique);
						}
					}

				}
			}
		}
		
		
		/**
		 * supprimer
		 *
		 * @return void
		 **/
		function supprimer() {
			$this->supprimer_reponses();
			sql_delete('spip_applications', 'id_application='.intval($this->id_application));
		}


		/**
		 * supprimer_reponses
		 *
		 * @return void
		 **/
		function supprimer_reponses() {
			$res = sql_select('id_reponse', 'spip_reponses', 'id_application='.intval($this->id_application));
			while ($reponses = sql_fetch($res)) {
				$reponse = new reponse($reponses['id_reponse']);
				$reponse->supprimer();
			}
		}


		function exporter() {
			$resultats = array();
			$i = 0;
			$questions = sql_select('Q.*', 'spip_questions AS Q INNER JOIN spip_blocs AS B ON B.id_bloc=Q.id_bloc', 'B.id_formulaire='.intval($this->formulaire->id_formulaire), '', 'B.ordre, Q.ordre');
			while ($question = spip_fetch_array($questions))
				$resultats[$i][] = typo($question['titre']);
			$i++;
			$applications = sql_select('*', 'spip_applications', 'id_application='.intval($this->id_application));
			while ($application = sql_fetch($applications)) {
				$questions = sql_select('Q.*', 'spip_questions AS Q INNER JOIN spip_blocs AS B ON B.id_bloc=Q.id_bloc', 'B.id_formulaire='.intval($this->formulaire->id_formulaire), '', 'B.ordre, Q.ordre');
				while ($question = sql_fetch($questions)) {
					$reponses = sql_select('*', 'spip_reponses', 'id_question='.intval($question['id_question']).' AND id_application='.intval($application['id_application']));
					$tableau_reponses = array();
					$tableau_choix = array();
					while ($reponse = sql_fetch($reponses)) {
						$tableau_reponses[] = $reponse['valeur'];
					}
					switch ($question['type']) {
						case 'champ_texte':
						case 'zone_texte':
						case 'email_applicant':
						case 'nom_applicant':
						case 'fichier':
							$resultats[$i][] = implode(', ', $tableau_reponses);
							break;
						case 'boutons_radio':
						case 'cases_a_cocher':
						case 'liste':
						case 'liste_multiple':
						case 'abonnements':
						case 'auteurs':
							foreach ($tableau_reponses as $id_choix) {
								$choix = sql_getfetsel('titre', 'spip_choix_question', 'id_choix_question='.intval($id_choix));
								$tableau_choix[] = typo($choix);
							}
							$resultats[$i][] = implode(', ', $tableau_choix);
							break;
					}
				}
				$i++;
			}
			include_spip('surcharges_fonctions');
			surcharges_exporter_csv('resultats', $resultats);
		}


	}


	/**
	 * bloc
	 *
	 * @copyright Artégo
	 */

	class bloc {

	    var $id_bloc;
	    var $formulaire;
		var $ordre;
		var $titre;
		var $descriptif;
		var $texte;


		/**
		 * bloc : constructeur
		 *
		 * @param int id_formulaire
		 * @param int id_bloc
		 * @return void
		 **/
		function bloc($id_formulaire, $id_bloc=-1) {
			$this->id_bloc = $id_bloc;
			$this->formulaire = new formulaire($id_formulaire);
			if ($this->id_bloc == -1) {
				$this->titre	= _T('formulairesprive:nouveau_bloc');
				$autres_blocs	= $this->recuperer_autres_blocs();
				$this->ordre	= count($autres_blocs);
			} else {
				$arr = sql_fetsel('*', 'spip_blocs', 'id_bloc='.intval($this->id_bloc));
				$this->ordre		= $arr['ordre'];
				$this->titre		= $arr['titre'];
				$this->descriptif	= $arr['descriptif'];
				$this->texte		= $arr['texte'];
			}
		}


		/**
	     * enregistrer : mettre à jour ou insérer un bloc
	     *
	     * @param int id_formulaire
	     * @return void
	     */
		function enregistrer() {
			if ($this->id_bloc == -1) { // insertion
				// on met le bloc en dernière position, il est mis à jour juste après $this->enregistrer
				$this->id_bloc = sql_insertq('spip_blocs', array('id_formulaire' => $this->formulaire->id_formulaire, 'ordre' => $this->ordre, 'titre' => $this->titre, 'descriptif' => $this->descriptif, 'texte' => $this->texte));
			} else {
				sql_updateq('spip_blocs', array('titre' => $this->titre, 'descriptif' => $this->descriptif, 'texte' => $this->texte), 'id_bloc='.intval($this->id_bloc));
			}
			$this->formulaire->mettre_a_jour_maj();
		}
		

		/**
		 * est_vide
		 *
		 * @return boolean est_vide
		 **/
		function est_vide() {
			$questions = $this->recuperer_questions();
			if (count($questions) == 0)
				return true;
			else
				return false;
		}


		/**
		 * supprimer
		 *
		 * @return void
		 **/
		function supprimer() {
			$this->supprimer_questions();
			$this->changer_ordre('dernier');
			if ($this->est_vide())
				sql_delete('spip_blocs', 'id_bloc='.intval($this->id_bloc));
		}


		/**
		 * supprimer_questions
		 *
		 * @return void
		 **/
		function supprimer_questions() {
			$questions = $this->recuperer_questions();
			foreach ($questions as $id_question) {
				$question = new question($this->formulaire->id_formulaire, $this->id_bloc, $id_question);
				$question->supprimer();
			}
		}
		
		
	    /**
	     * recuperer_questions
	     *
	     * @param boolean restreindre
	     * @return array questions
	     */
		function recuperer_questions($restreindre = false) {
			if ($restreindre)
				$plus = 'AND type IN ("boutons_radio","cases_a_cocher","liste","liste_multiple","abonnements","auteurs") ';
			else
				$plus = '';
			$questions = array();
			$res = sql_select('id_question', 'spip_questions', 'id_bloc='.intval($this->id_bloc).' '.$plus, '', 'ordre');
			while ($arr = sql_fetch($res))
				$questions[] = $arr['id_question'];
			return $questions;
		}


	    /**
	     * recuperer_questions_de_type_abonnements
	     *
	     * @return array questions
	     */
		function recuperer_questions_de_type_abonnements($inverse = false) {
			$questions = array();
			if (!$inverse)
				$res = sql_select('id_question', 'spip_questions', 'id_bloc='.intval($this->id_bloc).' AND type="abonnements"', '', 'ordre');
			else
				$res = sql_select('id_question', 'spip_questions', 'id_bloc='.intval($this->id_bloc).' AND type IN ("boutons_radio","cases_a_cocher","liste","liste_multiple","auteurs")', '', 'ordre');
			while ($arr = sql_fetch($res))
				$questions[] = $arr['id_question'];
			return $questions;
		}


	    /**
	     * recuperer_questions_de_type_auteurs
	     *
	     * @return array questions
	     */
		function recuperer_questions_de_type_auteurs($inverse = false) {
			$questions = array();
			if (!$inverse)
				$res = sql_select('id_question', 'spip_questions', 'id_bloc='.intval($this->id_bloc).' AND type="auteurs"', '', 'ordre');
			else
				$res = sql_select('id_question', 'spip_questions', 'id_bloc='.intval($this->id_bloc).' AND type IN ("boutons_radio","cases_a_cocher","liste","liste_multiple","abonnements")', '', 'ordre');
			while ($arr = sql_fetch($res))
				$questions[] = $arr['id_question'];
			return $questions;
		}


	    /**
	     * recuperer_questions_obligatoires
	     *
	     * @param boolean restreindre
	     * @return array questions
	     */
		function recuperer_questions_obligatoires($restreindre = false) {
			if ($restreindre)
				$plus = 'AND type IN ("boutons_radio","cases_a_cocher","liste") ';
			else
				$plus = '';
			$questions = array();
			$res = sql_select('id_question', 'spip_questions', 'id_bloc='.intval($this->id_bloc).' AND obligatoire="1" '.$plus, '', 'ordre');
			while ($arr = sql_fetch($res))
				$questions[] = $arr['id_question'];
			return $questions;
		}


	    /**
	     * recuperer_autres_blocs
	     *
	     * @return array autres_blocs
	     */
		function recuperer_autres_blocs() {
			$tableau_blocs = $this->formulaire->recuperer_blocs();
			// on supprime l'id en cours du tableau
			$tableau_sans_id_a_inserer = array();
			foreach ($tableau_blocs as $id) {
				if ($id != $this->id_bloc)
					$tableau_sans_id_a_inserer[] = $id;
			}
			return $tableau_sans_id_a_inserer;
		}


	    /**
	     * changer_ordre
	     *
	     * @param string/int position
	     */
		function changer_ordre($position) {
			$tableau_blocs = $this->recuperer_autres_blocs();
			$tableau_ordonne = formulaires_ordonner($tableau_blocs, $this->id_bloc, $position);
			foreach ($tableau_ordonne as $cle => $valeur)
				sql_updateq('spip_blocs', array('ordre' => $cle), 'id_bloc='.intval($valeur));
			$this->ordre = sql_getfetsel('ordre', 'spip_blocs', 'id_bloc='.intval($this->id_bloc));
		}
		
		
		/**
		 * possede_email_applicant
		 *
		 * @return boolean possede_email_applicant
		 **/
		function possede_email_applicant() {
			$res = sql_select('*', 'spip_questions', 'type="email_applicant" AND id_bloc='.intval($this->id_bloc));
			if (sql_count($res) == 0)
				return true;
			else
				return false;
		}


	    /**
	     * afficher
	     *
	     * @return void
	     */
		function afficher() {
			global $spip_lang_right;

			$taille_blocs = count($this->formulaire->recuperer_blocs());
			$questions = $this->recuperer_questions();
			$nb_questions = count($questions);

			if ($this->ordre > 0)	$monter = '<a class="editer_position_bloc" href="'.generer_url_action('editer_position_bloc', 'id_formulaire='.$this->formulaire->id_formulaire.'&id_bloc='.$this->id_bloc.'&position='.(($this->ordre)-1), true, true).'">'.http_img_pack(_DIR_PLUGIN_FORMULAIRES.'/prive/images/monter-bleu.png', '', 'width="16" height="16"').'</a>';
			else 					$monter = http_img_pack("rien.gif", "", "width='16' height='16'");
			if ($this->ordre < ($taille_blocs-1))	$descendre = '<a class="editer_position_bloc" href="'.generer_url_action('editer_position_bloc', 'id_formulaire='.$this->formulaire->id_formulaire.'&id_bloc='.$this->id_bloc.'&position='.(($this->ordre)+1), true, true).'">'.http_img_pack(_DIR_PLUGIN_FORMULAIRES."/prive/images/descendre-bleu.png", "", "width='16' height='16'").'</a>';
			else 									$descendre = http_img_pack("rien.gif", "", "width='16' height='16'");

			$editer = "<a href='".generer_url_ecrire("blocs_edit","id_formulaire=".$this->formulaire->id_formulaire."&id_bloc=".$this->id_bloc)."'>".http_img_pack(_DIR_PLUGIN_FORMULAIRES."/prive/images/editer.png", "", "width='16' height='16'")."</a>";
			$bord = http_img_pack("rien.gif", "", "width='7' height='16'");
			$espace = http_img_pack("rien.gif", "", "width='16' height='16'");

			if ($this->possede_email_applicant())	$supprimer = '<a class="supprimer_bloc" href="'.generer_url_action('supprimer_bloc', 'id_formulaire='.$this->formulaire->id_formulaire.'&id_bloc='.$this->id_bloc, true, true).'">'.http_img_pack(_DIR_PLUGIN_FORMULAIRES.'/prive/images/poubelle.png', '', 'width="16" height="16"').'</a>';
			else 									$supprimer = $espace;

			$titre = '<a href="'.generer_url_ecrire("blocs_edit","id_formulaire=".$this->formulaire->id_formulaire."&id_bloc=".$this->id_bloc).'" style="color: #000;">'.typo($this->titre).'</a>';
			$titre.= '<div style="position: absolute; top: 4px; right: 3px;">'.$monter.$descendre.$espace.$editer.$espace.$supprimer.$bord.'</div>';

			echo debut_cadre_trait_couleur(_DIR_PLUGIN_FORMULAIRES.'/prive/images/bloc.png', true, '', $titre);

			if (strlen($this->descriptif) > 0) {
				echo '<div style="padding: 10px;">';
				echo typo($this->descriptif);
				echo "</div>";
			}

			if ($nb_questions > 0) {
				echo "<div class='liste'>\n";
				echo "<table width='100%' cellpadding='3' cellspacing='0' border='0' background=''>\n";
			}
			foreach ($questions as $id_question) {
				$question = new question($this->formulaire->id_formulaire, $this->id_bloc, $id_question);
				$question->afficher();
			}
			if ($nb_questions > 0) {
				echo "</table>\n";
				echo "</div>\n";
			}

			if (strlen($this->texte) > 0) {
				echo "<div align='justify' style='padding: 10px;'>";
				echo typo($this->texte);
				echo "</div>";
			}

			echo icone_inline(_T('formulairesprive:creer_nouvelle_question'), generer_url_ecrire("questions_edit","id_formulaire=".$this->formulaire->id_formulaire."&id_bloc=".$this->id_bloc."&new=oui"), _DIR_PLUGIN_FORMULAIRES.'/prive/images/question.png', "creer.gif", $spip_lang_right);

			echo fin_cadre_trait_couleur(true);
		}


	}


	/**
	 * question
	 *
	 * @copyright Artégo
	 */

	class question {

	    var $id_question;
	    var $bloc;
		var $ordre;
		var $titre;
		var $descriptif;
		var $id_ancien_bloc;

		var $mimes_type = array();
		var $extensions = array();
		var $fichiers = array();


		/**
		 * question : constructeur
		 *
		 * @param int id_formulaire
		 * @param int id_bloc
		 * @param int id_question
		 * @return void
		 **/
		function question($id_formulaire, $id_bloc, $id_question=-1) {
			$this->id_question = $id_question;
			$this->bloc = new bloc($id_formulaire, $id_bloc);
			if ($this->id_question == -1) {
				$this->titre		= _T('formulairesprive:nouvelle_question');
				$autres_questions	= $this->recuperer_autres_questions();
				$this->ordre		= count($autres_questions);
				$this->type			= 'champ_texte';
				$this->obligatoire	= false;
			} else {
				$arr = sql_fetsel('*', 'spip_questions', 'id_question='.intval($this->id_question));
				$this->ordre		= $arr['ordre'];
				$this->titre		= $arr['titre'];
				$this->descriptif	= $arr['descriptif'];
				$this->type			= $arr['type'];
				$this->obligatoire	= $arr['obligatoire'];
				$this->controle		= $arr['controle'];
				$this->mime			= $arr['mime'];
				if ($this->mime) {
					$i = 0;
					$mimes = unserialize($this->mime);
					if (is_array($mimes)) {
						foreach ($mimes as $mime) {
							$t = sql_fetsel('*', 'spip_types_documents', 'mime_type="'.$mime.'"');
							$this->mimes_type[$i]	= $t['mime_type'];
							$this->extensions[$i]	= $t['extension'];
							$this->fichiers[$i]		= $t['titre'];
							$i++;
						}
					}
				}
			}
		}


		/**
		 * enregistrer
		 *
		 * @return void
		 **/
		function enregistrer() {
			if ($this->id_question == -1) { // insertion
				switch ($this->type) {
					case 'boutons_radio':
					case 'cases_a_cocher':
					case 'liste':
					case 'liste_multiple':
					case 'date':
					case 'abonnements':
					case 'auteurs':
						$this->controle = 'non_vide';
						break;
				}
				// on met la question en dernière position, elle est mise à jour juste après $this->enregistrer
				$this->id_question = sql_insertq('spip_questions', array(
																		'id_bloc' => $this->bloc->id_bloc,
																		'ordre' => $this->ordre,
																		'type' => $this->type,
																		'mime' => $this->mime,
																		'obligatoire' => $this->obligatoire,
																		'controle' => $this->controle,
																		'titre' => $this->titre,
																		'descriptif' => $this->descriptif
																		));
			} else {
				$t = sql_fetsel('type, id_bloc', 'spip_questions', 'id_question='.intval($this->id_question));
				$ancien_type = $t['type'];
				$id_ancien_bloc = $t['id_bloc'];
				if ($ancien_type != $this->type) {
					if	(	
							(
								($this->type == 'champ_texte') 
							or	($this->type == 'zone_texte')
							or	($this->type == 'date')
							or	($this->type == 'separateur')
							)
						and
							(
								($ancien_type == 'boutons_radio') 
							or	($ancien_type == 'cases_a_cocher')
							or	($ancien_type == 'liste')
							or	($ancien_type == 'liste_multiple')
							or	($ancien_type == 'abonnements')
							or	($ancien_type == 'auteurs')
							)
						)
						$this->supprimer_choix_question();
					if	(	
							$this->type == 'abonnements' 
						and
							(
								($ancien_type == 'boutons_radio') 
							or	($ancien_type == 'cases_a_cocher')
							or	($ancien_type == 'liste')
							or	($ancien_type == 'liste_multiple')
							)
						)
						$this->supprimer_choix_question();
				}
				sql_updateq('spip_questions', array(
													'id_bloc' => $this->bloc->id_bloc,
													'titre' => $this->titre,
													'descriptif' => $this->descriptif,
													'type' => $this->type,
													'mime' => $this->mime,
													'obligatoire' => $this->obligatoire,
													'controle' => $this->controle
													),
													'id_question='.intval($this->id_question));
			}
			$this->id_ancien_bloc = $id_ancien_bloc;
			$this->bloc->formulaire->mettre_a_jour_maj();
		}
		
		
		/**
		 * supprimable
		 *
		 * @return boolean supprimable
		 **/
		function supprimable() {
			if ($this->type == 'email_applicant')
				return false;
			else
				return true;
		}


		/**
		 * supprimer
		 *
		 * @return void
		 **/
		function supprimer() {
			if ($this->supprimable()) {
				$this->supprimer_choix_question();
				$this->supprimer_reponses();
				$this->changer_ordre('dernier');
				sql_delete('spip_questions', 'id_question='.intval($this->id_question));
			}
		}


		/**
		 * supprimer_choix_question
		 *
		 * @return void
		 **/
		function supprimer_choix_question() {
			$choix_question = $this->recuperer_choix_question();
			foreach ($choix_question as $id_choix) {
				$choix = new choix_question($this->bloc->formulaire->id_formulaire, $this->bloc->id_bloc, $this->id_question, $id_choix);
				$choix->supprimer();
			}
		}
		
		
		/**
		 * supprimer_reponses
		 *
		 * @return void
		 **/
		function supprimer_reponses() {
			$reponses = $this->recuperer_reponses();
			foreach ($reponses as $id_reponse) {
				$reponse = new reponse($id_reponse);
				$reponse->supprimer();
			}
		}
		
		
		/**
		 * supprimer_reponses_application
		 *
		 * @param int id_application
		 * @return void
		 **/
		function supprimer_reponses_application($id_application) {
			$reponses = $this->recuperer_reponses_application($id_application);
			foreach ($reponses as $id_reponse) {
				$reponse = new reponse($id_reponse);
				$reponse->supprimer();
			}
		}
		
		
	    /**
	     * recuperer_choix_question
	     *
	     * @return array choix_question
	     */
		function recuperer_choix_question() {
			$choix_question = array();
			$res = sql_select('id_choix_question', 'spip_choix_question', 'id_question='.intval($this->id_question), '', 'ordre');
			while ($arr = sql_fetch($res))
				$choix_question[] = $arr['id_choix_question'];
			return $choix_question;
		}


	    /**
	     * recuperer_autres_questions
	     *
	     * @param boolean restreindre
	     * @return array autres_questions
	     */
		function recuperer_autres_questions($restreindre = false) {
			$tableau_questions = $this->bloc->recuperer_questions($restreindre);
			// on supprime l'id en cours du tableau
			$tableau_sans_id_a_inserer = array();
			foreach ($tableau_questions as $id) {
				if ($id != $this->id_question)
					$tableau_sans_id_a_inserer[] = $id;
			}
			return $tableau_sans_id_a_inserer;
		}


	    /**
	     * recuperer_reponses
	     *
	     * @return array reponses
	     */
		function recuperer_reponses() {
			$reponses = array();
			$res = sql_select('id_reponse', 'spip_reponses', 'id_question='.intval($this->id_question));
			while ($arr = sql_fetch($res))
				$reponses[] = $arr['id_reponse'];
			return $reponses;
		}


	    /**
	     * recuperer_reponses_application
	     *
		 * @param int id_application
	     * @return array reponses
	     */
		function recuperer_reponses_application($id_application) {
			$reponses = array();
			$res = sql_select('id_reponse', 'spip_reponses', 'id_question='.intval($this->id_question).' AND id_application='.intval($id_application));
			while ($arr = sql_fetch($res))
				$reponses[] = $arr['id_reponse'];
			return $reponses;
		}


	    /**
	     * changer_ordre
	     *
	     * @param string/int position
	     */
		function changer_ordre($position) {
			if ($this->id_ancien_bloc != $this->bloc->id_bloc) { // réordonner l'ancien bloc
				$ancien_bloc = new bloc($this->bloc->formulaire->id_formulaire, $this->id_ancien_bloc);
				$anciennes_autres_questions = $ancien_bloc->recuperer_questions();
				$tableau_ordonne = formulaires_ordonner($anciennes_autres_questions, 0, 0);
				if (is_array($tableau_ordonne)) {
					foreach ($tableau_ordonne as $cle => $valeur)
						sql_updateq('spip_questions', array('ordre' => $cle), 'id_question='.intval($valeur));
				}
			}
			$tableau_questions = $this->recuperer_autres_questions();
			$tableau_ordonne = formulaires_ordonner($tableau_questions, $this->id_question, $position);
			foreach ($tableau_ordonne as $cle => $valeur)
				sql_updateq('spip_questions', array('ordre' => $cle), 'id_question='.intval($valeur));
			$this->ordre = sql_getfetsel('ordre', 'spip_questions', 'id_question='.intval($this->id_question));
		}
		
		
	    /**
	     * afficher
	     *
	     * @return void
	     */
		function afficher() {
			global $spip_lang_right;

			$taille_questions = count($this->bloc->recuperer_questions());

			$espace = http_img_pack("rien.gif", "", "width='16' height='16'");

			if ($this->ordre > 0)	$monter = '<a class="editer_position_question" href="'.generer_url_action("editer_position_question", "id_formulaire=".$this->bloc->formulaire->id_formulaire."&id_bloc=".$this->bloc->id_bloc."&id_question=".$this->id_question."&position=".(($this->ordre)-1), true, true).'">'.http_img_pack(_DIR_PLUGIN_FORMULAIRES."/prive/images/monter-16.png", "", "width='16' height='16'")."</a>";
			else 					$monter = $espace;
			if ($this->ordre < ($taille_questions-1))	$descendre = '<a class="editer_position_question" href="'.generer_url_action("editer_position_question", "id_formulaire=".$this->bloc->formulaire->id_formulaire."&id_bloc=".$this->bloc->id_bloc."&id_question=".$this->id_question."&position=".(($this->ordre)+1), true, true).'">'.http_img_pack(_DIR_PLUGIN_FORMULAIRES."/prive/images/descendre-16.png", "", "width='16' height='16'")."</a>";
			else 										$descendre = $espace;

			$editer = "<a href='".generer_url_ecrire("questions_edit","id_formulaire=".$this->bloc->formulaire->id_formulaire."&id_bloc=".$this->bloc->id_bloc."&id_question=".$this->id_question)."'>".http_img_pack(_DIR_PLUGIN_FORMULAIRES."/prive/images/editer.png", "", "width='16' height='16'")."</a>";

			if ($this->supprimable())	$supprimer = '<a class="supprimer_question" href="'.generer_url_action("supprimer_question", "id_formulaire=".$this->bloc->formulaire->id_formulaire."&id_bloc=".$this->bloc->id_bloc."&id_question=".$this->id_question, true, true).'">'.http_img_pack(_DIR_PLUGIN_FORMULAIRES."/prive/images/poubelle.png", "", "width='16' height='16'")."</a>";
			else 						$supprimer = $espace;

			if ($this->obligatoire) $image_obligatoire = http_img_pack(_DIR_PLUGIN_FORMULAIRES."/prive/images/obligatoire.png", "", "width='16' height='16'");
			else 					$image_obligatoire = $espace;

			switch ($this->type) {
				case 'champ_texte':
					if ($this->obligatoire) {
						switch ($this->controle) {
							case 'non_vide':
								$image_type = http_img_pack(_DIR_PLUGIN_FORMULAIRES."/prive/images/".$this->type.".png", "", "width='16' height='16'");
								break;
							case 'url':
								$image_type = http_img_pack(_DIR_PLUGIN_FORMULAIRES."/prive/images/lien.png", "", "width='16' height='16'");
								break;
							case 'nombre':
								$image_type = http_img_pack(_DIR_PLUGIN_FORMULAIRES."/prive/images/champ_numerique.png", "", "width='16' height='16'");
								break;
							case 'email':
								$image_type = http_img_pack(_DIR_PLUGIN_FORMULAIRES."/prive/images/email.png", "", "width='16' height='16'");
								break;
							case 'date':
								$image_type = http_img_pack(_DIR_PLUGIN_FORMULAIRES."/prive/images/date.png", "", "width='16' height='16'");
								break;
						}
					} else {
						$image_type = http_img_pack(_DIR_PLUGIN_FORMULAIRES."/prive/images/".$this->type.".png", "", "width='16' height='16'");
					}
					$ajouter = $espace;
					break;
				case 'boutons_radio':
				case 'cases_a_cocher':
				case 'liste':
				case 'liste_multiple':
				case 'abonnements':
					$image_type = http_img_pack(_DIR_PLUGIN_FORMULAIRES."/prive/images/".$this->type.".png", "", "width='16' height='16'");
					$ajouter = '<a href="'.generer_url_ecrire('choix_question_edit', 'id_formulaire='.$this->bloc->formulaire->id_formulaire.'&id_bloc='.$this->bloc->id_bloc.'&id_question='.$this->id_question.'&new=oui').'">'.http_img_pack(_DIR_PLUGIN_FORMULAIRES."/prive/images/ajouter_choix_".$this->type.".png", "", "width='16' height='16'").'</a>';
					break;
				case 'email_applicant':
					$ajouter = $espace;
					$image_type = http_img_pack(_DIR_PLUGIN_FORMULAIRES."/prive/images/".$this->type.".png", "", "width='16' height='16'");
					break;
				case 'nom_applicant':
					$ajouter = $espace;
					$image_type = http_img_pack(_DIR_PLUGIN_FORMULAIRES."/prive/images/".$this->type.".png", "", "width='16' height='16'");
					break;
				case 'auteurs':
					$image_type = http_img_pack(_DIR_PLUGIN_FORMULAIRES."/prive/images/liste_auteurs.png", "", "width='16' height='16'");
					$ajouter = '<a href="'.generer_url_ecrire('choix_question_edit', 'id_formulaire='.$this->bloc->formulaire->id_formulaire.'&id_bloc='.$this->bloc->id_bloc.'&id_question='.$this->id_question.'&new=oui').'">'.http_img_pack(_DIR_PLUGIN_FORMULAIRES."/prive/images/ajouter_choix_".$this->type.".png", "", "width='16' height='16'").'</a>';
					break;
				default:
					$ajouter = $espace;
					$image_type = http_img_pack(_DIR_PLUGIN_FORMULAIRES."/prive/images/".$this->type.".png", "", "width='16' height='16'");
					break;
			}

			echo "<tr class='tr_liste' valign='top'>\n";
			echo "<td width='16' class='arial11'>\n";
			echo $image_obligatoire;
			echo "</td>\n";
			echo "<td width='16' class='arial11'>\n";
			echo $image_type;
			echo "</td>\n";
			echo "<td class='arial2' width='160'>\n";
			echo "<a href='".generer_url_ecrire("questions_edit","id_formulaire=".$this->bloc->formulaire->id_formulaire."&id_bloc=".$this->bloc->id_bloc."&id_question=".$this->id_question)."'>\n";
			echo typo($this->titre);
			echo "</a>\n";
			echo "</td>\n";
			echo "<td class='arial2'>\n";
			echo typo($this->descriptif);
			echo "</td>\n";
			echo "<td class='arial1' width='128'>\n";
			echo $ajouter.$espace.$monter.$descendre.$espace.$editer.$espace.$supprimer;
			echo "</td>\n";
			echo "</tr>\n";

			if ($this->type == 'fichier' and count($this->fichiers)) {
				echo "<tr class='tr_liste' valign='top'>\n";
				echo "<td colspan='2' class='arial11'>&nbsp;</td>\n";
				echo "<td colspan='2' class='arial2'>\n";
				echo implode(', ', $this->fichiers);
				echo "</td>\n";
				echo "<td class='arial1' width='128'>&nbsp;</td>\n";
				echo "</tr>\n";
			}

			$choix_question = $this->recuperer_choix_question();
			foreach ($choix_question as $id_choix_question) {
				$choix_question = new choix_question($this->bloc->formulaire->id_formulaire, $this->bloc->id_bloc, $this->id_question, $id_choix_question);
				$choix_question->afficher();
			}
		}


	}


	/**
	 * choix_question
	 *
	 * @copyright Artégo
	 */

	class email_applicant extends question {


		/**
		 * email_applicant : constructeur
		 *
		 * @param int id_formulaire
		 * @param int id_bloc
		 * @param int id_question
		 * @return void
		 **/
		function email_applicant($id_formulaire, $id_bloc, $id_question=-1) {
			parent::question($id_formulaire, $id_bloc, $id_question);
			$this->titre		= _T('formulairesprive:email_applicant');
			$this->type			= 'email_applicant';
			$this->obligatoire	= 1;
			$this->controle		= 'email_applicant';
		}


	}


	/**
	 * choix_question
	 *
	 * @copyright Artégo
	 */

	class choix_question {

	    var $id_choix_question;
	    var $question;
		var $titre;
		var $ordre;
		var $id_rubrique;
		var $id_auteur;
		var $id_ancienne_question;


		/**
		 * choix_question : constructeur
		 *
		 * @param int id_formulaire
		 * @param int id_bloc
		 * @param int id_question
		 * @param int id_choix_question
		 * @return void
		 **/
		function choix_question($id_formulaire, $id_bloc, $id_question, $id_choix_question=-1) {
			$this->id_choix_question = $id_choix_question;
			$this->question = new question($id_formulaire, $id_bloc, $id_question);
			if ($this->id_choix_question == -1) {
				$this->titre			= _T('formulairesprive:nouveau_choix_question');
				$autres_choix_question	= $this->recuperer_autres_choix_question();
				$this->ordre			= count($autres_choix_question);
				$this->id_rubrique		= 0;
				$this->id_auteur		= 0;
			} else {
				$arr = sql_fetsel('*', 'spip_choix_question', 'id_choix_question='.intval($this->id_choix_question));
				$this->ordre		= $arr['ordre'];
				$this->titre		= $arr['titre'];
				$this->id_rubrique	= $arr['id_rubrique'];
				$this->id_auteur	= $arr['id_auteur'];
			}
		}


		/**
		 * enregistrer
		 *
		 * @return void
		 **/
		function enregistrer() {
			if ($this->id_choix_question == -1) { // insertion
				$this->id_choix_question = sql_insertq('spip_choix_question', array(
																					'id_question' => $this->question->id_question,
																					'ordre' => $this->ordre,
																					'titre' => $this->titre,
																					'id_rubrique' => $this->id_rubrique,
																					'id_auteur' => $this->id_auteur
																				));
			} else {
				$id_ancienne_question = sql_getfetsel('id_question', 'spip_choix_question', 'id_choix_question='.intval($this->id_choix_question));
				$this->id_ancienne_question = $id_ancienne_question;
				sql_updateq('spip_choix_question', array(
														'id_question' => $this->question->id_question,
														'titre' => $this->titre,
														'id_rubrique' => $this->id_rubrique,
														'id_auteur' => $this->id_auteur
														),
														'id_choix_question='.intval($this->id_choix_question));
			}
			$this->question->bloc->formulaire->mettre_a_jour_maj();
		}
		
		
		/**
		 * supprimer
		 *
		 * @return void
		 **/
		function supprimer() {
			$this->changer_ordre('dernier');
			sql_delete('spip_reponses', 'valeur='.intval($this->id_choix_question).' AND id_question='.intval($this->question->id_question));
			sql_delete('spip_choix_question', 'id_choix_question='.intval($this->id_choix_question));
		}


	    /**
	     * recuperer_autres_choix_question
	     *
	     * @return array autres_choix_question
	     */
		function recuperer_autres_choix_question() {
			$tableau_choix_question = $this->question->recuperer_choix_question();
			// on supprime l'id en cours du tableau
			$tableau_sans_id_a_inserer = array();
			foreach ($tableau_choix_question as $id) {
				if ($id != $this->id_choix_question)
					$tableau_sans_id_a_inserer[] = $id;
			}
			return $tableau_sans_id_a_inserer;
		}


	    /**
	     * changer_ordre
	     *
	     * @param string/int position
	     */
		function changer_ordre($position) {
			if ($this->id_ancienne_question != $this->question->id_question) { // réordonner l'ancienne question
				$ancienne_question = new question($this->question->bloc->formulaire->id_formulaire, $this->question->bloc->id_bloc, $this->id_ancienne_question);
				$anciens_autres_choix = $ancienne_question->recuperer_choix_question();
				$tableau_ordonne = formulaires_ordonner($anciens_autres_choix, 0, 0);
				if (is_array($tableau_ordonne)) {
					foreach ($tableau_ordonne as $cle => $valeur)
						sql_updateq('spip_choix_question', array('ordre' => $cle), 'id_choix_question='.intval($valeur));
				}
			}
			$tableau_choix_question = $this->recuperer_autres_choix_question();
			$tableau_ordonne = formulaires_ordonner($tableau_choix_question, $this->id_choix_question, $position);
			if (is_array($tableau_ordonne)) {
				foreach ($tableau_ordonne as $cle => $valeur)
					sql_updateq('spip_choix_question', array('ordre' => $cle), 'id_choix_question='.intval($valeur));
			}
			$this->ordre = sql_getfetsel('ordre', 'spip_choix_question', 'id_choix_question='.intval($this->id_choix_question));
		}
		
		
	    /**
	     * afficher
	     *
	     * @return void
	     */
		function afficher() {
			$taille_choix_question = count($this->question->recuperer_choix_question());

			$espace = http_img_pack("rien.gif", "", "width='16' height='16'");

			if ($this->ordre > 0)	$monter = '<a class="editer_position_choix_question" href="'.generer_url_action("editer_position_choix_question", "id_formulaire=".$this->question->bloc->formulaire->id_formulaire."&id_bloc=".$this->question->bloc->id_bloc."&id_question=".$this->question->id_question."&id_choix_question=".$this->id_choix_question."&position=".(($this->ordre)-1), true, true).'">'.http_img_pack(_DIR_PLUGIN_FORMULAIRES."/prive/images/monter-rouge.png", "", "width='16' height='16'")."</a>";
			else 					$monter = $espace;
			if ($this->ordre < ($taille_choix_question-1))	$descendre = '<a class="editer_position_choix_question" href="'.generer_url_action("editer_position_choix_question", "id_formulaire=".$this->question->bloc->formulaire->id_formulaire."&id_bloc=".$this->question->bloc->id_bloc."&id_question=".$this->question->id_question."&id_choix_question=".$this->id_choix_question."&position=".(($this->ordre)+1), true, true).'">'.http_img_pack(_DIR_PLUGIN_FORMULAIRES."/prive/images/descendre-rouge.png", "", "width='16' height='16'")."</a>";
			else 											$descendre = $espace;

			$editer = '<a href="'.generer_url_ecrire("choix_question_edit","id_formulaire=".$this->question->bloc->formulaire->id_formulaire."&id_bloc=".$this->question->bloc->id_bloc."&id_question=".$this->question->id_question."&id_choix_question=".$this->id_choix_question).'">'.http_img_pack(_DIR_PLUGIN_FORMULAIRES."/prive/images/editer.png", "", "width='16' height='16'").'</a>';
			$supprimer	= '<a class="supprimer_choix_question" href="'.generer_url_action("supprimer_choix_question", "id_formulaire=".$this->question->bloc->formulaire->id_formulaire."&id_bloc=".$this->question->bloc->id_bloc."&id_question=".$this->question->id_question."&id_choix_question=".$this->id_choix_question, true, true).'">'.http_img_pack(_DIR_PLUGIN_FORMULAIRES."/prive/images/poubelle.png", "", "width='16' height='16'").'</a>';

			$fleche = http_img_pack(_DIR_PLUGIN_FORMULAIRES."/prive/images/fleche.png", "", "width='16' height='16'");

			echo "<tr class='tr_liste_over' valign='top'>\n";
			echo "<td class='arial11'>&nbsp;</td>\n";
			echo "<td class='arial11'>";
			echo $fleche;
			echo "</td>\n";
			echo "<td class='arial2' colspan='2'>\n";
			echo "<A HREF='".generer_url_ecrire("choix_question_edit","id_formulaire=".$this->question->bloc->formulaire->id_formulaire."&id_bloc=".$this->question->bloc->id_bloc."&id_question=".$this->question->id_question."&id_choix_question=".$this->id_choix_question)."'>\n";
			echo typo($this->titre);
			echo "</A>\n";
			echo "</td>\n";
			echo "<td class='arial1' width='128'>\n";
			echo $espace.$espace.$monter.$descendre.$espace.$editer.$espace.$supprimer;
			echo "</td>\n";
			echo "</tr>\n";
		}


	}


	/**
	 * reponse
	 *
	 * @copyright Artégo
	 */

	class reponse {

	    var $id_reponse;


		/**
		 * reponse : constructeur
		 *
		 * @param int id_reponse
		 * @return void
		 **/
		function reponse($id_reponse) {
			$this->id_reponse = $id_reponse;
			$reponse = sql_fetsel('*', 'spip_reponses', 'id_reponse='.intval($this->id_reponse));
			$this->id_question		= $reponse['id_question'];
			$this->id_application	= $reponse['id_application'];
			$this->valeur			= $reponse['valeur'];
			$this->type = sql_getfetsel('type', 'spip_questions', 'id_question='.intval($this->id_question));
		}


		/**
		 * supprimer
		 *
		 * @return void
		 **/
		function supprimer() {
			if ($this->type == 'fichier') {
				$supprimer_document = charger_fonction('supprimer_document','action');
				$res = sql_select('*', 'spip_documents', 'id_document='.intval($this->valeur));
				while ($arr = sql_fetch($res)) {
					$supprimer_document($arr['id_document']);
					sql_delete('spip_documents_liens', 'id_document='.intval($arr['id_document']).' AND objet="application"');
				}
			}
			sql_delete('spip_reponses', 'id_reponse='.intval($this->id_reponse));
		}


	}


	/**
	 * invitation
	 *
	 * @copyright Artégo
	 */

	class invitation {

	    var $id_formulaire;
	    var $email;
		var $application;


		/**
		 * reponse : invitation
		 *
		 * @param int id_formulaire
		 * @param string email
		 * @return void
		 **/
		function invitation($id_formulaire, $email) {
			$recherche = sql_select('id_applicant', 'spip_applicants', 'email="'.$email.'"');
			if (sql_count($recherche) == 0) { // pas d'email comme ça : on crée un applicant
				$applicant = new applicant();
				$id_applicant = $applicant->id_applicant;
				$applicant->email = $email;
				$applicant->enregistrer();
			} else {
				$t = sql_fetch($recherche);
				$id_applicant = $t['id_applicant'];
			}
			$this->application = new application($id_applicant, $id_formulaire);
			if (!$this->application->existe)
				$this->application->creer_invitation();
		}


	}


?>