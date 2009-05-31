<?php
/*
 * forms
 * Gestion de formulaires editables dynamiques
 *
 * Auteurs :
 * Antoine Pitrou
 * Cedric Morin
 * Renato
 * (c) 2005-2009 - Distribue sous licence GNU/GPL
 *
 */

	include_spip("inc/forms");

	// Hack crade a cause des limitations du compilateur
	function _forms_afficher_reponses_sondage($id_form) {
		return forms_afficher_reponses_sondage($id_form);
	}

	// http://doc.spip.org/@puce_statut_article
	function forms_puce_statut_donnee($id, $statut, $id_form, $ajax = false) {
		include_spip('inc/instituer_forms_donnee');
		return puce_statut_donnee($id,$statut,$id_form,$ajax);
	}
	
?>