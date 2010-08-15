<?php


if (!defined("_ECRIRE_INC_VERSION")) return;	#securite
include_spip('inc/spipbb_common');
include_spip('inc/traiter_imagerie');
include_spip('inc/filtres');
spipbb_log('included',2,__FILE__);

function balise_FORMULAIRE_SPIPBB_PROFIL ($p) {
	return calculer_balise_dynamique($p,'FORMULAIRE_SPIPBB_PROFIL', array('id_auteur'));
}

function balise_FORMULAIRE_SPIPBB_PROFIL_stat($args, $filtres) {
	// Pas d'id_auteur ? Erreur de squelette
	if (!$args[0])
		return erreur_squelette(
			_T('zbug_champ_hors_motif',
				array ('champ' => '#FORMULAIRE_SPIPBB_PROFIL',
					'motif' => 'AUTEURS')), '');
	return ($args);
}

function balise_FORMULAIRE_SPIPBB_PROFIL_dyn($id_auteur) {
	$statut=$GLOBALS["auteur_session"]["statut"];
	$id_auteur_session=$GLOBALS["auteur_session"]['id_auteur'];

	# detail infos sur auteur
	$echec=""; #initialisation

	# chps spip passer dans le formulaire
	$new_pass = _request('new_pass');
	$new_pass2 = _request('new_pass2');
	$auteur_bio = corriger_caracteres(_request('bio'));
	$auteur_pgp = corriger_caracteres(_request('pgp'));
	$auteur_nom_site = corriger_caracteres(_request('nom_site')); //h.?? attention mix avec $nom_site_spip ;(
	$auteur_url_site = vider_url(_request('url_site'));
	//$auteur_email = _request('email'); On ne change pas l'email... seuls les admins le peuvent (dans l'interface privée)
	$nouveau = _request('nouveau'); // nouveau == 1 si date_crea_spipbb est vide

	$traiter_chps=array(); // c: 21/12/7 Bug report BB du 2
	$renvois_chps=array(); // c: 23/12/7 Bug report Jack sur gmane


	}
?>