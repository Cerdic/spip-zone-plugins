<?php

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/presentation');
include_spip('inc/acces');

function exec_auteur_infos_supp_dist()
{
	global $id_auteur, $redirect, $echec, $initial,
	  $connect_statut, $connect_toutes_rubriques, $connect_id_auteur;

	$id_auteur = intval($id_auteur);

	$auteur = spip_fetch_array(spip_query("SELECT * FROM spip_auteurs WHERE id_auteur=$id_auteur"));

	$legender_auteur_supp = charger_fonction('legender_auteur_supp', 'inc');
	$legender_auteur_supp = $legender_auteur_supp($id_auteur, $auteur, $initial, $echec, $redirect);

	if (_request('var_ajaxcharset')) ajax_retour($legender_auteur_supp);

	return $legender_auteur_supp;
}
?>