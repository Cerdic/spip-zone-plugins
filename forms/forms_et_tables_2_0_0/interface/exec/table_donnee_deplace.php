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

if (!defined("_ECRIRE_INC_VERSION")) return;

include_spip('inc/presentation');
include_spip('inc/autoriser');
include_spip('inc/forms');  // ajax_retour compatibilite 1.9.1

function exec_table_donnee_deplace_dist()
{
	$id_donnee = _request('id_donnee');
	$id_form = _request('id_form');
	$table_donnee_deplace = charger_fonction('table_donnee_deplace','inc');
	$res = $table_donnee_deplace($id_donnee,$id_form);
	ajax_retour($res);
}

?>