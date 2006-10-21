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
//
// si id_auteur est hors table, c'est une creation sinon une modif
//
	  $auteur = array();
	  $auteur_supp = array();
	  if ($id_auteur) {
		$auteur = spip_fetch_array(spip_query("SELECT * FROM spip_auteurs WHERE id_auteur=$id_auteur"));
		$auteur_supp = spip_fetch_array(spip_query("SELECT * FROM spip_auteurs_supp WHERE id_auteur=$id_auteur"));
	  }

	  $acces = ($id_auteur == $auteur_session['id_auteur']) ? true : " a voir ";

	// variables sans probleme
	$auteur['nom'] = corriger_caracteres($nom);
	$auteur_supp['organisation'] = corriger_caracteres($organisation);
	$auteur_supp['telephone'] = corriger_caracteres($telephone);
	$auteur_supp['fax'] = corriger_caracteres($fax);
	$auteur_supp['adresse'] = corriger_caracteres($adresse);
	$auteur_supp['codepostal'] = corriger_caracteres($codepostal);
	$auteur_supp['ville'] = corriger_caracteres($ville);
	$auteur_supp['skype'] = corriger_caracteres($skype);
	$auteur_supp['pays'] = corriger_caracteres($pays);
	$auteur_supp['latitude'] = corriger_caracteres($latitude);
	$auteur_supp['longitude'] = corriger_caracteres($longitude);

		$n = spip_query("UPDATE spip_auteurs_supp SET
		nom=" . spip_abstract_quote($auteur['nom']) . ",
		organisation=" . spip_abstract_quote($auteur_supp['organisation']) . ",
		telephone=" . spip_abstract_quote($auteur_supp['telephone']) . ",
		fax=" . spip_abstract_quote($auteur_supp['fax']) . ",
		skype=" . spip_abstract_quote($auteur_supp['skype']) . ",
		adresse=" . spip_abstract_quote($auteur_supp['adresse']) . ",
		codepostal=" . spip_abstract_quote($auteur_supp['codepostal']) . ",
		ville=" . spip_abstract_quote($auteur_supp['ville']) . ",
		pays=" . spip_abstract_quote($auteur_supp['pays']) . ",
		latitude=" . spip_abstract_quote($auteur_supp['latitude']) . ",
		longitude=" . spip_abstract_quote($auteur_supp['longitude']) . " WHERE id_auteur=".$auteur['id_auteur']);
		if (!$n) die('UPDATE');
	}

// Si on modifie la fiche auteur, reindexer
	if ($nom OR $statut) {
		if ($GLOBALS['meta']['activer_moteur'] == 'oui') {
			include_spip("inc/indexation");
			marquer_indexer('spip_auteurs_supp', $id_auteur);
		}
	// ..et mettre a jour les fichiers .htpasswd et .htpasswd-admin
		ecrire_acces();
	}

	if ($echec) $echec = '&echec=' . join('@@@', $echec);

	// il faudrait rajouter OR $echec mais il y a conflit avec Ajax

	if (($init = ($tout[0]=='0'))) {
	  // tout nouveau. envoyer le formulaire de saisie du reste
	  // en transmettant le retour eventuel
	  // decode / encode car encode pas necessairement deja fait.

		$ret = !$redirect ? '' 
		  : ('&redirect=' . rawurlencode(rawurldecode($redirect)));

		$redirect = generer_url_ecrire("auteur_infos_supp", "id_auteur=$id_auteur&initial=$init$echec$ret",true);
	} else {
	  // modif: renvoyer le resultat ou a nouveau le formulaire si erreur
		  if (!$redirect) {
		    $redirect = generer_url_ecrire("auteur_infos_supp", "id_auteur=$id_auteur", true, true);
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