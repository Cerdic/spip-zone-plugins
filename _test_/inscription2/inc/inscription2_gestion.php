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
include_spip('inc/presentation');
include_spip('inc/acces');
include_spip('inc/action');

// http://doc.spip.org/@exec_legender_auteur_dist
function inscription2_ajouts()
{
	global $connect_id_auteur, $spip_display;

	$id_auteur = intval(_request('id_auteur'));
	$nom_table = "spip_auteurs_elargis";
	$redirect = _request('redirect');
	$echec = _request('echec');
	$new = _request('new');

	$s = spip_query("SELECT * FROM ".$nom_table." WHERE id_auteur=$id_auteur");
	$auteur = spip_fetch_array($s);

	if (!$auteur AND !$new) {
		spip_abstract_insert($nom_table, "(id_auteur)", "($id_auteur)");
		$s = spip_query("SELECT * FROM ".$nom_table." WHERE id_auteur=$id_auteur");
		$auteur = spip_fetch_array($s);
	}

	$legender_auteur_supp = charger_fonction('legender_auteur_supp', 'inc');
	$fiche = $legender_auteur_supp($auteur, $new, $echec, $redirect);
	
	return $fiche;
}
?>