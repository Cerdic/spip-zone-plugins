<?php
######################################################################
# RECHERCHE MULTI-CRITERES                                           #
# Auteur: Dominique (Dom) Lepaisant - Octobre 2007                   #
# Adaptation de la contrib de Paul Sanchez - Netdeveloppeur          #
# http://www.netdeveloppeur.com                                      #
# Ce programme est un logiciel libre distribue sous licence GNU/GPL. #
# Pour plus de details voir le fichier COPYING.txt                   #
######################################################################

function action_rec_mc_ecrireconf() {
	global $_POST;
	global $redirect;
	global $action, $arg, $hash, $id_auteur;
	# arg => rien !
	
	// controle action
	include_spip('inc/securiser_action');
	if (!verifier_action_auteur("$action $arg", $hash, $id_auteur)) {
		include_spip('inc/minipres');
		minipres(_T('info_acces_interdit'));
	}
if (($cf=_request('submit'))!==NULL ) {
	$nc=_request('nbcol');
	$tp=_request('taille');
	$cp=_request('coul');
	$cb=_request('bord');
		$sql="UPDATE spip_rmc_rubs_groupes_conf SET colonnes = $nc, taille_police = $tp, couleur_police = '$cp', couleur_bordure = '$cb'";
		$result=spip_query($sql);
	}
	
if (($cfrub=_request('submit2'))!==NULL ) {
	$nc_rub=_request('nbcol_rub');
	$tp_rub=_request('taille_rub');
	$cp_rub=_request('coul_rub');
	$cb_rub=_request('bord_rub');
		$sql="UPDATE spip_rmc_rubs_groupes_conf SET colonnes_rub = $nc_rub, taille_police_rub = $tp_rub, couleur_police_rub = '$cp_rub', couleur_bordure_rub = '$cb_rub'";
		$result=spip_query($sql);
	}
	
	redirige_par_entete(rawurldecode($redirect));	
}
?>
