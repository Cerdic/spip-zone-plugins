<?php

	/**
	 * balise_URL_VALIDATION_INSCRIPTION
	 *
	 * @param p est un objet SPIP
	 * @return string url de validation de l'inscription
	 * @author Pierre Basson
	 **/
	function balise_URL_VALIDATION_INSCRIPTION($p) {
		$p->code = "lettres_calculer_URL_VALIDATION('inscription')";
		$p->statut = 'php';
		return $p;
	}


	/**
	 * balise_URL_VALIDATION_DESINSCRIPTION
	 *
	 * @param p est un objet SPIP
	 * @return string url de validation de la désinscription
	 * @author Pierre Basson
	 **/
	function balise_URL_VALIDATION_DESINSCRIPTION($p) {
		$p->code = "lettres_calculer_URL_VALIDATION('desinscription')";
		$p->statut = 'php';
		return $p;
	}


	/**
	 * balise_URL_VALIDATION_CHANGEMENT_FORMAT
	 *
	 * @param p est un objet SPIP
	 * @return string url de validation du changement de format
	 * @author Pierre Basson
	 **/
	function balise_URL_VALIDATION_CHANGEMENT_FORMAT($p) {
		$p->code = "lettres_calculer_URL_VALIDATION('changement_format')";
		$p->statut = 'php';
		return $p;
	}


	/**
	 * balise_URL_FORMULAIRE_LETTRES
	 *
	 * @param p est un objet SPIP
	 * @return string url du formulaire d'inscription aux lettres
	 * @author Pierre Basson
	 **/
	function balise_URL_FORMULAIRE_LETTRES($p) {
		$fond_formulaire_inscription = lire_meta('fond_formulaire_lettre');
		$p->code = "generer_url_public($fond_formulaire_inscription)";
		$p->statut = 'php';
		return $p;
	}



?>