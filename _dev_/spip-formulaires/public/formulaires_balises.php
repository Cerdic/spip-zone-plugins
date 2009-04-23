<?php


	/**
	 * SPIP-Formulaires
	 *
	 * @copyright 2006-2007 Artégo
	 **/


	include_spip('formulaires_fonctions');


	/**
	 * balise_URL_FORMULAIRE
	 *
	 * @param  objet p contexte spip
	 * @return  string url du formulaire
	 * @author  Pierre Basson
	 */
	function balise_URL_FORMULAIRE($p) {
		$_id_formulaire = champ_sql('id_formulaire', $p);
		$p->code = "generer_url_formulaire($_id_formulaire)";
		$p->interdire_scripts = false;
		return $p;
	}



	/**
	 * balise_URL_BLOC
	 *
	 * @param  objet p contexte spip
	 * @return  string url du bloc
	 * @author  Pierre Basson
	 */
	function balise_URL_BLOC($p) {
		$_id_formulaire = champ_sql('id_formulaire', $p);
		$_id_bloc = champ_sql('id_bloc', $p);
		$p->code = "parametre_url(generer_url_formulaire($_id_formulaire),'id_bloc',$_id_bloc,'&')";
		$p->interdire_scripts = false;
		return $p;
	}


	/**
	 * balise_URL_ACTION_LOGIN_FORMULAIRE
	 *
	 * affiche l'url de l'action login sur spip-formulaires
	 *
	 * @param  objet p contexte spip
	 * @return  string url vers action login
	 * @author  Pierre Basson
	 */
	function balise_URL_ACTION_LOGIN_FORMULAIRE($p) {
		$_lang = champ_sql('lang', $p);
		if (!$_lang) {
		 	$_lang = $GLOBALS['spip_lang'];
		}
		$p->code = "generer_url_action('login_formulaire', 'lang='.$_lang, true)";
		$p->interdire_scripts = false;
		return $p;
	}


	/**
	 * balise_URL_ACTION_LOGOUT_FORMULAIRE
	 *
	 * affiche l'url de l'action logout sur spip-formulaires
	 *
	 * @param  objet p contexte spip
	 * @return  string url vers action login
	 * @author  Pierre Basson
	 */
	function balise_URL_ACTION_LOGOUT_FORMULAIRE($p) {
		$_lang = champ_sql('lang', $p);
		if (!$_lang) {
		 	$_lang = $GLOBALS['spip_lang'];
		}
		$p->code = "generer_url_action('logout_formulaire', 'lang='.$_lang, true)";
		$p->interdire_scripts = false;
		return $p;
	}


	/**
	 * balise_URL_FORMULAIRE_OUBLI_FORMULAIRE
	 *
	 * @param  objet p contexte spip
	 * @return  string url du formulaire pour récupérer son mdp par email
	 * @author  Pierre Basson
	 */
	function balise_URL_FORMULAIRE_OUBLI_FORMULAIRE($p) {
		$_lang = champ_sql('lang', $p);
		if (!$_lang) {
		 	$_lang = $GLOBALS['spip_lang'];
		}
		$p->code = "generer_url_public(\$GLOBALS['meta']['spip_formulaires_fond_formulaire_oubli_formulaire'], ($_lang ? 'lang='.$_lang : ''), true)";
		$p->interdire_scripts = false;
		return $p;
	}


	/**
	 * balise_EMAIL_APPLICANT
	 *
	 * @param  objet p contexte spip
	 * @return  string email applicant
	 * @author  Pierre Basson
	 */
	function balise_EMAIL_APPLICANT($p) {
		$p->code = "calculer_EMAIL_APPLICANT()";
		$p->interdire_scripts = false;
		return $p;
	}


	/**
	 * calculer_EMAIL_APPLICANT
	 *
	 * @return  string email applicant
	 * @author  Pierre Basson
	 */
	function calculer_EMAIL_APPLICANT() {
		if (!empty($_COOKIE['spip_formulaires_mcrypt_iv']) and !empty($_COOKIE['spip_formulaires_id_applicant'])) {
			$id_applicant = formulaires_identifier_applicant();
			$applicant = new applicant($id_applicant);
			return $applicant->email;
		}
		return '';
	}


	/**
	 * balise_NOM_APPLICANT
	 *
	 * @param  objet p contexte spip
	 * @return  string nom applicant
	 * @author  Pierre Basson
	 */
	function balise_NOM_APPLICANT($p) {
		$p->code = "calculer_NOM_APPLICANT()";
		$p->interdire_scripts = false;
		return $p;
	}


	/**
	 * calculer_NOM_APPLICANT
	 *
	 * @return  string nom applicant
	 * @author  Pierre Basson
	 */
	function calculer_NOM_APPLICANT() {
		if (!empty($_COOKIE['spip_formulaires_mcrypt_iv']) and !empty($_COOKIE['spip_formulaires_id_applicant'])) {
			$id_applicant = formulaires_identifier_applicant();
			$applicant = new applicant($id_applicant);
			return $applicant->nom;
		}
		return '';
	}


	/**
	 * balise_REPONSE
	 *
	 * @param  objet p contexte spip
	 * @return  string valeur de la réponse
	 * @author  Pierre Basson
	 */
	function balise_REPONSE($p) {
		$_id_application = champ_sql('id_application', $p);
		$_id_question = champ_sql('id_question', $p);
		$p->code = "calculer_REPONSE($_id_application,$_id_question)";
		$p->interdire_scripts = false;
		return $p;
	}


	/**
	 * calculer_REPONSE
	 *
	 * @param  int id_application
	 * @param  int id_question
	 * @return  string valeur
	 * @author  Pierre Basson
	 */
	function calculer_REPONSE($id_application, $id_question) {
		if (intval($id_application) == 0)
			return '';
		$valeur = sql_getfetsel('valeur', 'spip_reponses', 'id_application='.intval($id_application).' AND id_question='.intval($id_question));
		return $valeur;
	}


	/**
	 * balise_REPONSES
	 *
	 * @param  objet p contexte spip
	 * @return  array valeurs
	 * @author  Pierre Basson
	 */
	function balise_REPONSES($p) {
		$_id_application = champ_sql('id_application', $p);
		$_id_question = champ_sql('id_question', $p);
		$p->code = "calculer_REPONSES($_id_application,$_id_question)";
		$p->interdire_scripts = false;
		return $p;
	}


	/**
	 * calculer_REPONSES
	 *
	 * @param  int id_application
	 * @param  int id_question
	 * @return  array valeurs
	 * @author  Pierre Basson
	 */
	function calculer_REPONSES($id_application, $id_question) {
		$valeurs = array();
		if (intval($id_application) == 0)
			return $valeurs;
		$res = sql_select('valeur', 'spip_reponses', 'id_application='.intval($id_application).' AND id_question='.intval($id_question));
		while ($arr = sql_fetch($res))
			$valeurs[] = $arr['valeur'];
		return $valeurs;
	}


	/**
	 * balise_ABONNEMENTS
	 *
	 * @param  objet p contexte spip
	 * @return  array valeurs
	 * @author  Pierre Basson
	 */
	function balise_ABONNEMENTS($p) {
		$_id_application = champ_sql('id_application', $p);
		$p->code = "calculer_ABONNEMENTS($_id_application)";
		$p->interdire_scripts = false;
		return $p;
	}


	/**
	 * calculer_ABONNEMENTS
	 *
	 * @param  int id_application
	 * @return  array valeurs
	 * @author  Pierre Basson
	 */
	function calculer_ABONNEMENTS($id_application) {
		$valeurs = array();
		$email_applicant = sql_getfetsel('A.email', 'spip_applicants AS A INNER JOIN spip_applications AS APP ON APP.id_applicant=A.id_applicant', 'APP.id_application='.intval($id_application));
		$abonne = new abonne(0, $email_applicant);
		if ($abonne->existe) {
			$valeurs = $abonne->recuperer_abonnements();
		}
		return $valeurs;
	}


?>