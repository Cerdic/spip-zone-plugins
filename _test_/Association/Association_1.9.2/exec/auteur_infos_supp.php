<?php

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/presentation');
include_spip('inc/acces');
include_spip('inc/action');

function exec_auteur_infos_supp_dist() {
	global $id_auteur, $redirect, $echec, $initial,
	$connect_statut, $connect_toutes_rubriques, $connect_id_auteur;
	$id_auteur = intval($id_auteur);
	$auteur = spip_fetch_array(spip_query("SELECT * FROM spip_asso_adherents WHERE id_auteur=$id_auteur"));

	if (!$auteur) {
		gros_titre(_T('info_acces_interdit'));
          exit;
     }

	if (!$echec AND $retour) {
		include_spip('inc/headers');
		redirige_par_entete(rawurldecode($retour));
		exit;
	}

	$legender_auteur_supp = charger_fonction('legender_auteur_supp', 'inc');

	return ajax_retour($legender_auteur_supp($id_auteur, $auteur, $initial, $echec, $retour));
}
?>