<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2006                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined("_ECRIRE_INC_VERSION")) return;
include_spip('inc/filtres');
include_spip('inc/acces');
include_spip('inc/actions');
include_spip('base/abstract_sql');

// http://doc.spip.org/@action_legender_auteur
function action_legender_auteur_supp_dist()
{
	$securiser_action = charger_fonction('securiser_action', 'inc');
        $securiser_action();

        $arg = _request('arg');

	$echec = array();

	if (!preg_match(",^(\d+)\D(\d*)(\D(\w*)\D(.*))?$,", $arg, $r)) {
		$r = "action_legender_auteur_supp_dist $arg pas compris";
		spip_log($r);
        } else 	redirige_par_entete(action_legender_auteur_supp_post($r));
}

// http://doc.spip.org/@action_legender_post
function action_legender_auteur_supp_post($r){
	global $auteur_session, $id_auteur;

	$nom_table = "spip_auteurs_ajouts";
	$prenom = _request('prenom');
	$nom_famille = _request('nom_famille');
	$organisation = _request('organisation');
	$url_organisation = _request('url_organisation');
	$telephone = _request('telephone');
	$fax = _request('fax');
	$skype = _request('skype');
	$adresse = _request('adresse');
	$codepostal = _request('codepostal');
	$ville = _request('ville');
	$pays = _request('pays');
	$latitude = _request('latitude');
	$longitude = _request('longitude');
// 	$id_auteur = _request('arg');
	$redirect = _request('redirect');
	$statut = _request('statut');

	list($tout, $id_auteur, $ajouter_id_article,$x,$s, $n) = $r;

	$auteur = array();
	if ($id_auteur) {
		$auteur = spip_fetch_array(spip_query("SELECT * FROM ".$nom_table." WHERE id_auteur=$id_auteur"));
	}

	 $acces = ($id_auteur == $auteur_session['id_auteur']) ? true : " a voir ";

// Récupération des variables nécessaires...
	$auteur['id_auteur'] = corriger_caracteres($id_auteur);
	$auteur['nom_famille'] = corriger_caracteres($nom_famille);
	$auteur['prenom'] = corriger_caracteres($prenom);
	$auteur['organisation'] = corriger_caracteres($organisation);
	$auteur['url_organisation'] = vider_url($url_organisation, false);
	$auteur['telephone'] = corriger_caracteres($telephone);
	$auteur['fax'] = corriger_caracteres($fax);
	$auteur['adresse'] = corriger_caracteres($adresse);
	$auteur['codepostal'] = corriger_caracteres($codepostal);
	$auteur['ville'] = corriger_caracteres($ville);
	$auteur['skype'] = corriger_caracteres($skype);
	$auteur['pays'] = corriger_caracteres($pays);
	$auteur['latitude'] = corriger_caracteres($latitude);
	$auteur['longitude'] = corriger_caracteres($longitude);

// La requete SQL à passer dans la base
	$n = spip_query("UPDATE ".$nom_table." SET nom_famille=" . _q($auteur['nom_famille']) . ", prenom=" . _q($auteur['prenom']) . ", organisation=" . _q($auteur['organisation']) . ", url_organisation=" . _q($auteur['url_organisation']) . ", telephone=" . _q($auteur['telephone']) . ", fax=" . _q($auteur['fax']) . ", skype=" . _q($auteur['skype']) . ", adresse=" . _q($auteur['adresse']) . ", codepostal=" . _q($auteur['codepostal']) . ", ville=" . _q($auteur['ville']) . ", pays=" . _q($auteur['pays']) . ", latitude=" . _q($auteur['latitude']) . ", longitude=" . _q($auteur['longitude']) . " WHERE id_auteur=".$auteur['id_auteur']);
		if (!$n) die('UPDATE');
// 	return $n;

// Si on modifie les données on lance la reindexation
	if ($nom OR $statut) {
		if ($GLOBALS['meta']['activer_moteur'] == 'oui') {
			include_spip("inc/indexation");
			marquer_indexer($nom_table, $id_auteur);
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
