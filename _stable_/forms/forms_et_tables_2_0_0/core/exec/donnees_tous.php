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

include_spip('inc/forms_tables_affichage');

function exec_donnees_tous(){
	$res = spip_query("SELECT type_form FROM spip_forms WHERE id_form="._q(_request('id_form')));
	if (!$row = spip_fetch_array($res)) die ('erreur formulaire inexistant');
	$type_form = $row['type_form'];
	echo affichage_donnees_tous($type_form?$type_form:'form');
}

?>