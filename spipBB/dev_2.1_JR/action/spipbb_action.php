<?php
/*
+-------------------------------------------+
| GAFoSPIP v. 0.5 - 21/08/07 - spip 1.9.2
+-------------------------------------------+
| Gestion Alternative des Forums SPIP
+-------------------------------------------+
| Hugues AROUX - SCOTY @ koakidi.com
+-------------------------------------------+
| les actions !
+-------------------------------------------+
*/


if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/spipbb_common');
spipbb_log('included',2,__FILE__);

# pour 192 ..
# h. --> 193 modif requete ;-) !
//include_spip('inc/spipbb_util');

#
# action generique
#
function action_spipbb_action() {

	$securiser_action = charger_fonction('securiser_action', 'inc');
	$arg = $securiser_action();
	$redirect = rawurldecode(_request('redirect'));

	preg_match('/^(\w+)\W(.*)$/', $arg, $r);
	/* $r = Array 
		0 -> action-id
		1 -> action
		2 -> idforum
	*/
	$var_nom = 'action_spipbb_action_' . $r[1];
	if (function_exists($var_nom)) {
		spipbb_log("action: $var_nom $r[2]",3,"A_a_s_a");
		$var_nom($r[2]);
	}
	else {
		spipbb_log("action: $action: $arg incompris",3,"A_a_s_a");
	}

	redirige_par_entete($redirect);	
} // action_spipbb_action


//
// lier le sujet au mot "annonce"
//
function action_spipbb_action_sujetannonce($arg) {
	$id_mot_annonce=_request('id_mot_annonce');
	$mode=_request('mode');
	$arg = intval($arg); // id_sujet
	spipbb_log("action_spipbb_action_sujetannonce: $id_mot_annonce, $arg, $mode",3,"a_s_a_sua");
	
	if ($mode=="annonce") {
		@sql_insertq("spip_mots_forum",array('id_mot'=> $id_mot_annonce,'id_forum'=>$arg));
	}
	elseif ($mode=="desannonce") {
		@sql_delete("spip_mots_forum","id_mot=$id_mot_annonce AND id_forum=$arg");
	}
}

#
# lier le forum au mot "annonce"
#
function action_spipbb_action_forumannonce($arg) {
	$id_mot_annonce=_request('id_mot_annonce');
	$mode=_request('mode');
	$arg = intval($arg); // id_article

	if ($mode=="annonce") {
		sql_insertq("spip_mots_articles",array('id_mot'=> $id_mot_annonce,'id_article'=>$arg));
	}
	elseif ($mode=="desannonce") {
		sql_delete("spip_mots_articles","id_mot=$id_mot_annonce AND id_article=$arg");
	}
}


//
// traiter  fermer ou liberer article-forum
// faut-il faire apparaitre dans les logs (hash calculer action auteur) ?? ??

function action_spipbb_action_fermelibere($arg) {
	if(!function_exists('verif_article_ferme')) include_spip("inc/spipbb_util");

	$id_mot_ferme=_request('id_mot_ferme');
	$mode=_request('mode');
	$arg = intval($arg); // id_article

	$id_auteur = $GLOBALS['auteur_session']['id_auteur'];
	$deja_ferme = verif_article_ferme($arg, $id_mot_ferme);
	$f_gafart = _DIR_SESSIONS."spipbbart_$arg-$id_auteur.lck";

	if($mode=="ferme" OR $deja_ferme=='') {
		sql_insertq("spip_mots_articles",array('id_mot'=> $id_mot_ferme,'id_article'=>$arg));
	}
	if($mode=="maintenance")
		// pose le verrou de maintenance
		{ spip_touch($f_gafart); }


	if($mode=="libere") {
		sql_delete("spip_mots_articles","id_mot=$id_mot_ferme AND id_article=$arg");
	}
	if($mode=="libere_maintenance")
		// effacer le verrou de maintenance
		{
		if(file_exists($f_gafart))
			unlink($f_gafart);
		}

}

// fermer liberer sujet
function action_spipbb_action_ferlibsujet($arg) {

	#include_spip("inc/spipbb_presentation");

	$id_mot_ferme=_request('id_mot_ferme');
	$mode=_request('mode');
	$arg = intval($arg); // id_sujet

	if($mode=="ferme" OR $deja_ferme=='') {
		sql_insertq("spip_mots_forum",array('id_mot'=> $id_mot_ferme,'id_forum'=> $arg));
	}

	if($mode=="libere") {
		sql_delete("spip_mots_forum","id_mot=$id_mot_ferme AND id_forum=$arg");
	}

}
?>