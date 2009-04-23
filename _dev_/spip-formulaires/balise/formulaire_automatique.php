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


	/**
	 * balise_FORMULAIRE_AUTOMATIQUE
	 *
	 * @param p est un objet SPIP
	 * @return string formulaire
	 * @author Pierre Basson
	 **/
	function balise_FORMULAIRE_AUTOMATIQUE($p) {
		return calculer_balise_dynamique($p,'FORMULAIRE_AUTOMATIQUE', array('id_formulaire'));
	}


	/**
	 * balise_FORMULAIRE_AUTOMATIQUE_stat
	 *
	 * @param array args
	 * @param array filtres
	 * @return array args 
	 * @author Pierre Basson
	 **/
	function balise_FORMULAIRE_AUTOMATIQUE_stat($args, $filtres) {

		// Pas d'id_formulaire ? Erreur de squelette
		if (!$args[0])
			return erreur_squelette(_T('zbug_champ_hors_motif', array ('champ' => '#FORMULAIRE_AUTOMATIQUE', 'motif' => 'FORMULAIRES')), '');

		$formulaire = new formulaire($args[0]);

		if ($formulaire->statut == 'hors_ligne')
			return '';

		return $args;
	}


	/**
	 * balise_FORMULAIRE_AUTOMATIQUE_dyn
	 *
	 * Calcule la balise #FORMULAIRE_AUTOMATIQUE
	 *
	 * @return array
	 * @author Pierre Basson
	 **/
	function balise_FORMULAIRE_AUTOMATIQUE_dyn($id_formulaire) {
		$premiere_fois = false;

		$formulaire = new formulaire($id_formulaire);
		$lang = $formulaire->lang;

		if (!empty($_COOKIE['spip_formulaires_mcrypt_iv']) and !empty($_COOKIE['spip_formulaires_id_applicant'])) {
			$id_applicant = formulaires_identifier_applicant();
			$applicant = new applicant($id_applicant);
			if ($applicant->existe) {
				$application = new application($applicant->id_applicant, $id_formulaire);
				if ($application->existe) {
					$premiere_fois = false;
				} else {
					$premiere_fois = true;
				}
			} else { // applicant n'existant pas
				$applicant->supprimer_cookies; // on lui supprime ses cookies erronés
				$premiere_fois = true;
			}
		} else { // pas de cookie détecté : premiere fois
			$premiere_fois = true;
		}

		if ($premiere_fois and $formulaire->limiter_invitation == 'oui') {
			include_spip('balise/formulaire_login_formulaire');
			return balise_FORMULAIRE_LOGIN_FORMULAIRE_dyn();
		}

		switch ($formulaire->type) {
			case 'une_seule_page':
				$resultat = balise_FORMULAIRE_AUTOMATIQUE_UNE_SEULE_PAGE_dyn($formulaire, $application, $lang, $premiere_fois);
				break;
			case 'plusieurs_pages':
				$resultat = balise_FORMULAIRE_AUTOMATIQUE_PLUSIEURS_PAGES_dyn($formulaire, $application, $lang, $premiere_fois);
				break;
		}
		return $resultat;
	}


	/**
	 * balise_FORMULAIRE_AUTOMATIQUE_UNE_SEULE_PAGE_dyn
	 *
	 * @param objet formulaire
	 * @param objet application
	 * @param string lang
	 * @param boolean premiere_fois
	 * @return array
	 * @author Pierre Basson
	 **/
	function balise_FORMULAIRE_AUTOMATIQUE_UNE_SEULE_PAGE_dyn($formulaire, $application, $lang, $premiere_fois) {
		if ($premiere_fois) { // formulaire vide
			return	inclure_balise_dynamique(
						array(
							'formulaires/formulaire_automatique_une_seule_page',
							0,
							array(
								'id_formulaire'	=> $formulaire->id_formulaire,
								'erreurs'		=> array(),
								'lang'			=> $lang
							)
						),
						false
					);
		} else { // une application existe, validation bloc par bloc
			if ($formulaire->limiter_invitation == 'oui' and $application->est_vide()) { // invitation
				$erreurs = array();
			} else {
				$id_dernier_bloc = $application->formulaire->recuperer_dernier_bloc();
				// on regarde si on a toutes les réponses aux questions obligatoires jusqu'au dernier bloc
				$tableau = $application->valider_bloc_par_bloc_jusquau_bloc($id_dernier_bloc, true);
				$resultat_bon	= $tableau['resultat_bon'];
				$id_bloc_erreur	= $tableau['id_bloc_erreur'];
				$erreurs		= $tableau['erreurs'];
				if ($resultat_bon) {
					$erreurs = array();
					$id_bloc = -1;
					if ($_GET['resultat'] != 'oui')
						$id_bloc = $formulaire->recuperer_premier_bloc();
				} else {
					$id_bloc = $id_bloc_erreur;
				}
			}
			if ($id_bloc == -1) {
				$application->changer_statut('valide'); // envoi les notifications etc...
				return	inclure_balise_dynamique(
							array(
								'formulaires/formulaire_automatique_merci',
								0,
								array(
									'id_application'	=> $application->id_application,
									'lang'				=> $lang
								)
							),
							false
						);
			} else {
				return	inclure_balise_dynamique(
							array(
								'formulaires/formulaire_automatique_une_seule_page',
								0,
								array(
									'id_application'	=> $application->id_application,
									'id_formulaire'		=> $application->formulaire->id_formulaire,
									'erreurs'			=> $erreurs,
									'lang'				=> $lang
								)
							),
							false
						);
			}
		}
	}
	

	/**
	 * balise_FORMULAIRE_AUTOMATIQUE_PLUSIEURS_PAGES_dyn
	 *
	 * @param objet formulaire
	 * @param objet application
	 * @param string lang
	 * @param boolean premiere_fois
	 * @return array
	 * @author Pierre Basson
	 **/
	function balise_FORMULAIRE_AUTOMATIQUE_PLUSIEURS_PAGES_dyn($formulaire, $application, $lang, $premiere_fois) {
		if ($premiere_fois) { // formulaire vide
			$erreurs = array();
			$id_bloc = intval(_request('id_bloc'));
			if ($id_bloc) { // erreur de toutes façons
				$id_premier_bloc = $formulaire->recuperer_premier_bloc();
				if ($id_bloc != $id_premier_bloc) {
					$bloc = new bloc($formulaire->id_formulaire, $id_premier_bloc);
					$erreurs = $bloc->recuperer_questions_obligatoires();
				}
			}
			return	inclure_balise_dynamique(
						array(
							'formulaires/formulaire_automatique_plusieurs_pages',
							0,
							array(
								'id_formulaire'	=> $formulaire->id_formulaire,
								'id_bloc'		=> $formulaire->recuperer_premier_bloc(),
								'erreurs'		=> $erreurs,
								'lang'			=> $lang
							)
						),
						false
					);
		} else { // une application existe, validation bloc par bloc
			$id_bloc = intval(_request('id_bloc'));
			if (!$id_bloc)
				$id_bloc = $application->formulaire->recuperer_premier_bloc();
			if ($formulaire->limiter_invitation == 'oui' and $application->est_vide()) { // invitation
				$erreurs = array();
				$id_bloc = $formulaire->recuperer_premier_bloc();
			} else {
				// on regarde si on a toutes les réponses aux questions obligatoires jusqu'au bloc en cours id_bloc !! non inclu !!
				$tableau = $application->valider_bloc_par_bloc_jusquau_bloc($id_bloc, false);
				$resultat_bon	= $tableau['resultat_bon'];
				$id_bloc_erreur	= $tableau['id_bloc_erreur'];
				$erreurs		= $tableau['erreurs'];
				if ($resultat_bon) {
					$erreurs = array();
				} else {
					$id_bloc = $id_bloc_erreur;
				}
			}
			if ($id_bloc == -1) {
				$application->changer_statut('valide'); // envoi les notifications etc...
				return	inclure_balise_dynamique(
							array(
								'formulaires/formulaire_automatique_merci',
								0,
								array(
									'id_application'	=> $application->id_application,
									'lang'				=> $lang
								)
							),
							false
						);
			} else {
				return	inclure_balise_dynamique(
							array(
								'formulaires/formulaire_automatique_plusieurs_pages',
								0,
								array(
									'id_application'	=> $application->id_application,
									'id_formulaire'		=> $application->formulaire->id_formulaire,
									'id_bloc'			=> $id_bloc,
									'erreurs'			=> $erreurs,
									'lang'				=> $lang
								)
							),
							false
						);
			}
		}
	}
	

?>