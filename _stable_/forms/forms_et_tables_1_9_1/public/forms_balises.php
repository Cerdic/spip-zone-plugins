<?php

/*
 * forms
 * Gestion de formulaires editables dynamiques
 *
 * Auteurs :
 * Antoine Pitrou
 * Cedric Morin
 * Renato
 * © 2005,2006 - Distribue sous licence GNU/GPL
 *
 */
	
	function balise_RESULTATS_SONDAGE($p) {
		$_id_form = champ_sql('id_form', $p);
	
		$p->code = "Forms_afficher_reponses_sondage(" . $_id_form . ")";
		$p->statut = 'html';
		return $p;
	}

	// #VALEUR
	function balise_VALEUR_dist ($p) {
		$_valeur = champ_sql('valeur', $p);  // facultatif
		if (!$p->etoile){
			$type = $p->type_requete;
			$_id_donnee = champ_sql('id_donnee', $p); // indispensable
			$_champ = champ_sql('champ', $p);  // indispensable
			$_id_form = champ_sql('id_form', $p); // facultatif
			$p->code = "forms_calcule_valeur_en_clair('$type', $_id_donnee, $_champ, $_valeur, $_id_form)";
		}
		else $p->code = $_valeur;
		if (!$p->etoile=='**')
			$p->interdire_scripts = false;
		return $p;
	}
	
?>