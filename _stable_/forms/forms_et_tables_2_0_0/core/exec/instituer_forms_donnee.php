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

function exec_instituer_forms_donnee_dist()
{
	$id_donnee = _request('id_donnee');
	$instituer_forms_donnee = charger_fonction('instituer_forms_donnee','inc');
	$s = spip_query(
	"SELECT id_form,statut FROM spip_forms_donnees WHERE id_donnee="._q($id_donnee));
	$r = spip_fetch_array($s);

	ajax_retour($instituer_forms_donnee($r['id_form'], $id_donnee, $r['statut'], $rang=NULL));
}

?>