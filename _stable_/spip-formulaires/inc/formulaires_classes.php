<?php


	/**
	 * SPIP-Formulaires
	 *
	 * @copyright 2006-2007 Artégo
	 **/


	include_spip('inc/cookie');
	include_spip('formulaires_fonctions');
	include_spip('inc/spip-notifications');
	include_spip('inc/rubriques');
	include_spip('inc/presentation');


	/**
	 * applicant
	 *
	 * @copyright 2006-2007 Artégo
	 */

	class applicant {

	    var $id_applicant;
		var $iv;
		var $email;
		var $mdp;
		var $nom;

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
					$res = spip_query('SELECT id_applicant FROM spip_applicants WHERE iv="'.base64_encode($this->iv).'"');
					if (spip_num_rows($res) == 0)
						$verification = true;
				}
				spip_query('INSERT INTO spip_applicants (iv) VALUES ("'.base64_encode($this->iv).'")');
				$this->id_applicant = spip_insert_id();
				$this->existe = true;
			} else {
				$verification = spip_query('SELECT * FROM spip_applicants WHERE id_applicant="'.$id_applicant.'"');
				if (spip_num_rows($verification) == 1) {
					$tableau = spip_fetch_array($verification);
					$this->id_applicant	= $tableau['id_applicant'];
					$this->iv			= base64_decode($tableau['iv']);
					$this->email		= $tableau['email'];
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
			list($email_en_base) = spip_fetch_array(spip_query('SELECT email FROM spip_applicants WHERE id_applicant="'.$this->id_applicant.'"'), SPIP_NUM);
			if (ereg(_REGEXP_EMAIL, $email_en_base)) {
				// mettre à jour l'email de l'applicant
				spip_query('UPDATE spip_applicants SET email="'.$this->email.'", nom="'.addslashes(ucwords($this->nom)).'" WHERE id_applicant="'.$this->id_applicant.'"');
				// synchroniser les réponses à une question email_applicant
				$reponses_email_applicant = spip_query('SELECT R.id_reponse AS id_reponse
														FROM spip_reponses AS R
														INNER JOIN spip_applications AS APP ON APP.id_application=R.id_application
														INNER JOIN spip_questions AS Q ON Q.id_question=R.id_question
														WHERE APP.id_applicant="'.$this->id_applicant.'"
															AND Q.type="email_applicant"');
				while ($arr = spip_fetch_array($reponses_email_applicant)) {
					spip_query('UPDATE spip_reponses SET valeur="'.$this->email.'" WHERE id_reponse="'.$arr['id_reponse'].'"');
				}
				// synchroniser les réponses à une question nom_applicant
				$reponses_nom_applicant = spip_query('SELECT R.id_reponse AS id_reponse
														FROM spip_reponses AS R
														INNER JOIN spip_applications AS APP ON APP.id_application=R.id_application
														INNER JOIN spip_questions AS Q ON Q.id_question=R.id_question
														WHERE APP.id_applicant="'.$this->id_applicant.'"
															AND Q.type="nom_applicant"');
				while ($arr = spip_fetch_array($reponses_email_applicant)) {
					spip_query('UPDATE spip_reponses SET valeur="'.addslashes(ucwords($this->nom)).'" WHERE id_reponse="'.$arr['id_reponse'].'"');
				}
			} else {
				$mdp = strtolower(formulaires_generer_nouveau_mdp());
				$this->mdp = $mdp;
				spip_query('UPDATE spip_applicants 
							SET email="'.$this->email.'", 
								nom="'.addslashes(ucwords($this->nom)).'",
								mdp="'.$this->mdp.'" 
							WHERE id_applicant="'.$this->id_applicant.'"');
			}
			$this->enregistrer_maj();
			$this->indexer();
		}


		function enregistrer_maj() {
			spip_query('UPDATE spip_applicants SET maj=NOW() WHERE id_applicant='.$this->id_applicant);
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
			spip_query('UPDATE spip_applicants SET cookie=NOW() WHERE id_applicant="'.$this->id_applicant.'"');
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


		function indexer() {
			spip_query('UPDATE spip_applicants SET idx="1" WHERE id_applicant='.$this->id_applicant);
			indexer_objet('spip_applicants', $this->id_applicant, true);
		}


	}



	/**
	 * formulaire
	 *
	 * @copyright 2006-2007 Artégo
	 */

	class formulaire {

	    var $id_formulaire;
		var $id_rubrique;
		var $titre;
		var $descriptif;
		var $texte;
		var $ps;
		var $lang;
		var $date_debut;
		var $date_fin;
		var $type;
		var $limiter_temps;
		var $limiter_invitation;
		var $limiter_applicant;
		var $notifier_applicant;
		var $notifier_auteurs;
		var $en_ligne;
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
				if (!$id_rubrique) list($id_rubrique) = spip_fetch_array(spip_query('SELECT id_rubrique FROM spip_rubriques WHERE statut="publie" ORDER BY id_rubrique LIMIT 1'), SPIP_NUM);
				$this->id_rubrique			= $id_rubrique;
				$this->titre				= _T('formulairesprive:nouveau_formulaire');
				$this->type					= 'une_seule_page';
				$this->limiter_temps		= 'non';
				$this->limiter_invitation	= 'non';
				$this->limiter_applicant	= 'non';
				$this->notifier_applicant	= 'non';
				$this->notifier_auteurs		= 'non';
				$this->date_debut			= date('Y-m-d 00:00:00');
				$this->date_fin				= date('Y-m-d 23:59:59', mktime(0,0,0,date("m")+1,date("d"),date("Y")));
				$this->existe				= false;
			} else {
				$requete = spip_query('SELECT * FROM spip_formulaires WHERE id_formulaire="'.$id_formulaire.'"');
				$formulaire = spip_fetch_array($requete);
				$this->id_formulaire		= $formulaire['id_formulaire'];
				$this->id_rubrique			= $formulaire['id_rubrique'];
				$this->titre				= $formulaire['titre'];
				$this->descriptif			= $formulaire['descriptif'];
				$this->chapo				= $formulaire['chapo'];
				$this->texte				= $formulaire['texte'];
				$this->ps					= $formulaire['ps'];
				$this->lang					= $formulaire['lang'];
				$this->date_debut			= $formulaire['date_debut'];
				$this->date_fin				= $formulaire['date_fin'];
				$this->type					= $formulaire['type'];
				$this->limiter_temps		= $formulaire['limiter_temps'];
				$this->limiter_invitation	= $formulaire['limiter_invitation'];
				$this->limiter_applicant	= $formulaire['limiter_applicant'];
				$this->notifier_applicant	= $formulaire['notifier_applicant'];
				$this->notifier_auteurs		= $formulaire['notifier_auteurs'];
				$this->en_ligne				= $formulaire['en_ligne'];
				$this->statut				= $formulaire['statut'];
				$this->existe				= true;
			}
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
			$res = spip_query('SELECT id_application FROM spip_applications WHERE id_formulaire="'.$this->id_formulaire.'"');
			if (spip_num_rows($res) > 0)
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
			$this->supprimer_blocs();
			$this->supprimer_auteurs();
			$this->supprimer_documents();
			$this->supprimer_mots_cles();
			$this->supprimer_applications();
			$this->supprimer_logos();
			if ($this->est_vide()) {
				spip_query('DELETE FROM spip_formulaires WHERE id_formulaire="'.$this->id_formulaire.'"');
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
			$res = spip_query('SELECT id_auteur FROM spip_auteurs_formulaires WHERE id_formulaire="'.$this->id_formulaire.'"');
			while ($auteurs = spip_fetch_array($res)) {
				$this->supprimer_auteur($auteurs['id_auteur']);
			}
		}
		
		
		function supprimer_documents() {
			$documents = spip_query('SELECT D.id_document, 
											D.id_vignette, 
											D.fichier
										FROM spip_documents_formulaires AS DF 
										INNER JOIN spip_documents AS D ON D.id_document=DF.id_document 
										WHERE DF.id_formulaire="'.$this->id_formulaire.'"');
			while ($arr = spip_fetch_array($documents)) {
				if ($arr['id_vignette'] != 0) {
					$vignette = spip_query('SELECT fichier FROM spip_documents WHERE id_document="'.$arr['id_vignette'].'"');
					list($fichier_vignette) = spip_fetch_array($vignette, SPIP_NUM);
					unlink('../'.$fichier_vignette);
					spip_query('DELETE FROM spip_documents WHERE id_document="'.$arr['id_vignette'].'"');
				}
				unlink('../'.$arr['fichier']);
				spip_query('DELETE FROM spip_documents WHERE id_document="'.$arr['id_document'].'"');
			}
			spip_query('DELETE FROM spip_documents_formulaires WHERE id_formulaire="'.$this->id_formulaire.'"');
		}
		
		
		function supprimer_mots_cles() {
			spip_query('DELETE FROM spip_mots_formulaires WHERE id_formulaire="'.$this->id_formulaire.'"');
		}
		
		
		function supprimer_applications() {
			$res = spip_query('SELECT id_applicant, id_application FROM spip_applications WHERE id_formulaire="'.$this->id_formulaire.'"');
			while ($applications = spip_fetch_array($res)) {
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
	     * enregistrer : mettre à jour ou insérer un formulaire
	     *
	     * @param int id_formulaire
	     * @return void
	     */
		function enregistrer() {
			if ($this->id_formulaire == -1) { // insertion
				spip_query('INSERT INTO spip_formulaires (	titre,
															id_rubrique,
															descriptif,
															chapo,
															type,
															limiter_temps,
															limiter_invitation,
															limiter_applicant,
															notifier_applicant,
															notifier_auteurs,
															texte,
															ps,
															date_debut,
															date_fin,
															maj)
													VALUES ("'.$this->titre.'",
															"'.$this->id_rubrique.'",
															"'.$this->descriptif.'",
															"'.$this->chapo.'",
															"'.$this->type.'",
															"'.$this->limiter_temps.'",
															"'.$this->limiter_invitation.'",
															"'.$this->limiter_applicant.'",
															"'.$this->notifier_applicant.'",
															"'.$this->notifier_auteurs.'",
															"'.$this->texte.'",
															"'.$this->ps.'",
															"'.$this->date_debut.'",
															"'.$this->date_fin.'",
															NOW())');
				$this->id_formulaire = spip_insert_id();
				$this->ajouter_auteur($GLOBALS['auteur_session']['id_auteur']);
			} else {
				list($limiter_applicant_vieux, $limiter_invitation_vieux) = spip_fetch_array(spip_query('SELECT limiter_applicant, limiter_invitation FROM spip_formulaires WHERE id_formulaire="'.$this->id_formulaire.'"'), SPIP_NUM);
				spip_query('UPDATE spip_formulaires
							SET titre="'.$this->titre.'",
								id_rubrique="'.$this->id_rubrique.'",
								descriptif="'.$this->descriptif.'",
								chapo="'.$this->chapo.'",
								type="'.$this->type.'",
								limiter_temps="'.$this->limiter_temps.'",
								limiter_invitation="'.$this->limiter_invitation.'",
								limiter_applicant="'.$this->limiter_applicant.'",
								notifier_applicant="'.$this->notifier_applicant.'",
								notifier_auteurs="'.$this->notifier_auteurs.'",
								texte="'.$this->texte.'",
								ps="'.$this->ps.'",
								maj=NOW()
							WHERE id_formulaire="'.$this->id_formulaire.'"');
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
					$verification = spip_query('SELECT Q.id_question  
												FROM spip_questions AS Q
												INNER JOIN spip_blocs AS B ON B.id_bloc=Q.id_bloc
												WHERE Q.type="email_applicant" 
													AND B.id_formulaire="'.$this->id_formulaire.'"');
					if (spip_num_rows($verification) == 0) {
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
					spip_query('UPDATE spip_questions SET type="champ_texte", controle="email" WHERE id_bloc="'.$id_bloc.'" AND type="email_applicant"');
					spip_query('UPDATE spip_questions SET type="champ_texte" WHERE id_bloc="'.$id_bloc.'" AND type="nom_applicant"');
					spip_query('UPDATE spip_questions SET type="cases_a_cocher" WHERE id_bloc="'.$id_bloc.'" AND type="abonnements"');
				}
			}
			$this->indexer();
		}
		

	    /**
	     * mettre_a_jour_rubriques
	     *
	     * @return void
	     */
		function mettre_a_jour_rubriques() {
			if ($this->limiter_temps == 'oui') {
				if ($this->en_ligne == 'non') {
					$this->statut = 'en_attente';
					spip_query('UPDATE spip_formulaires SET statut="'.$this->statut.'" WHERE id_formulaire="'.$this->id_formulaire.'"');
				} else {
					$resultat_en_attente = spip_query('SELECT statut FROM spip_formulaires WHERE id_formulaire="'.$this->id_formulaire.'" AND NOW() < date_debut');
					if (spip_num_rows($resultat_en_attente) == 1) {
						list($statut) = spip_fetch_array($resultat_en_attente, SPIP_NUM);
						if ($statut != 'en_attente') spip_query('UPDATE spip_formulaires SET statut="en_attente", maj=NOW() WHERE id_formulaire="'.$this->id_formulaire.'"');
					}
					$resultat_publie = spip_query('SELECT statut FROM spip_formulaires WHERE id_formulaire="'.$this->id_formulaire.'" AND NOW() >= date_debut AND NOW() <= date_fin');
					if (spip_num_rows($resultat_publie) == 1) {
						list($statut) = spip_fetch_array($resultat_publie, SPIP_NUM);
						if ($statut != 'publie') spip_query('UPDATE spip_formulaires SET statut="publie", maj=NOW() WHERE id_formulaire="'.$this->id_formulaire.'"');
					}
					$resultat_termine = spip_query('SELECT statut FROM spip_formulaires WHERE id_formulaire="'.$this->id_formulaire.'" AND NOW() > date_fin');
					if (spip_num_rows($resultat_termine) == 1) {
						list($statut) = spip_fetch_array($resultat_termine, SPIP_NUM);
						if ($statut != 'termine') spip_query('UPDATE spip_formulaires SET statut="termine", maj=NOW() WHERE id_formulaire="'.$this->id_formulaire.'"');
					}
				}
			} else {
				if ($this->en_ligne == 'oui')	$this->statut = 'publie';
				else							$this->statut = 'en_attente';
				spip_query('UPDATE spip_formulaires SET statut="'.$this->statut.'" WHERE id_formulaire="'.$this->id_formulaire.'"');
			}
			calculer_rubriques();
			formulaires_propager_les_secteurs($dummy);
			formulaires_calculer_langues_rubriques($dummy);
		}


	    /**
	     * mettre_a_jour_maj : pour les objets enfants
	     *
	     */
		function mettre_a_jour_maj() {
			spip_query('UPDATE spip_formulaires SET maj=NOW() WHERE id_formulaire="'.$this->id_formulaire.'"');
			$this->mettre_a_jour_rubriques();
		}
		

	    /**
	     * supprimer_auteur
	     *
	     * @param int id_auteur
	     */
		function supprimer_auteur($id_auteur) {
			spip_query('DELETE FROM spip_auteurs_formulaires WHERE id_auteur="'.$id_auteur.'" AND id_formulaire="'.$this->id_formulaire.'" LIMIT 1');
		}
		
		
	    /**
	     * ajouter_auteur
	     *
	     * @param int id_auteur
	     */
		function ajouter_auteur($id_auteur) {
			if (spip_num_rows(spip_query('SELECT id_auteur FROM spip_auteurs_formulaires WHERE id_auteur="'.$id_auteur.'" AND id_formulaire="'.$this->id_formulaire.'"')) == 0)
				spip_query('INSERT INTO spip_auteurs_formulaires (id_auteur, id_formulaire) VALUES ("'.$id_auteur.'", "'.$this->id_formulaire.'")');
		}
		
		
	    /**
	     * changer_dates
	     *
	     * @param int annee_debut
	     * @param int mois_debut
	     * @param int jour_debut
	     * @param int annee_fin
	     * @param int mois_fin
	     * @param int jour_fin
	     */
		function changer_dates($annee_debut, $mois_debut, $jour_debut, $annee_fin, $mois_fin, $jour_fin) {
			$this->date_debut	= $annee_debut.'-'.$mois_debut.'-'.$jour_debut.' 00:00:00';
			$this->date_fin		= $annee_fin.'-'.$mois_fin.'-'.$jour_fin.' 23:59:00';
			spip_query('UPDATE spip_formulaires SET date_debut="'.$this->date_debut.'", date_fin="'.$this->date_fin.'", maj=NOW() WHERE id_formulaire="'.$this->id_formulaire.'"');
			$this->mettre_a_jour_rubriques();
		}
		

	    /**
	     * changer_en_ligne
	     *
	     * @param string en_ligne
	     * @return string url
	     */
		function changer_en_ligne($en_ligne) {
			$this->en_ligne = $en_ligne;
			switch ($this->en_ligne) {
				case 'poubelle':
					$this->supprimer();
					$url = generer_url_ecrire('formulaires_tous');
					break;
				default:
					spip_query('UPDATE spip_formulaires SET en_ligne="'.$this->en_ligne.'", maj=NOW() WHERE id_formulaire="'.$this->id_formulaire.'"');
					$url = generer_url_ecrire('formulaires', 'id_formulaire='.$this->id_formulaire, true);
					break;
			}
			$this->mettre_a_jour_rubriques();
			return $url;
		}


	    /**
	     * recuperer_blocs
	     *
	     * @return array blocs
	     */
		function recuperer_blocs() {
			$blocs = array();
			$res = spip_query('SELECT id_bloc FROM spip_blocs WHERE id_formulaire="'.$this->id_formulaire.'" ORDER BY ordre');
			while ($arr = spip_fetch_array($res))
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
			spip_query('DELETE FROM spip_applications WHERE id_formulaire="'.$this->id_formulaire.'"');
		}


		function indexer() {
			spip_query('UPDATE spip_formulaires SET idx="1" WHERE id_formulaire='.$this->id_formulaire);
			indexer_objet('spip_formulaires', $this->id_formulaire, true);
		}


	}


	/**
	 * application
	 *
	 * @copyright 2006-2007 Artégo
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
						$res = spip_query('SELECT APP.id_application, APP.statut 
											FROM spip_applications AS APP
											INNER JOIN spip_applicants AS A ON A.id_applicant=APP.id_applicant
											WHERE A.id_applicant="'.$this->applicant->id_applicant.'" 
												AND APP.id_formulaire="'.$this->formulaire->id_formulaire.'" 
												AND APP.statut IN ("valide")
												AND A.email != ""
												AND A.mdp != ""');
						if (spip_num_rows($res) == 1) {
							list($this->id_application, $this->statut) = spip_fetch_array($res, SPIP_NUM);
							$this->existe = true;
						} else {
							$this->existe = false;
						}
					} else {
						if ($this->formulaire->limiter_applicant == 'oui') {
							// on recherche l'application associée possédant le statut "temporaire" ou "valide"
							$res = spip_query('SELECT id_application, statut FROM spip_applications WHERE id_applicant="'.$this->applicant->id_applicant.'" AND id_formulaire="'.$this->formulaire->id_formulaire.'" AND statut IN ("temporaire","valide")');
							if (spip_num_rows($res) == 1) {
								list($this->id_application, $this->statut) = spip_fetch_array($res, SPIP_NUM);
								$this->existe = true;
							} else {
								$this->existe = false;
							}
						} else {
							// on recherche l'application associée possédant le statut "temporaire"
							$res = spip_query('SELECT id_application FROM spip_applications WHERE id_applicant="'.$this->applicant->id_applicant.'" AND id_formulaire="'.$this->formulaire->id_formulaire.'" AND statut IN ("temporaire")');
							if (spip_num_rows($res) == 1) {
								list($this->id_application) = spip_fetch_array($res, SPIP_NUM);
								$this->statut = 'temporaire';
								$this->existe = true;
							} else {
								$this->existe = false;
							}
						}
					}
				} else {
					$res = spip_query('SELECT id_application, statut FROM spip_applications WHERE id_applicant="'.$this->applicant->id_applicant.'" AND id_formulaire="'.$this->formulaire->id_formulaire.'" AND id_application="'.$this->id_application.'"');
					if (spip_num_rows($res) == 1) {
						list($this->id_application, $this->statut) = spip_fetch_array($res, SPIP_NUM);
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
				spip_query('INSERT INTO spip_applications (id_applicant, id_formulaire, statut, maj) VALUES ("'.$this->applicant->id_applicant.'", "'.$this->formulaire->id_formulaire.'", "temporaire", NOW())');
				$this->id_application = spip_insert_id();
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
			$applicant = spip_query('SELECT id_applicant FROM spip_applicants WHERE email != "" AND mdp != "" AND id_applicant="'.$this->applicant->id_applicant.'"');
			if (spip_num_rows($applicant) == 0)
				return true;
			// aucune réponse ?
			$reponses = spip_query('SELECT id_reponse FROM spip_reponses WHERE id_application="'.$this->id_application.'"');
			if (spip_num_rows($reponses) > 0)
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
			$res = spip_query('SELECT valeur FROM spip_reponses WHERE id_question="'.$question->id_question.'" AND id_application="'.$this->id_application.'" AND valeur!="" LIMIT 1');
			if (spip_num_rows($res) > 0) {
				list($valeur) = spip_fetch_array($res, SPIP_NUM);
				switch ($question->controle) {
					case 'non_vide':			return !empty($valeur);
					case 'email':				return ereg(_REGEXP_EMAIL, $valeur);
					case 'url':					return ereg("^(http|https)://", $valeur);
					case 'nombre':				return ereg("^[0-9]+$", $valeur);
					case 'date':				return ereg("([0-9]{2})/([0-9]{2})/([0-9]{4})", $valeur);
					case 'email_applicant':
						if (!ereg(_REGEXP_EMAIL, $valeur))
							return false;
						$verification = spip_query('SELECT id_applicant 
													FROM spip_applicants
													WHERE email="'.$valeur.'"');
						if (spip_num_rows($verification) == 0) {
							return true;
						} else {
							list($id_applicant) = spip_fetch_array($verification, SPIP_NUM);
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
							spip_query('INSERT INTO spip_reponses (id_question, id_application, valeur) VALUES ("'.$id_question.'", "'.$this->id_application.'", "'.addslashes($valeur).'")');
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
									spip_query('INSERT INTO spip_reponses (id_question, id_application, valeur) VALUES ("'.$id_question.'", "'.$this->id_application.'", "'.addslashes($valeur).'")');
								}
						} else {
							if (!empty($valeurs)) {
								$question->supprimer_reponses_application($this->id_application);
								spip_query('INSERT INTO spip_reponses (id_question, id_application, valeur) VALUES ("'.$id_question.'", "'.$this->id_application.'", "'.addslashes($valeurs).'")');
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
							$res = spip_query('SELECT * FROM spip_types_documents WHERE mime_type="'.addslashes($mime).'" AND upload="oui" AND inclus="image" LIMIT 1');
							if (spip_num_rows($res) > 0) {
								$type_document = spip_fetch_array($res);
								$extension = $type_document['extension'];
								$id_type = $type_document['id_type'];
								$nom_fichier = $this->id_application.'-'.mktime().'.'.$extension;
								$fichier = _DIR_FORMULAIRES.$nom_fichier;
								$taille_image = @getimagesize($chemin_fichier);
								$largeur = intval($taille_image[0]);
								$hauteur = intval($taille_image[1]);
								move_uploaded_file($chemin_fichier, $fichier);
								spip_query('INSERT INTO spip_documents (id_type, titre, date, fichier, taille, largeur, hauteur, mode, distant, maj)
											VALUES ("'.$id_type.'", "'.addslashes($titre).'", NOW(), "'.$fichier.'", "'.$taille.'", "'.$largeur.'", "'.$hauteur.'", "document", "non", NOW())');
								$id_document = spip_insert_id();
								spip_query('INSERT INTO spip_documents_applications (id_document, id_application)
								 			VALUES ("'.$id_document.'", "'.$this->id_application.'")');
								spip_query('INSERT INTO spip_reponses (id_question, id_application, valeur) 
											VALUES ("'.$id_question.'", "'.$this->id_application.'", "'.$id_document.'")');
							}
						}
						$reponses = $question->recuperer_reponses_application($this->id_application);
						foreach ($reponses as $id_reponse) {
							$supprimer_document = _request('s_'.$id_reponse);
							if (!empty($supprimer_document)) {
								$verif = spip_query('SELECT D.* 
													 FROM spip_documents AS D
													 INNER JOIN spip_documents_applications AS DA ON DA.id_document=D.id_document
													 WHERE DA.id_application="'.$this->id_application.'"
														AND D.id_document="'.$supprimer_document.'"');
								if (spip_num_rows($verif) > 0) {
									$arr = spip_fetch_array($verif);
									unlink($arr['fichier']);
									spip_query('DELETE FROM spip_documents WHERE id_document="'.$supprimer_document.'"');
									spip_query('DELETE FROM spip_documents_applications WHERE id_document="'.$supprimer_document.'"');
									spip_query('DELETE FROM spip_reponses WHERE id_application="'.$this->id_application.'" AND id_reponse="'.$id_reponse.'"');
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
			spip_query('UPDATE spip_applications SET maj=NOW() WHERE id_application="'.$this->id_application.'"');
		}

		
		/**
		 * creer_invitation
		 *
		 * @return void 
		 **/
		function creer_invitation() {
			if ($this->formulaire->limiter_invitation == 'oui') {
				spip_query('INSERT INTO spip_applications (id_applicant, id_formulaire, statut, maj) VALUES ("'.$this->applicant->id_applicant.'", "'.$this->formulaire->id_formulaire.'", "valide", NOW())');
				$this->id_application = spip_insert_id();
			}
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
				spip_query('UPDATE spip_applications SET statut="'.$statut.'", maj=NOW() WHERE id_application="'.$this->id_application.'"');
				$this->statut = $statut;
			}

			if ($this->formulaire->notifier_auteurs == 'oui') {
				$objet 			= inclure_balise_dynamique(array('notifications/notification_nouvelle_application_titre', 0, array('id_formulaire' => $this->formulaire->id_formulaire, 'id_application' => $this->id_application, 'lang' => $lang)), false);
				$message_html	= inclure_balise_dynamique(array('notifications/notification_nouvelle_application_html', 0, array('id_formulaire' => $this->formulaire->id_formulaire, 'id_application' => $this->id_application, 'lang' => $lang)), false);
				$message_texte	= inclure_balise_dynamique(array('notifications/notification_nouvelle_application_texte', 0, array('id_formulaire' => $this->formulaire->id_formulaire, 'id_application' => $this->id_application, 'lang' => $lang)), false);
				// réponses aux questions de type "auteurs"
				$resA = spip_query('SELECT A.email 
									FROM spip_auteurs AS A
									INNER JOIN spip_choix_question AS CQ ON CQ.id_auteur=A.id_auteur
									INNER JOIN spip_questions AS Q ON Q.id_question=CQ.id_question
									INNER JOIN spip_blocs AS B ON B.id_bloc=Q.id_bloc
									INNER JOIN spip_applications AS APP ON APP.id_formulaire=B.id_formulaire
									INNER JOIN spip_reponses AS REP ON REP.id_application=APP.id_application
									WHERE Q.type="auteurs"
										AND REP.id_question=Q.id_question
										AND REP.valeur=CQ.id_choix_question
										AND APP.id_application='.$this->id_application);
				$resB = spip_query('SELECT A.email 
									FROM spip_auteurs AS A
									INNER JOIN spip_auteurs_formulaires AS AF ON AF.id_auteur=A.id_auteur
									WHERE AF.id_formulaire="'.$this->formulaire->id_formulaire.'"');
				if (spip_num_rows($resB) >= 1) {
					$tableau_emails = array();
					if (spip_num_rows($resA) >= 1) {
						while ($arr = spip_fetch_array($resA))
							$tableau_emails[] = $arr['email'];
					} else {
						while ($arr = spip_fetch_array($resB))
							$tableau_emails[] = $arr['email'];
					}
					$email_premier_auteur = $tableau_emails[0];
					$notification = new Notification($email_premier_auteur, $objet, $message_html, $message_texte);
					$tableau_emails_sans_premier = array_slice($tableau_emails, 1);
					foreach ($tableau_emails_sans_premier as $email_auteur)
						$notification->AddAddress($email_auteur);
					$resC = spip_query('SELECT R.valeur
										FROM spip_reponses AS R
										INNER JOIN spip_questions AS Q ON Q.id_question=R.id_question
										INNER JOIN spip_blocs AS B ON B.id_bloc=Q.id_bloc
										WHERE Q.controle="email_applicant"
											AND B.id_formulaire='.$this->formulaire->id_formulaire.'
											AND R.id_application='.$this->id_application.'
										LIMIT 1');
					if (spip_num_rows($resC) == 1) {
						list($email_applicant) = spip_fetch_array($resC, SPIP_NUM);
						$notification->AddReplyTo($email_applicant);
					} else {
						$resD = spip_query('SELECT R.valeur
											FROM spip_reponses AS R
											INNER JOIN spip_questions AS Q ON Q.id_question=R.id_question
											INNER JOIN spip_blocs AS B ON B.id_bloc=Q.id_bloc
											WHERE Q.controle="email"
												AND B.id_formulaire='.$this->formulaire->id_formulaire.'
												AND R.id_application='.$this->id_application.'
											LIMIT 1');
						if (spip_num_rows($resD) == 1) {
							list($email_usager) = spip_fetch_array($resD, SPIP_NUM);
							$notification->AddReplyTo($email_usager);
						}
					}
					$message_envoye = $notification->Send();
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

			$rubriques = array();

			// on récupère les questions de type "abonnements"
			$abonnements_disponibles = $this->formulaire->recuperer_abonnements_disponibles();

			// a-t'on une réponse ?
			foreach ($abonnements_disponibles as $id_rubrique) {
				$res = spip_query('SELECT REP.id_reponse 
									FROM spip_reponses AS REP
									INNER JOIN spip_choix_question AS CH ON CH.id_question=REP.id_question
									WHERE CH.id_rubrique="'.$id_rubrique.'"
										AND REP.id_application="'.$this->id_application.'"
										AND REP.valeur=CH.id_choix_question');
				if (spip_num_rows($res) > 0)
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
			spip_query('DELETE FROM spip_applications WHERE id_application="'.$this->id_application.'"');
		}


		/**
		 * supprimer_reponses
		 *
		 * @return void
		 **/
		function supprimer_reponses() {
			$res = spip_query('SELECT id_reponse FROM spip_reponses WHERE id_application="'.$this->id_application.'"');
			while ($reponses = spip_fetch_array($res)) {
				$reponse = new reponse($reponses['id_reponse']);
				$reponse->supprimer();
			}
		}


	}


	/**
	 * bloc
	 *
	 * @copyright 2006-2007 Artégo
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
				$res = spip_query('SELECT * FROM spip_blocs WHERE id_bloc="'.$this->id_bloc.'"');
				$arr = spip_fetch_array($res);
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
				spip_query('INSERT INTO spip_blocs (id_formulaire,
													ordre,
													titre,
													descriptif,
													texte)
											VALUES ("'.$this->formulaire->id_formulaire.'",
													"'.$this->ordre.'",
													"'.$this->titre.'",
													"'.$this->descriptif.'",
													"'.$this->texte.'")');
				$this->id_bloc = spip_insert_id();
			} else {
				spip_query('UPDATE spip_blocs
							SET titre="'.$this->titre.'",
								descriptif="'.$this->descriptif.'",
								texte="'.$this->texte.'"
							WHERE id_bloc="'.$this->id_bloc.'"');
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
				spip_query('DELETE FROM spip_blocs WHERE id_bloc="'.$this->id_bloc.'"');
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
			$res = spip_query('SELECT id_question FROM spip_questions WHERE id_bloc="'.$this->id_bloc.'" '.$plus.'ORDER BY ordre');
			while ($arr = spip_fetch_array($res))
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
				$res = spip_query('SELECT id_question FROM spip_questions WHERE id_bloc="'.$this->id_bloc.'" AND type="abonnements" ORDER BY ordre');
			else
				$res = spip_query('SELECT id_question FROM spip_questions WHERE id_bloc="'.$this->id_bloc.'" AND type IN ("boutons_radio","cases_a_cocher","liste","liste_multiple","auteurs") ORDER BY ordre');
			while ($arr = spip_fetch_array($res))
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
				$res = spip_query('SELECT id_question FROM spip_questions WHERE id_bloc="'.$this->id_bloc.'" AND type="auteurs" ORDER BY ordre');
			else
				$res = spip_query('SELECT id_question FROM spip_questions WHERE id_bloc="'.$this->id_bloc.'" AND type IN ("boutons_radio","cases_a_cocher","liste","liste_multiple","abonnements") ORDER BY ordre');
			while ($arr = spip_fetch_array($res))
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
			$res = spip_query('SELECT id_question FROM spip_questions WHERE id_bloc="'.$this->id_bloc.'" AND obligatoire="1" '.$plus.'ORDER BY ordre');
			while ($arr = spip_fetch_array($res))
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
				spip_query('UPDATE spip_blocs SET ordre="'.$cle.'" WHERE id_bloc="'.$valeur.'"');
			list($this->ordre) = spip_fetch_array(spip_query('SELECT ordre FROM spip_blocs WHERE id_bloc="'.$this->id_bloc.'"'), SPIP_NUM);
		}
		
		
		/**
		 * possede_email_applicant
		 *
		 * @return boolean possede_email_applicant
		 **/
		function possede_email_applicant() {
			$res = spip_query('SELECT * FROM spip_questions WHERE type="email_applicant" AND id_bloc="'.$this->id_bloc.'"');
			if (spip_num_rows($res) == 0)
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

			if ($this->ordre > 0)	$monter = "<A HREF='".generer_url_ecrire("formulaires","id_formulaire=".$this->formulaire->id_formulaire."&ordonner_bloc=".$this->id_bloc."&position=".(($this->ordre)-1))."'>".http_img_pack('../'._DIR_PLUGIN_FORMULAIRES."/img_pack/monter-bleu.png", "", "width='16' height='16'")."</A>";
			else 					$monter = http_img_pack("rien.gif", "", "width='16' height='16'");
			if ($this->ordre < ($taille_blocs-1))	$descendre = "<A HREF='".generer_url_ecrire("formulaires","id_formulaire=".$this->formulaire->id_formulaire."&ordonner_bloc=".$this->id_bloc."&position=".(($this->ordre)+1))."'>".http_img_pack('../'._DIR_PLUGIN_FORMULAIRES."/img_pack/descendre-bleu.png", "", "width='16' height='16'")."</A>";
			else 									$descendre = http_img_pack("rien.gif", "", "width='16' height='16'");
			if ($nb_questions > 0) 	$bouton_invisible = bouton_block_invisible('bloc_'.$this->id_bloc);
			else 					$bouton_invisible = '';

			$editer = "<A HREF='".generer_url_ecrire("blocs_edit","id_formulaire=".$this->formulaire->id_formulaire."&id_bloc=".$this->id_bloc)."'>".http_img_pack('../'._DIR_PLUGIN_FORMULAIRES."/img_pack/editer.png", "", "width='16' height='16'")."</A>";
			$bord = http_img_pack("rien.gif", "", "width='7' height='16'");
			$espace = http_img_pack("rien.gif", "", "width='16' height='16'");

			if ($this->possede_email_applicant())	$supprimer = "<A HREF='".generer_url_ecrire("formulaires","id_formulaire=".$this->formulaire->id_formulaire."&supprimer_bloc=".$this->id_bloc)."'>".http_img_pack('../'._DIR_PLUGIN_FORMULAIRES."/img_pack/poubelle.png", "", "width='16' height='16'")."</A>";
			else 									$supprimer = $espace;

			$titre = '<span style="float: right;">'.$monter.$descendre.$espace.$editer.$espace.$supprimer.$bord.'</span><a href="'.generer_url_ecrire("blocs_edit","id_formulaire=".$this->formulaire->id_formulaire."&id_bloc=".$this->id_bloc).'" style="color: #000;">'.propre($this->titre).'</a>';

			debut_cadre_couleur('../'._DIR_PLUGIN_FORMULAIRES.'/img_pack/bloc.png', false, "", $titre);

			if (strlen($this->descriptif) > 0) {
				echo "<div align='justify' style='padding: 10px;'>";
				echo propre($this->descriptif);
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
				echo propre($this->texte);
				echo "</div>";
			}

			echo "<div align='$spip_lang_right'>";
			echo icone(_T('formulairesprive:creer_nouvelle_question'), generer_url_ecrire("questions_edit","id_formulaire=".$this->formulaire->id_formulaire."&id_bloc=".$this->id_bloc."&new=oui"), '../'._DIR_PLUGIN_FORMULAIRES.'/img_pack/question.png', "creer.gif", '', 'non');
			echo "</div><p>";

			fin_cadre_couleur();
		}


	}


	/**
	 * question
	 *
	 * @copyright 2006-2007 Artégo
	 */

	class question {

	    var $id_question;
	    var $bloc;
		var $ordre;
		var $titre;
		var $descriptif;
		var $id_ancien_bloc;


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
				$res = spip_query('SELECT * FROM spip_questions WHERE id_question="'.$this->id_question.'"');
				$arr = spip_fetch_array($res);
				$this->ordre		= $arr['ordre'];
				$this->titre		= $arr['titre'];
				$this->descriptif	= $arr['descriptif'];
				$this->type			= $arr['type'];
				$this->obligatoire	= $arr['obligatoire'];
				$this->controle		= $arr['controle'];
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
				spip_query('INSERT INTO spip_questions (id_bloc,
														ordre,
														type,
														obligatoire,
														controle,
														titre,
														descriptif)
												VALUES ("'.$this->bloc->id_bloc.'",
														"'.$this->ordre.'",
														"'.$this->type.'",
														"'.$this->obligatoire.'",
														"'.$this->controle.'",
														"'.$this->titre.'",
														"'.$this->descriptif.'")');
				$this->id_question = spip_insert_id();
			} else {
				list($ancien_type, $id_ancien_bloc) = spip_fetch_array(spip_query('SELECT type, id_bloc FROM spip_questions WHERE id_question="'.$this->id_question.'"'), SPIP_NUM);
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
				spip_query('UPDATE spip_questions
							SET id_bloc="'.$this->bloc->id_bloc.'",
								titre="'.$this->titre.'",
								descriptif="'.$this->descriptif.'",
								type="'.$this->type.'",
								obligatoire="'.$this->obligatoire.'",
								controle="'.$this->controle.'"
							WHERE id_question="'.$this->id_question.'"');
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
				spip_query('DELETE FROM spip_questions WHERE id_question="'.$this->id_question.'"');
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
			$res = spip_query('SELECT id_choix_question FROM spip_choix_question WHERE id_question="'.$this->id_question.'" ORDER BY ordre');
			while ($arr = spip_fetch_array($res))
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
			$res = spip_query('SELECT id_reponse FROM spip_reponses WHERE id_question="'.$this->id_question.'" ORDER BY ordre');
			while ($arr = spip_fetch_array($res))
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
			$res = spip_query('SELECT id_reponse FROM spip_reponses WHERE id_question="'.$this->id_question.'" AND id_application="'.$id_application.'"');
			while ($arr = spip_fetch_array($res))
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
						spip_query('UPDATE spip_questions SET ordre="'.$cle.'" WHERE id_question="'.$valeur.'"');
				}
			}
			$tableau_questions = $this->recuperer_autres_questions();
			$tableau_ordonne = formulaires_ordonner($tableau_questions, $this->id_question, $position);
			foreach ($tableau_ordonne as $cle => $valeur)
				spip_query('UPDATE spip_questions SET ordre="'.$cle.'" WHERE id_question="'.$valeur.'"');
			list($this->ordre) = spip_fetch_array(spip_query('SELECT ordre FROM spip_questions WHERE id_question="'.$this->id_question.'"'), SPIP_NUM);
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

			if ($this->ordre > 0)	$monter = "<A HREF='".generer_url_ecrire("formulaires","id_formulaire=".$this->bloc->formulaire->id_formulaire."&id_bloc=".$this->bloc->id_bloc."&ordonner_question=".$this->id_question."&position=".(($this->ordre)-1))."'>".http_img_pack('../'._DIR_PLUGIN_FORMULAIRES."/img_pack/monter-16.png", "", "width='16' height='16'")."</A>";
			else 					$monter = $espace;
			if ($this->ordre < ($taille_questions-1))	$descendre = "<A HREF='".generer_url_ecrire("formulaires","id_formulaire=".$this->bloc->formulaire->id_formulaire."&id_bloc=".$this->bloc->id_bloc."&ordonner_question=".$this->id_question."&position=".(($this->ordre)+1))."'>".http_img_pack('../'._DIR_PLUGIN_FORMULAIRES."/img_pack/descendre-16.png", "", "width='16' height='16'")."</A>";
			else 										$descendre = $espace;

			$editer = "<A HREF='".generer_url_ecrire("questions_edit","id_formulaire=".$this->bloc->formulaire->id_formulaire."&id_bloc=".$this->bloc->id_bloc."&id_question=".$this->id_question)."'>".http_img_pack('../'._DIR_PLUGIN_FORMULAIRES."/img_pack/editer.png", "", "width='16' height='16'")."</A>";

			if ($this->supprimable())	$supprimer = "<A HREF='".generer_url_ecrire("formulaires","id_formulaire=".$this->bloc->formulaire->id_formulaire."&id_bloc=".$this->bloc->id_bloc."&supprimer_question=".$this->id_question)."'>".http_img_pack('../'._DIR_PLUGIN_FORMULAIRES."/img_pack/poubelle.png", "", "width='16' height='16'")."</A>";
			else 						$supprimer = $espace;

			if ($this->obligatoire) $image_obligatoire = http_img_pack('../'._DIR_PLUGIN_FORMULAIRES."/img_pack/obligatoire.png", "", "width='16' height='16'");
			else 					$image_obligatoire = $espace;

			switch ($this->type) {
				case 'champ_texte':
					if ($this->obligatoire) {
						switch ($this->controle) {
							case 'non_vide':
								$image_type = http_img_pack('../'._DIR_PLUGIN_FORMULAIRES."/img_pack/".$this->type.".png", "", "width='16' height='16'");
								break;
							case 'url':
								$image_type = http_img_pack('../'._DIR_PLUGIN_FORMULAIRES."/img_pack/lien.png", "", "width='16' height='16'");
								break;
							case 'nombre':
								$image_type = http_img_pack('../'._DIR_PLUGIN_FORMULAIRES."/img_pack/champ_numerique.png", "", "width='16' height='16'");
								break;
							case 'email':
								$image_type = http_img_pack('../'._DIR_PLUGIN_FORMULAIRES."/img_pack/email.png", "", "width='16' height='16'");
								break;
							case 'date':
								$image_type = http_img_pack('../'._DIR_PLUGIN_FORMULAIRES."/img_pack/date.png", "", "width='16' height='16'");
								break;
						}
					} else {
						$image_type = http_img_pack('../'._DIR_PLUGIN_FORMULAIRES."/img_pack/".$this->type.".png", "", "width='16' height='16'");
					}
					$ajouter = $espace;
					break;
				case 'boutons_radio':
				case 'cases_a_cocher':
				case 'liste':
				case 'liste_multiple':
				case 'abonnements':
					$image_type = http_img_pack('../'._DIR_PLUGIN_FORMULAIRES."/img_pack/".$this->type.".png", "", "width='16' height='16'");
					$ajouter = '<a href="'.generer_url_ecrire('choix_question_edit', 'id_formulaire='.$this->bloc->formulaire->id_formulaire.'&id_bloc='.$this->bloc->id_bloc.'&id_question='.$this->id_question.'&new=oui').'">'.http_img_pack('../'._DIR_PLUGIN_FORMULAIRES."/img_pack/ajouter_choix_".$this->type.".png", "", "width='16' height='16'").'</a>';
					break;
				case 'email_applicant':
					$ajouter = $espace;
					$image_type = http_img_pack('../'._DIR_PLUGIN_FORMULAIRES."/img_pack/".$this->type.".png", "", "width='16' height='16'");
					break;
				case 'nom_applicant':
					$ajouter = $espace;
					$image_type = http_img_pack('../'._DIR_PLUGIN_FORMULAIRES."/img_pack/".$this->type.".png", "", "width='16' height='16'");
					break;
				case 'auteurs':
					$image_type = http_img_pack('../'._DIR_PLUGIN_FORMULAIRES."/img_pack/liste_auteurs.png", "", "width='16' height='16'");
					$ajouter = '<a href="'.generer_url_ecrire('choix_question_edit', 'id_formulaire='.$this->bloc->formulaire->id_formulaire.'&id_bloc='.$this->bloc->id_bloc.'&id_question='.$this->id_question.'&new=oui').'">'.http_img_pack('../'._DIR_PLUGIN_FORMULAIRES."/img_pack/ajouter_choix_".$this->type.".png", "", "width='16' height='16'").'</a>';
					break;
				default:
					$ajouter = $espace;
					$image_type = http_img_pack('../'._DIR_PLUGIN_FORMULAIRES."/img_pack/".$this->type.".png", "", "width='16' height='16'");
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
			echo "<A HREF='".generer_url_ecrire("questions_edit","id_formulaire=".$this->bloc->formulaire->id_formulaire."&id_bloc=".$this->bloc->id_bloc."&id_question=".$this->id_question)."'>\n";
			echo propre($this->titre);
			echo "</A>\n";
			echo "</td>\n";
			echo "<td class='arial2'>\n";
			echo propre($this->descriptif);
			echo "</td>\n";
			echo "<td class='arial1' width='128'>\n";
			echo $ajouter.$espace.$monter.$descendre.$espace.$editer.$espace.$supprimer;
			echo "</td>\n";
			echo "</tr>\n";

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
	 * @copyright 2006-2007 Artégo
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
	 * @copyright 2006-2007 Artégo
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
				$res = spip_query('SELECT * FROM spip_choix_question WHERE id_choix_question="'.$this->id_choix_question.'"');
				$arr = spip_fetch_array($res);
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
				spip_query('INSERT INTO spip_choix_question (id_question,
															ordre,
															titre,
															id_rubrique,
															id_auteur)
													VALUES ("'.$this->question->id_question.'",
															"'.$this->ordre.'",
															"'.$this->titre.'",
															"'.$this->id_rubrique.'",
															"'.$this->id_auteur.'")');
				$this->id_choix_question = spip_insert_id();
			} else {
				list($id_ancienne_question) = spip_fetch_array(spip_query('SELECT id_question FROM spip_choix_question WHERE id_choix_question="'.$this->id_choix_question.'"'), SPIP_NUM);
				$this->id_ancienne_question = $id_ancienne_question;
				spip_query('UPDATE spip_choix_question
							SET id_question="'.$this->question->id_question.'",
								titre="'.$this->titre.'",
								id_rubrique="'.$this->id_rubrique.'",
								id_auteur="'.$this->id_auteur.'"
							WHERE id_choix_question="'.$this->id_choix_question.'"');
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
			spip_query('DELETE FROM spip_reponses WHERE valeur="'.$this->id_choix_question.'" AND id_question="'.$this->question->id_question.'"');
			spip_query('DELETE FROM spip_choix_question WHERE id_choix_question="'.$this->id_choix_question.'"');
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
						spip_query('UPDATE spip_choix_question SET ordre="'.$cle.'" WHERE id_choix_question="'.$valeur.'"');
				}
			}
			$tableau_choix_question = $this->recuperer_autres_choix_question();
			$tableau_ordonne = formulaires_ordonner($tableau_choix_question, $this->id_choix_question, $position);
			if (is_array($tableau_ordonne)) {
				foreach ($tableau_ordonne as $cle => $valeur)
					spip_query('UPDATE spip_choix_question SET ordre="'.$cle.'" WHERE id_choix_question="'.$valeur.'"');
			}
			list($this->ordre) = spip_fetch_array(spip_query('SELECT ordre FROM spip_choix_question WHERE id_choix_question="'.$this->id_choix_question.'"'), SPIP_NUM);
		}
		
		
	    /**
	     * afficher
	     *
	     * @return void
	     */
		function afficher() {
			$taille_choix_question = count($this->question->recuperer_choix_question());

			$espace = http_img_pack("rien.gif", "", "width='16' height='16'");

			if ($this->ordre > 0)	$monter = "<A HREF='".generer_url_ecrire("formulaires","id_formulaire=".$this->question->bloc->formulaire->id_formulaire."&id_bloc=".$this->question->bloc->id_bloc."&id_question=".$this->question->id_question."&ordonner_choix_question=".$this->id_choix_question."&position=".(($this->ordre)-1))."'>".http_img_pack('../'._DIR_PLUGIN_FORMULAIRES."/img_pack/monter-rouge.png", "", "width='16' height='16'")."</A>";
			else 					$monter = $espace;
			if ($this->ordre < ($taille_choix_question-1))	$descendre = "<A HREF='".generer_url_ecrire("formulaires","id_formulaire=".$this->question->bloc->formulaire->id_formulaire."&id_bloc=".$this->question->bloc->id_bloc."&id_question=".$this->question->id_question."&ordonner_choix_question=".$this->id_choix_question."&position=".(($this->ordre)+1))."'>".http_img_pack('../'._DIR_PLUGIN_FORMULAIRES."/img_pack/descendre-rouge.png", "", "width='16' height='16'")."</A>";
			else 											$descendre = $espace;

			$editer = "<A HREF='".generer_url_ecrire("choix_question_edit","id_formulaire=".$this->question->bloc->formulaire->id_formulaire."&id_bloc=".$this->question->bloc->id_bloc."&id_question=".$this->question->id_question."&id_choix_question=".$this->id_choix_question)."'>".http_img_pack('../'._DIR_PLUGIN_FORMULAIRES."/img_pack/editer.png", "", "width='16' height='16'")."</A>";
			$supprimer	= "<A HREF='".generer_url_ecrire("formulaires","id_formulaire=".$this->question->bloc->formulaire->id_formulaire."&id_bloc=".$this->question->bloc->id_bloc."&id_question=".$this->question->id_question."&supprimer_choix_question=".$this->id_choix_question)."'>".http_img_pack('../'._DIR_PLUGIN_FORMULAIRES."/img_pack/poubelle.png", "", "width='16' height='16'")."</A>";

			$fleche = http_img_pack('../'._DIR_PLUGIN_FORMULAIRES."/img_pack/fleche.png", "", "width='16' height='16'");

			echo "<tr class='tr_liste_over' valign='top'>\n";
			echo "<td class='arial11'>&nbsp;</td>\n";
			echo "<td class='arial11'>";
			echo $fleche;
			echo "</td>\n";
			echo "<td class='arial2' colspan='2'>\n";
			echo "<A HREF='".generer_url_ecrire("choix_question_edit","id_formulaire=".$this->question->bloc->formulaire->id_formulaire."&id_bloc=".$this->question->bloc->id_bloc."&id_question=".$this->question->id_question."&id_choix_question=".$this->id_choix_question)."'>\n";
			echo propre($this->titre);
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
	 * @copyright 2006-2007 Artégo
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
			list($reponse) = spip_fetch_array(spip_query('SELECT * FROM spip_reponses WHERE id_reponse="'.$this->id_reponse.'"'), SPIP_NUM);
			$this->id_question		= $reponse['id_question'];
			$this->id_application	= $reponse['id_application'];
			$this->valeur			= $reponse['valeur'];
			list($this->type) = spip_fetch_array(spip_query('SELECT type FROM spip_questions WHERE id_question="'.$this->id_question.'"'), SPIP_NUM);
		}


		/**
		 * supprimer
		 *
		 * @return void
		 **/
		function supprimer() {
			if ($this->type == 'fichier') {
				$res = spip_query('SELECT * FROM spip_documents WHERE id_document="'.$this->valeur.'"');
				while ($arr = spip_fetch_array($res)) {
					unlink($arr['fichier']);
					spip_query('DELETE FROM spip_documents WHERE id_document="'.$arr['id_document'].'"');
					spip_query('DELETE FROM spip_documents_applications WHERE id_document="'.$arr['id_document'].'"');
				}
			}
			spip_query('DELETE FROM spip_reponses WHERE id_reponse="'.$this->id_reponse.'"');
		}


	}


	/**
	 * invitation
	 *
	 * @copyright 2006-2007 Artégo
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
			$recherche = spip_query('SELECT id_applicant FROM spip_applicants WHERE email="'.$email.'"');
			if (spip_num_rows($recherche) == 0) { // pas d'email comme ça : on crée un applicant
				$applicant = new applicant();
				$id_applicant = $applicant->id_applicant;
				$applicant->email = $email;
				$applicant->enregistrer();
			} else {
				list($id_applicant) = spip_fetch_array($recherche, SPIP_NUM);
			}
			$this->application = new application($id_applicant, $id_formulaire);
			if (!$this->application->existe)
				$this->application->creer_invitation();
		}


	}


?>