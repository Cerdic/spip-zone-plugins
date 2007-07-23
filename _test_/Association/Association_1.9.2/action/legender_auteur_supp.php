<?php

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/filtres');
include_spip('inc/acces');
include_spip('inc/actions');
include_spip('base/abstract_sql');

// http://doc.spip.org/@action_legender_auteur
function action_legender_auteur_supp_dist() {
	$securiser_action = charger_fonction('securiser_action', 'inc');
     $securiser_action();
     $arg = _request('arg');
	$echec = array();

	if (!preg_match(",^(\d+)\D(\d*)(\D(\w*)\D(.*))?$,", $arg, $r)) {
		$r = "action_legender_auteur_supp_dist $arg pas compris";
		spip_log($r);
     } 
	else 	
	redirige_par_entete(action_legender_auteur_supp_post($r));
}

// http://doc.spip.org/@action_legender_post
function action_legender_auteur_supp_post($r){
	global $auteur_session, $id_auteur;
	
	$id_asso=_request('id_asso');
	$nom=_request('nom');
	$prenom=_request('prenom');
	$naissance=_request('naissance');
	$sexe=_request('sexe');
	$rue=_request('rue');
	$ville=_request('ville');
	$cp=_request('cp');
	$telephone=_request('telephone');
	$portable=_request('portable');
	$profession=_request('profession');
	$societe=_request('societe');
	$secteur=_request('secteur');
	$categorie=_request('categorie');
	$fonction=_request('fonction');
	$publication=_request('publication');
	$utilisateur1=_request('utilisateur1');
	$utilisateur2=_request('utilisateur2');	
	$utilisateur3=_request('utilisateur3');	
	$utilisateur4=_request('utilisateur4');	
	$validite=_request('validite');
	$statut=_request('statut');
	$remarques=_request('remarques');
	$redirect = _request('redirect');
	
	list($tout, $id_auteur, $ajouter_id_article,$x,$s, $n) = $r;

	$auteur = array();
	if ($id_auteur) {
		$query=spip_query("SELECT * FROM spip_asso_adherents WHERE id_auteur=$id_auteur");
		$auteur = spip_fetch_array($query);
	}

	$acces = ($id_auteur == $auteur_session['id_auteur']) ? true : " a voir ";
	
// La requete SQL à passer dans la base
	if ($auteur){
		$query = spip_query("UPDATE spip_asso_adherents SET nom="._q($nom).", prenom="._q($prenom).", naissance="._q($naissance).", sexe="._q($sexe).", rue="._q($rue).",  ville="._q($ville).", cp="._q($cp).", telephone="._q($telephone).", portable="._q($portable).", profession="._q($profession).", societe="._q($societe).", secteur="._q($secteur).", categorie="._q($categorie).", fonction="._q($fonction).", publication="._q($publication).", utilisateur1="._q($utilisateur1).", utilisateur2="._q($utilisateur2).", utilisateur3="._q($utilisateur3).", utilisateur4="._q($utilisateur4).", remarques="._q($remarques)." WHERE id_auteur=".$id_auteur);
		if (!$query) die('UPDATE');
	}
	else {
		$query= spip_query("INSERT INTO spip_asso_adherents (id_auteur, nom, prenom, naissance, sexe, rue,  ville, cp, telephone, portable, profession, societe, secteur, categorie, fonction, publication, utilisateur1, utilisateur2, utilisateur3, utilisateur4, statut, remarques, creation) VALUES ( ".$id_auteur.", "._q($nom).", "._q($prenom).", "._q($naissance).", "._q($sexe).", "._q($rue).", "._q($ville).", "._q($cp).", "._q($telephone).", "._q($portable).", "._q($profession).", "._q($societe).", "._q($secteur).", "._q($categorie).", "._q($fonction).", "._q($publication).", "._q($utilisateur1).", "._q($utilisateur2).", "._q($utilisateur3).", "._q($utilisateur4).", 'prospect', "._q($remarques).", NOW() ) ");
		if (!$query) die('INSERT');
		$query= spip_query ("INSERT INTO spip_auteurs_elargis (id_auteur, nom, prenom, spip_listes_format, creation) VALUES (".$id_auteur.", "._q($nom).", "._q($prenom).", 'html', NOW() ) ");
		
		//Inscription spip-listes
		$spip_liste= lire_config('association/spip_liste');
		if ($spip_liste) {
			$query=spip_query("SELECT * FROM spip_auteurs_listes WHERE id_auteur='$id_auteur' ");
			if (!spip_fetch_array($query))  {
				spip_query ("INSERT INTO spip_auteurs_listes (id_auteur, id_liste, format, date_inscription) VALUES ( '$id_auteur', '$spip_liste','html', NOW() ) ");	
			}
		}
	}
// 	return $n;

// Si on modifie les données on lance la reindexation
	if ($nom OR $statut) {
		if ($GLOBALS['meta']['activer_moteur'] == 'oui') {
			include_spip("inc/indexation");
			marquer_indexer('spip_auteurs', $id_auteur);
		}
		ecrire_acces();
	}

	if ($echec) $echec = '&echec=' . join('@@@', $echec);

	// il faudrait rajouter OR $echec mais il y a conflit avec Ajax

	$redirect = generer_url_ecrire("auteur_infos_supp", "id_auteur=$id_auteur", true, true);
	$anc = '';
	$redirect .= '&initial=-1' . $anc;
	return $redirect;
	}

?>