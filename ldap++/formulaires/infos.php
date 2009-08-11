<?php

function formulaires_infos_charger_dist() {
	$GLOBALS['liste_chp_auteur'] = array('nom', 'bio', 'email', 'nom_site', 'url_site', 'login', 'pgp');
	$GLOBALS['liste_chp_auteur_elargi'] = lister_champs_auteurs_elargis();
	$GLOBALS['liste_chp_ldap'] = lister_champs_ldap();	
	unset($GLOBALS['liste_chp_auteur_elargi']['id_auteur']);
	return array();
}


function formulaires_infos_verifier_dist() {
	$retour = array();
	$champs_auteur = _request('chp_aut');
	if($champs_auteur[5] == '') {
		$retour['message_erreur'] = "Vous devez obligatoirement remplir le champ login";
		spip_log('erreur', 'groupes');
		spip_log($champs_auteur, 'groupes');
	}
	if(count($retour)==0) {
		spip_log('verif ok', 'groupes');
	}
	return $retour;
}

function formulaires_infos_traiter_dist() {
	include_spip('ldaplus_fonctions');
	$i = 0;
	$chp_aut = _request('chp_aut');
	$champs_auteurs;
	
	effacer_meta('ldaplus_chp_auteur');
	effacer_meta('ldaplus_chp_elargis');
	
	foreach(array('nom', 'bio', 'email', 'nom_site', 'url_site', 'login', 'pgp') as $k=>$v) {
		$champs_auteurs[$v] = $chp_aut[$i];
		$i++;
	}
	ecrire_meta('ldaplus_chp_auteur', serialize($champs_auteurs));
	
	if(defined('_DIR_PLUGIN_INSCRIPTION2')) {
		$chp_elargis = _request('chp_elargis');
		$champs_elargis;
		$i = 0;
		foreach(lister_champs_auteurs_elargis() as $k=>$v) {
			$champs_elargis[$k] = $chp_elargis[$i];
			$i++;
		}
		ecrire_meta('ldaplus_chp_elargis', serialize($champs_elargis));
	}
	ecrire_metas();
}
?>