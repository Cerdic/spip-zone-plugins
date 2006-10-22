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
include_spip('inc/actions');
include_spip('inc/acces');
include_spip('base/abstract_sql');

// http://doc.spip.org/@action_legender_auteur
function action_legender_auteur_supp()
{
        $var_f = charger_fonction('controler_action_auteur', 'inc');
        $var_f();

        $arg = _request('arg');

	$echec = array();

        if (!preg_match(",^(\d+)\D(\d*)(\D?)(.*)$,", $arg, $r)) {
		$r = "action_legender_auteur_supp_dist $arg pas compris";
		spip_log($r);
        } else action_legender_post_supp($r);
}

// http://doc.spip.org/@action_legender_post
function action_legender_post_supp($r)
{
	global 
	$nom,
	$auteur_session,
	$organisation,
	$url_organisation,
	$telephone,
	$fax,
	$skype,
	$adresse,
	$codepostal,
	$ville,
	$pays,
	$latitude,
	$longitude,
	$id_auteur,
	$redirect,
	$statut;

	list($tout, $id_auteur, $ajouter_id_article,$s, $n) = $r;

	$auteur = array();


	if ($id_auteur) {
		$auteur = spip_fetch_array(spip_query("SELECT * FROM spip_auteurs WHERE id_auteur=$id_auteur"));
	}

// Récupération des variables nécessaires...
	$auteur['id_auteur'] = corriger_caracteres($id_auteur);
	$auteur['nom'] = corriger_caracteres($nom);
	$auteur['organisation'] = corriger_caracteres($organisation);
	$auteur['url_organisation'] = corriger_caracteres($url_organisation);
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
	$n = spip_query("UPDATE spip_auteurs SET organisation=" . spip_abstract_quote($auteur['organisation']) . ", url_organisation=" . spip_abstract_quote($auteur['url_organisation']) . ", telephone=" . spip_abstract_quote($auteur['telephone']) . ", fax=" . spip_abstract_quote($auteur['fax']) . ", skype=" . spip_abstract_quote($auteur['skype']) . ", adresse=" . spip_abstract_quote($auteur['adresse']) . ", codepostal=" . spip_abstract_quote($auteur['codepostal']) . ", ville=" . spip_abstract_quote($auteur['ville']) . ", pays=" . spip_abstract_quote($auteur['pays']) . ", latitude=" . spip_abstract_quote($auteur['latitude']) . ", longitude=" . spip_abstract_quote($auteur['longitude']) . " WHERE id_auteur=".$auteur['id_auteur']);
		if (!$n) die('UPDATE');
	}
	return $n;

// Si on modifie les données on lance la reindexation
	if ($nom OR $statut) {
		if ($GLOBALS['meta']['activer_moteur'] == 'oui') {
			include_spip("inc/indexation");
			marquer_indexer('spip_auteurs', $id_auteur);
		}
	}

	if ($echec) $echec = '&echec=' . join('@@@', $echec);

	// il faudrait rajouter OR $echec mais il y a conflit avec Ajax

	if (($init = ($tout[0]=='0'))) {
	  // tout nouveau. envoyer le formulaire de saisie du reste
	  // en transmettant le retour eventuel
	  // decode / encode car encode pas necessairement deja fait.

	$ret = !$redirect ? '' 
		  : ('&redirect=' . rawurlencode(rawurldecode($redirect)));

	$redirect = generer_url_ecrire("legender_auteur_supp", "id_auteur=$id_auteur&initial=$init$echec$ret",true);
	} else {
	  // modif: renvoyer le resultat ou a nouveau le formulaire si erreur
		  if (!$redirect) {
		    $redirect = generer_url_ecrire("legender_auteur_supp", "id_auteur=$id_auteur", true, true);
		    $ancre = '';
		  } else 
		    list($redirect,$anc) = split('#',rawurldecode($redirect));

		if (!$echec)
		  $redirect .= '&initial=-1' . $anc;
		else  {
		  $redirect .= $echec . '&initial=0' . $anc;
		}
	}

	redirige_par_entete($redirect);
?>