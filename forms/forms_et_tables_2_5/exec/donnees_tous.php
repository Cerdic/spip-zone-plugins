<?php
/*
 * forms
 * Gestion de formulaires editables dynamiques
 *
 * Auteurs :
 * Loïc LE MAO, Sylvain BLANC
 *
 * Antoine Pitrou
 * Cedric Morin
 * Renato
 * © 2005,2006 - Distribue sous licence GNU/GPL
 *
 */

include_spip('inc/forms_tables_affichage');
function exec_donnees_tous(){
	$res = sql_select('type_form',  'spip_forms',  'id_form='.sql_quote(_request('id_form')));
	// $res = spip_query("SELECT type_form FROM spip_forms WHERE id_form="._q(_request('id_form')));
	if (!$row = sql_fetch($res)) die ('erreur formulaire inexistant');
	$type_form = $row['type_form'];
	echo affichage_donnees_tous($type_form?$type_form:'form');
}

?>