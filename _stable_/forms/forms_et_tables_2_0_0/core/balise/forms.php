<?php
/*
 * forms
 * Gestion de formulaires editables dynamiques
 *
 * Auteurs :
 * Antoine Pitrou
 * Cedric Morin
 * Renato Formato
 * (c) 2005-2009 - Distribue sous licence GNU/GPL
 *
 */

if (!defined("_ECRIRE_INC_VERSION")) return;	#securite

// Pas besoin de contexte de compilation
global $balise_FORMS_collecte;
$balise_FORMS_collecte = array('id_form','id_article','id_donnee', 'id_donnee_liee');

function balise_FORMS ($p) {
	$p->descr['session'] = true;

	return calculer_balise_dynamique($p,'FORMS', array('id_form', 'id_article', 'id_donnee','id_donnee_liee', 'class'));
}

function balise_FORMS_stat($args, $filtres) {
	return $args;
}

?>